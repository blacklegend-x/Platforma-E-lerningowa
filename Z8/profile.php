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
				if($row['login'] != 'admin'){
					echo "<br><a href='profile.php?id=$id&uprawnienia=n'> " . $row['login'] . "</a>";
				}
			}
			echo "<br>Pracownicy:";
			$result = mysqli_query($link, "SELECT * FROM pracownik");
			foreach($result as $row){
				$id = $row['idu'];
				echo "<br><a href='profile.php?id=$id&uprawnienia=p'> " . $row['login'] . "</a>";
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
		$id = $_GET['id'];
		$uprawnienia = $_GET['uprawnienia'];
		
		if($uprawnienia == 'p'){ //STRONA PRACOWNIKA
			//pobranie loginu
			$result = mysqli_query($link, "SELECT login FROM pracownik WHERE idu=$id");
			$rekord = mysqli_fetch_array($result);
			$login = $rekord[0];
			
			//pobranie info o zablokowaniu
			$result = mysqli_query($link, "SELECT isblock FROM pracownik WHERE idu=$id");
			$rekord = mysqli_fetch_array($result);
			$isblock = $rekord[0];
			
			//login
			echo "Strona użytkownika - " . $login;
			
			//mozliwosc blokowania lub usuwania - admin
			if($_SESSION['username'] == 'admin'){
				if($isblock == 0) echo "&ensp;<a href='blockscript.php?id=$id&val=0&uprawnienia=$uprawnienia'>Zablokuj użytkownika</a>";
				if($isblock == 1) echo "&ensp;<a href='blockscript.php?id=$id&val=1&uprawnienia=$uprawnienia'>Odblokuj użytkownika</a>";
				echo "&ensp;<a href='deleteuser.php?id=$id&uprawnienia=$uprawnienia'>Usuń użytkownika</a>";
			}
		
			//AKTYWNOSC
			if($_SESSION['uprawnienia'] == 'n'){
				echo "<br><br>AKTYWNOŚĆ: ";
				$aktywnosc = mysqli_query($link, "SELECT * FROM aktywnosc WHERE login='$login'");
				foreach($aktywnosc as $row){
					echo "<br>- " . $row['datetime'] . " " . $row['opis'];
				}

				echo "<br><br>WYNIKI TESTÓW: ";
				$result2 = mysqli_query($link, "SELECT login FROM pracownik WHERE idu='$id'");
				foreach($result2 as $row){
					$login = $row['login'];
				}
				
				$result = mysqli_query($link, "SELECT * FROM wyniki WHERE login='$login'");
				foreach($result as $row){
					echo "<br><hr>";
					$idt = $row['idt'];
					$testquery = mysqli_query($link, "SELECT nazwa FROM test WHERE idt='$idt'");
					$testrekord = mysqli_fetch_array($testquery);
					$nazwa = $testrekord[0];
					$plik = $row['plik_pdf'];
					echo "TEST: " . $nazwa;
					echo "<br>DATA: " . $row['datetime'];
					echo "<br>WYNIK: " . $row['punkty'];
					echo "<br>PLIK: " . "<a href='$plik' target='_blank'>plik pdf</a>";
				}
			}
		}
		else if($uprawnienia == 'n'){ //STRONA NAUCZYCIELA
			//pobranie loginu
			$result = mysqli_query($link, "SELECT login FROM nauczyciel WHERE idn=$id");
			$rekord = mysqli_fetch_array($result);
			$login = $rekord[0];
			
			//pobranie info o zablokowaniu
			$result = mysqli_query($link, "SELECT isblock FROM nauczyciel WHERE idn=$id");
			$rekord = mysqli_fetch_array($result);
			$isblock = $rekord[0];
			
			//login
			echo "Strona użytkownika - " . $login;
			
			//mozliwosc blokowania lub usuwania - admin
			if($_SESSION['username'] == 'admin'){
				if($isblock == 0) echo "&ensp;<a href='blockscript.php?id=$id&val=0&uprawnienia=$uprawnienia'> Zablokuj użytkownika </a>";
				if($isblock == 1) echo "&ensp;<a href='blockscript.php?id=$id&val=1&uprawnienia=$uprawnienia'> Odblokuj użytkownika </a>";
				echo "&ensp;<a href='deleteuser.php?id=$id&uprawnienia=$uprawnienia'>Usuń użytkownika</a>";
			}
		
			//AKTYWNOSC
			if($_SESSION['uprawnienia'] == 'n'){
				echo "<br><br>AKTYWNOŚĆ: ";
				$aktywnosc = mysqli_query($link, "SELECT * FROM aktywnosc WHERE login='$login'");
				foreach($aktywnosc as $row){
					echo "<br>- " . $row['datetime'] . " " . $row['opis'];
				}	
			}
		}
		}
	?>
	</div>
	</div>
</div>
</body>
</html>