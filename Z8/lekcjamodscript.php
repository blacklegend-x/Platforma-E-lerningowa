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
$tekst = $_POST['tekst'];
$idl = $_GET['idl'];
$idn = $_GET['idn'];

$target_dir = 'pliki/';

//plik
$file_name = $_FILES["file"]["name"];
if($file_name != ""){
	$file_extension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
	$target_location = $target_dir . $file_name;
	move_uploaded_file($_FILES["file"]["tmp_name"], $target_location);
}else{
	$file_extension = "";
	$target_location = "";
}

$sql = "UPDATE lekcje SET idn='$idn', nazwa='$nazwa', tresc='$tekst', file_name='$target_location', file_ext='$file_extension' WHERE idl='$idl'";
	
mysqli_query($link, $sql);
mysqli_close($link);

header('Location: mainPage.php');	
?>