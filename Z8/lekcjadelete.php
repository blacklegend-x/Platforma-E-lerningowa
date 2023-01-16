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

$idl = $_GET['idl'];

$sql = "DELETE FROM lekcje WHERE idl=$idl";
	
mysqli_query($link, $sql);
mysqli_close($link);

header('Location: mainPage.php');	
?>