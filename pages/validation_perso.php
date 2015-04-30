<html>
<?php
	session_start();

	if(empty($_POST['nom']) OR empty($_POST['classe'])) // On v�rifie si le formulaire �tait bien remplit
		{
			header ("Refresh: 5;URL=checkperso.php");
			echo 'Il faut renseigner un nom et une classe !';
		}
	else
		{
			header ("Refresh: 5;URL=../jeux.php");
			$nom = $_POST["nom"];
			$classe = $_POST["classe"];

			// Connexion � la bdd
			$conn = new mysqli("localhost", "root", "", "Game");
			// V�rifie la connexion
			if ($conn->connect_error) 
				{
					die("Echec de la connexion : " . $conn->connect_error); // Affiche un message d'erreur si elle �choue
				} 

			$cptid = "SELECT * FROM compte WHERE pseudo = '".$_SESSION["pseudo"]."'";
			
			$req = $conn->query($cptid) or die('Erreur SQL !');
			
			$data = mysqli_fetch_array($req);
			
			
			// V�rifie la classe choisie
			if($classe == 1)
				{
					// Ajoute une nouvelle ligne dans la table personnage avec les stats de guerrier
					$sql = "INSERT INTO personnage(nom,cptid,classe,niveau,pdv,res,arm,forc,inte,dex) VALUES ('".$nom."','".$data['id']."', '".$classe."',1,20,5,10,15,0,5);"; 
				}
			else
				{
					// Ajoute une nouvelle ligne dans la table personnage avec les stats de mage
					$sql = "INSERT INTO personnage(nom,cptid,classe,niveau,pdv,res,arm,forc,inte,dex) VALUES ('".$nom."','".$data['id']."', '".$classe."',1,15,10,5,0,15,5);"; 
				}

			// V�rifie si l'ajout est un succ�s
			if ($conn->query($sql) === TRUE)
				{
					echo "Votre personnage est op�rationnel ! Entr�e en jeux ...<br/>";
				} 
			else 
				{
					echo $conn->error; // Sinon renvoi un message d'erreur
				}

			// D�connexion de la bdd
			$conn->close();
		}
?>
</html>