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

//ZAKONCZENIE TESTU

$idt = $_GET['idt'];
$username = $_SESSION['username'];

date_default_timezone_set('Europe/Warsaw');
$datetime = date('Y-m-d H:i:s');

//pobranie nazwy rozwiazanego testu
$testquery = mysqli_query($link, "SELECT * FROM test WHERE idt='$idt'");
foreach ($testquery as $row) {
	$nazwa = $row['nazwa'];
}

$contents = "<p>" . $nazwa . "</p><br>" . $datetime . "<br>" . $username . "<br>";

$konczy = "Konczy " . $nazwa;
$loggeduser = $_SESSION['username'];
$uprawnienia = $_SESSION['uprawnienia'];
$log = mysqli_query($link, "INSERT INTO aktywnosc(login, uprawnienia, datetime, opis) VALUES('$loggeduser', '$uprawnienia', '$datetime', '$konczy')");

$punkty = 0; //poprawne odpowiedzi
$pytania = 0;

//sprawdzenie kazdego pytania
$pytaniaquery = mysqli_query($link, "SELECT * FROM pytania WHERE idt='$idt'");
foreach($pytaniaquery as $row){
	$pytania = $pytania + 1;
	
	$idp = $row['idp'];
	$tresc_pytania = $row['tresc_pytania']; 
	$odp_a = $row['odpowiedz_a']; 
	$odp_b = $row['odpowiedz_b']; 
	$odp_c = $row['odpowiedz_c']; 
	$odp_d = $row['odpowiedz_d']; 
	$poprawna = $row['poprawna']; 
	
	$contents = $contents . "<br><h4>Treść pytania: " . $tresc_pytania . "</h4>";
	
	if($poprawna == 'a'){
	$contents = $contents . '<style="color:green;"><br>a. ' . $odp_a . " - POPRAWNA";
	$contents = $contents . "<br>b. " . $odp_b;
	$contents = $contents . "<br>c. " . $odp_c;
	$contents = $contents . "<br>d. " . $odp_d;		
	}
	if($poprawna == 'b'){
	$contents = $contents . "<br>a. " . $odp_a;
	$contents = $contents . '<br><style="color:green;">b. ' . $odp_b . " - POPRAWNA";
	$contents = $contents . "<br>c. " . $odp_c;
	$contents = $contents . "<br>d. " . $odp_d;		
	}
	if($poprawna == 'c'){
	$contents = $contents . "<br>a. " . $odp_a;
	$contents = $contents . "<br>b. " . $odp_b;
	$contents = $contents . '<br><style="color:green;">c. ' . $odp_c . " - POPRAWNA";
	$contents = $contents . "<br>d. " . $odp_d;		
	}
	if($poprawna == 'd'){
	$contents = $contents . "<br>a. " . $odp_a;
	$contents = $contents . "<br>b. " . $odp_b;
	$contents = $contents . "<br>c. " . $odp_c;
	$contents = $contents . '<br><h4 style="color:green;">d. ' . $odp_d . " - POPRAWNA</h4>";		
	}
	
	$odpowiedz = $_POST[$idp]; //odpowiedz
	if($odpowiedz == $poprawna){
		$punkty = $punkty + 1;
		$contents = $contents . '<br><h4 style="color:green;">Odpowiedź: ' . $odpowiedz . " - Pkt za pytanie: 1</h4>";
	}else{
		$contents = $contents . '<br><h4 style="color:red;">Odpowiedź: ' . $odpowiedz . " - Pkt za pytanie: 0</h4>";
	}
	
	$contents = $contents . "<br>";
	
}

//pobranie id uzytkownika
if($_SESSION['uprawnienia'] == 'p'){
	$idquery = mysqli_query($link, "SELECT * FROM pracownik WHERE login='$username'");
	foreach ($idquery as $row) {
		$id = $row['idu'];
	}
}
else if($_SESSION['uprawnienia'] == 'n'){
	$idquery = mysqli_query($link, "SELECT * FROM nauczyciel WHERE login='$username'");
	foreach ($idquery as $row) {
		$id = $row['idn'];
	}
}

$login = $_SESSION['username'];

$wynik = $punkty . "/" . $pytania;

$plik_pdf = $login . $nazwa . $datetime . ".pdf";

$plik_pdf_path = "pliki/" . $login . $nazwa . $datetime . ".pdf";

$zapisz = mysqli_query($link, "INSERT INTO wyniki (login, idt, datetime, punkty, plik_pdf) 
VALUES ('$login', '$idt', '$datetime', '$wynik', '$plik_pdf_path')");

mysqli_close($link);

$contents = $contents . "<br>PUNKTY: " . $wynik;	

$_SESSION['contents'] = $contents;

echo "<a href='pdfgenerator.php?plik_pdf='$plik_pdf' target='_blank'> PLIK PDF </a>";

echo "<br><a href='mainPage.php'>Powrót do strony głównej</a>";

?>