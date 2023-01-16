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
//pobranie info o zablokowaniu
if($_SESSION['uprawnienia'] == 'p') $result = mysqli_query($link, "SELECT isblock FROM pracownik WHERE login='$username'");
if($_SESSION['uprawnienia'] == 'n') $result = mysqli_query($link, "SELECT isblock FROM nauczyciel WHERE login='$username'");
$rekord = mysqli_fetch_array($result);
$isblock = $rekord[0];
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
		if($isblock){
			echo "JESTEŚ ZABLOKOWANY";
		}else{
		$idt = $_GET['idt'];
		
		$result = mysqli_query($link, "SELECT nazwa FROM test WHERE idt='$idt'");
		$rekord = mysqli_fetch_array($result);
		$nazwa = $rekord[0];
	
		echo "TEST: " . $nazwa;
		
		$result = mysqli_query($link, "SELECT idn FROM test WHERE idt='$idt'");
		$rekord = mysqli_fetch_array($result);
		$idn = $rekord[0];
		
		$result = mysqli_query($link, "SELECT login FROM nauczyciel WHERE idn='$idn'");
		$rekord = mysqli_fetch_array($result);
		$login = $rekord[0];
		
		echo "&ensp;Autor: " . $login . "<br>";
		
		//modyfikacja
		if($_SESSION['uprawnienia'] == 'n'){ //jesli nauczyciel
			echo "<br><a href='testmod.php?idt=$idt'>Modyfikuj test</a>";
			echo "<br><a href='testdelete.php?idt=$idt'>Usuń test</a>";
		}
	
		$result = mysqli_query($link, "SELECT opis FROM test WHERE idt='$idt'");
		$rekord = mysqli_fetch_array($result);
		$opis = $rekord[0];
		
		echo "<br><hr>";
		echo "OPIS:<br>" . $opis;
		echo "<br><hr>";
		
		echo "<a href='teststart.php?idt=$idt'>Rozpocznij test</a>";
		}
	?>
	</div>
	</div>
</div>
</body>
</html>