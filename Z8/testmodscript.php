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
$result = mysqli_query($link, "SELECT idn FROM nauczyciel WHERE login='$username'");
$rekord = mysqli_fetch_array($result);;

$nazwa = $_POST['nazwa'];
$opis = $_POST['opis'];
$idt = $_GET['idt'];
$idn = $_GET['idn'];

$sql = "UPDATE test SET idn='$idn', nazwa='$nazwa', opis='$opis' WHERE idt='$idt'";
	
mysqli_query($link, $sql);
mysqli_close($link);

header('Location: mainPage.php');	
?>