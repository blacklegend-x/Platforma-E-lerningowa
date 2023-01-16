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

$idt = $_GET['idt'];
$tresc_pytania = $_POST['tresc_pytania'];
$odp_a = $_POST['odp_a'];
$odp_b = $_POST['odp_b'];
$odp_c = $_POST['odp_c'];
$odp_d = $_POST['odp_d'];

if(!isset($_POST['apoprawna'])){
	$apoprawna = 0;
}else{
	$apoprawna = $_POST['apoprawna'];
}

if(!isset($_POST['bpoprawna'])){
	$bpoprawna = 0;
}else{
	$bpoprawna = $_POST['bpoprawna'];
}

if(!isset($_POST['cpoprawna'])){
	$cpoprawna = 0;
}else{
	$cpoprawna = $_POST['cpoprawna'];
}

if(!isset($_POST['dpoprawna'])){
	$dpoprawna = 0;
}else{
	$dpoprawna = $_POST['dpoprawna'];
}

$target_dir = 'pliki/';

$file_name = $_FILES["file"]["name"];
if($file_name != ""){
	$file_extension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
	$target_location = $target_dir . $file_name;
	move_uploaded_file($_FILES["file"]["tmp_name"], $target_location);
}else{
	$file_extension = "";
	$target_location = "";
}

$sql = "INSERT INTO pytania(idt, tresc_pytania, odpowiedz_a, odpowiedz_b, odpowiedz_c, odpowiedz_d, apoprawna, bpoprawna, cpoprawna, dpoprawna, file_name, file_Ext) 
VALUES('$idt', '$tresc_pytania', '$odp_a', '$odp_b', '$odp_c', '$odp_d', '$apoprawna', '$bpoprawna', '$cpoprawna', '$dpoprawna', '$file_name', '$file_extension')";
	
mysqli_query($link, $sql);
mysqli_close($link);

header("Location: pytaniatest.php?idt=" . $idt);	
?>