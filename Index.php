<html>
<?php	
	session_start();
?>
	<header>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="./style.css" />
		<title>Rpg</title>
	</header>
	<body>
		<?php	
	if(empty($_SESSION["pseudo"]) OR $_SESSION["pseudo"] == "")
		{			
		?>
		<form method="post" action="./pages/connexion.php">
			<p>
				Connexion<br/><br/>
				<label for="pseudo">Pseudo :</label>
				<input type="text" name="pseudo" /><br /><br/>
				
				<label for="mdp">Mot de passe :</label>
				<input type="password" name="mdp" /><br /><br/>
				
				<input type="submit" value="Envoyer" />
			</p>
		</form>
		<a href="./pages/creation_compte.php" target="_self">Pas encore de compte ? Inscrivez vous !</a>
		<?php
		}
		else
		{
			echo "Vous êtes déjà connecté !";
			?><form method="link" action="jeux.php"> <input type="submit" value="Jouez !"></form>
			<form method="link" action="/pages/deconnexion.php"> <input type="submit" value="Déconnexion"></form><?php
		}
		?>
	</body>
	<footer>

	</footer>
</html>