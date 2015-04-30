<html>
<?php
	session_start();
	//Vérifie si un utilisateur est connecté
	if(empty($_SESSION["pseudo"]) OR $_SESSION["pseudo"] == "")
		{	
		  header("Refresh: 5;URL=../Index.php");
		  echo "Vous n'êtes pas connecté";
		}
	else
		{
			$conn = new mysqli("localhost", "root", "", "Game");
			
			//Verification des connexion
			if ($conn->connect_error) 
			{
				header ("Refresh: 5;URL=../Index.php");
				die("Echec de la connexion : " . $conn->connect_error); // Affiche un message d'erreur si elle échoue
			}
			
			$query = $conn->query("SELECT id FROM personnage WHERE cptid = (SELECT id FROM compte WHERE pseudo = '".$_SESSION["pseudo"]."')");
	
			if($query->num_rows==1)
				{
					header ("Refresh: 5;URL=../jeux.php");
					
					echo 'Connexion au jeux';
				}
			else
				{
					echo "Création de votre personnage : ";
					?>
						<form method="post" action="validation_perso.php">
						
							<label for="nom">Nom du personnage :</label>
							<input type="text" name="nom" /><br />

							Classe du personnage :
							<input type="radio" name="classe" value="1" id="guerrier" /> <label for="guerrier">guerrier</label>
							<input type="radio" name="classe" value="2" id="mage" /> <label for="mage">Mage</label><br /><br />		
											
							<input type="submit" value="Envoyer" />
						</form>
					<?php
				}
			
		}
?>
</html>