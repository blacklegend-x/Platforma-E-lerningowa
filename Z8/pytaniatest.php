<?php declare(strict_types=1); // włączenie typowania zmiennych
session_start();
if (!isset($_SESSION['loggedin']))
{
	header('Location: logowanie.php');
	exit();
}

$link = mysqli_connect(); //polaczenie z baza danych
if(!$link) { echo "Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
$link->query("SET NAMES 'utf8'");

$username = $_SESSION['username'];
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="fonts/fontawesome/css/all.css">
<link rel="stylesheet" href="maincss.css">
<style>
</style>
<script src="https://cdn.ckeditor.com/ckeditor5/35.4.0/classic/ckeditor.js"></script>
</head>
<body>

<div class="content">
	<div class="menu">
	<div class="menuhead" style="font-size:24px;text-align:center;font-weight:bold;">
		<?php
		echo "<br>E-LEARNING<br><br><br>";
		?>
	</div>
	<div class="menuframe">
	<?php
		echo "<a href='logout.php'>" . $username . " - wyloguj</a>";
		
		echo "<br><hr>";
		echo "<a href='mainPage.php'>E-LEARNING: Strona główna</a>";
		
		echo "<br><hr>";
		echo "Dostępne lekcje:";
		$result = mysqli_query($link, "SELECT * FROM lekcje");
		//$rekord = mysqli_fetch_array($result);
		foreach($result as $row){
			$idl = $row['idl'];
			echo "<br><a href='lekcjaview.php?idl=$idl'> " . $row['nazwa'] . "</a>";
		}
		
		echo "<br><hr>";
		echo "Dostępne testy:";
		$result = mysqli_query($link, "SELECT * FROM test");
		foreach($result as $row){
			$idt = $row['idt'];
			echo "<br><a href='testview.php?idt=$idt'> " . $row['nazwa'] . "</a>";
		}
		
		if($_SESSION['uprawnienia'] == 'n'){
			echo "<br><hr>";
			echo "<a href='lekcjaadd.php'>NOWA LEKCJA</a>";
			echo "<br><hr>";
			echo "<a href='testadd.php'>NOWY TEST</a>";
			
			if($_SESSION['username'] == 'admin'){
				echo "<br><hr>";
				echo "<a href='nauczycieladd.php'>DODAJ NAUCZYCIELA</a>";
			}
			
			echo "<br><hr>";
			echo "UŻYTKOWNICY PORTALU";
			echo "<br>Nauczyciele:";
			$result = mysqli_query($link, "SELECT * FROM nauczyciel");
			foreach($result as $row){
				$id = $row['idn'];
				$login = $row['login'];
				if($row['login'] != 'admin'){
					echo "<br><a href='profile.php?id=$id&uprawnienia=n'> " . $login . "</a>";
				}
			}
			echo "<br>Pracownicy:";
			$result = mysqli_query($link, "SELECT * FROM pracownik");
			foreach($result as $row){
				$id = $row['idu'];
				$login = $row['login'];
				echo "<br><a href='profile.php?&id=$id&uprawnienia=p'> " . $login . "</a>";
			}
		}
	?>
	</div>
	</div>
	<div class='main'>
	<div class='mainframe'>
	<?php
		$idt = $_GET['idt'];
		
		echo "PYTANIA TESTU:<br>";
		
		echo "<br><hr>";
		echo "DODAJ PYTANIE:<br>";
		echo "<form method='post' action='pytanieadd.php?idt=$idt' enctype='multipart/form-data'>
			Pytanie:<input type='text' name='tresc_pytania' maxlength='200' size='100'><br>
			Odpowiedź a:<input type='text' name='odp_a' maxlength='200' size='100'><br>
			Odpowiedź b:<input type='text' name='odp_b' maxlength='200' size='100'><br>
			Odpowiedź c:<input type='text' name='odp_c' maxlength='200' size='100'><br>
			Odpowiedź d:<input type='text' name='odp_d' maxlength='200' size='100'><br>
			Poprawna odpowiedź:<input type='text' name='poprawna' maxlength='200' size='100'><br>
			<hr><br>
			
			Plik do pytania: <input type='file' name='file' id='file'><br><br>
			
			<input type='submit' value='Dodaj pytanie'/>
			</form>";
		
		$result = mysqli_query($link, "SELECT * FROM pytania WHERE idt='$idt'");
		foreach($result as $row){
			$idp = $row['idp'];
			echo "<br><hr>";
			echo "Pytanie: " . $row['tresc_pytania'];
			echo "<br>a: " . $row['odpowiedz_a'];
			echo "<br>b: " . $row['odpowiedz_b'];
			echo "<br>c: " . $row['odpowiedz_c'];
			echo "<br>d: " . $row['odpowiedz_d'];
			echo "<br>Poprawna odpowiedź: " . $row['poprawna'];
			
			$file = $row['file_name']; 
			$ext = $row['file_ext']; 
			
			echo "<br>";
			
			$path = "pliki/" . $file;
			if($file != "") //jesli plik istnieje
			{ 
				//echo "<br><hr>";
				if($ext == "mp4"){
					echo "<video controls autoplay muted width='320px' height='240px'><source src='$path' type='video/mp4'></video><br>";
				}
				if($ext == "mp3"){
					echo "<audio controls><source src='$path' type='audio/mpeg'></audio><br>";
				}
				if($ext == "jpg" || $ext == "png" || $ext == "jpeg" || $ext == "gif"){
					echo "<img src='$path'><br>";
				}
				//echo "<br><hr>";
			}
			
			
			echo "<br><a href='pytaniedelete.php?idp=$idp&idt=$idt'>Usuń pytanie</a>";
		}
	?>
	</div>
	</div>
</div>
</body>
</html>