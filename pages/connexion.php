<html>
<?php
if(empty($_POST['pseudo']) OR empty($_POST['mdp'])) 
	{
		header ("Refresh: 5;URL=../Index.php");	
		echo 'Vous avez oublié de remplir un champ.';
	}
else
	{
		header ("Refresh: 5;URL=checkperso.php");	
		$pseudo = $_POST["pseudo"];
		$mdp = md5($_POST["mdp"]);
		// Connexion à la bdd
		$conn = new mysqli("localhost", "root", "", "Game");

		//Verification de la connexion
		if ($conn->connect_error) 
		{
			die("Echec de la connexion : " . $conn->connect_error); // Affiche un message d'erreur si elle échoue
		}
		
		//Récupération du mot de passe
		$sql = "SELECT * FROM compte WHERE pseudo='".$pseudo."'";

		//Vérification de la bonne execution de la requête
		if ($conn->query($sql) === TRUE)
			{
			} 
		else 
			{
				echo $conn->error; // Sinon renvoi un message d'erreur
			}	
			
		$req = $conn->query($sql) or die('Erreur SQL !');
		// Retourne un tableau avec le mdp
		$data = mysqli_fetch_array($req);
		
		// Compare les mdp
		if($data['mdp'] != $mdp) 
			{	
				echo 'Mauvais login / password. Merci de recommencer';
			}
		else 
			{
				// Lancement de la session
				session_start();
				$_SESSION['pseudo'] = $pseudo;
				echo 'Vous êtes bien logué, entrée en jeux.';
			}   
	}
?>
</html>