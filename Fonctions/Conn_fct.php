<?php

include_once 'psl-config.php';
 
function sec_session_start() {
    $session_name = 'sec_session_id';   // Attribue un nom de session
    $secure = SECURE;
    // Cette variable empêche Javascript d’accéder à l’id de session
    $httponly = true;
    // Force la session à n’utiliser que les cookies
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Récupère les paramètres actuels de cookies
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Donne à la session le nom configuré plus haut
    session_name($session_name);
    session_start();            // Démarre la session PHP 
    session_regenerate_id();    // Génère une nouvelle session et efface la précédente
}

function login($email, $password, $mysqli) {
    // L’utilisation de déclarations empêche les injections SQL
    if ($stmt = $mysqli->prepare("SELECT id, username, password, salt 
        FROM members
       WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Lie "$email" aux paramètres.
        $stmt->execute();    // Exécute la déclaration.
        $stmt->store_result();
 
        // Récupère les variables dans le résultat
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();
 
        // Hashe le mot de passe avec le salt unique
        $password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // Si l’utilisateur existe, le script vérifie qu’il n’est pas verrouillé
            // à cause d’essais de connexion trop répétés 
 
            if (checkbrute($user_id, $mysqli) == true) {
                // Le compte est verrouillé 
                // Envoie un email à l’utilisateur l’informant que son compte est verrouillé
                return false;
            } else {
                // Vérifie si les deux mots de passe sont les mêmes
                // Le mot de passe que l’utilisateur a donné.
                if ($db_password == $password) {
                    // Le mot de passe est correct!
                    // Récupère la chaîne user-agent de l’utilisateur
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // Protection XSS car nous pourrions conserver cette valeur
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // Protection XSS car nous pourrions conserver cette valeur
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', 
                              $password . $user_browser);
                    // Ouverture de session réussie.
                    return true;
                } else {
                    // Le mot de passe n’est pas correct
                    // Nous enregistrons cet essai dans la base de données
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            // L’utilisateur n’existe pas.
            return false;
        }
    }
}

function login_check($mysqli) {
    // Vérifie que toutes les variables de session sont mises en place
    if (isset($_SESSION['user_id'], 
                        $_SESSION['username'], 
                        $_SESSION['login_string'])) {
 
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // Récupère la chaîne user-agent de l’utilisateur
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT password 
                                      FROM members 
                                      WHERE id = ? LIMIT 1")) {
            // Lie "$user_id" aux paramètres. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Exécute la déclaration.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // Si l’utilisateur existe, récupère les variables dans le résultat
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if ($login_check == $login_string) {
                    // Connecté!!!! 
                    return true;
                } else {
                    // Pas connecté 
                    return false;
                }
            } else {
                // Pas connecté 
                return false;
            }
        } else {
            // Pas connecté 
            return false;
        }
    } else {
        // Pas connecté 
        return false;
    }
}

function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        // Nous ne voulons que les liens relatifs de $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}



?>