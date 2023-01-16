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

$idt = $_GET['idt'];

//usuniecie pytan zawartych w tescie
$sql = "DELETE FROM pytania WHERE idt=$idt";

mysqli_query($link, $sql);

$sql2 = "DELETE FROM test WHERE idt=$idt";
	
mysqli_query($link, $sql2);
mysqli_close($link);

header('Location: mainPage.php');	
?>