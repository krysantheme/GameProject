<html>
<?php
	$pseudo = $_POST["pseudo"];
	$mdp = md5($_POST["mdp"]);

	// Connexion à la bdd
	$conn = new mysqli("localhost", "root", "", "Game");

	// Vérifie la connexion
	if ($conn->connect_error) 
		{
			header ("Refresh: 5;URL=../Index.php");
			die("Echec de la connexion : " . $conn->connect_error); // Affiche un message d'erreur si elle échoue
		}

	$query = $conn->query("SELECT id FROM compte WHERE pseudo = '".$_POST['pseudo']."'");

	if(empty($_POST['pseudo']) OR empty($_POST['mdp'])) // On vérifie si le formulaire était bien remplit
		{
			header ("Refresh: 5;URL=creation_compte.php");
			echo 'Il faut renseigner le pseudo et le mdp !';
		}
	else if($query ->num_rows==1)
		{
			header ("Refresh: 5;URL=creation_compte.php");
			// Pseudo déjà utilisé
			echo 'Ce pseudo est déjà utilisé';
		}
	else
		{
			header ("Refresh: 5;URL=../Index.php");

			//Création du compte

			//Création de la requête sql
			$sql = "INSERT INTO compte(pseudo,mdp) VALUES ('".$pseudo."', '".$mdp."');"; 

			//Vérification de l'envoi
			if ($conn->query($sql) === TRUE)
				{
					echo "Votre compte est créé !<br/> Retour à l'index dans 5 seconde, veuillez vous connecter ...";
				} 
			else 
				{
					echo $conn->error;
				}

			$conn->close();
		}
?>
</html>