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

$id = $_GET['id'];
$val = $_GET['val'];
$uprawnienia = $_GET['uprawnienia'];

if($uprawnienia == 'p'){ //BLOKOWANIE PRACOWNIKA
	if($val == 0){ //zablokuj
		$sql = "UPDATE pracownik SET isblock = 1 WHERE idu='$id'";
	}
	if($val == 1){ //odblokuj
		$sql = "UPDATE pracownik SET isblock = 0 WHERE idu='$id'";
	}
}
if($uprawnienia == 'n'){ //BLOKOWANIE NAUCZYCIELA	
	if($val == 0){ //zablokuj
		$sql = "UPDATE nauczyciel SET isblock = 1 WHERE idn='$id'";
	}
	if($val == 1){ //odblokuj
		$sql = "UPDATE nauczyciel SET isblock = 0 WHERE idn='$id'";
	}
}
	
mysqli_query($link, $sql);
mysqli_close($link);

header('Location: mainPage.php');	
?>