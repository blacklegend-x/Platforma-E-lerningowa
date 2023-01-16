<?php declare(strict_types=1); // włączenie typowania zmiennych
session_start();
if (!isset($_SESSION['loggedin']))
{
	header('Location: login.php');
	exit();
}

$link = mysqli_connect(); //polaczenie z baza danych
if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD

$_SESSION['uprawnienia'] = $_GET['uprawnienia'];

$datetime = date('Y-m-d H:i:s');
$uprawnienia = $_GET['uprawnienia'];
$login = $_SESSION['username'];
$log = mysqli_query($link, "INSERT INTO aktywnosc(login, uprawnienia, datetime, opis) VALUES('$login', '$uprawnienia', '$datetime', 'Logowanie')");
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="fonts/fontawesome/css/all.css">
<style>
	::-webkit-file-upload-button{
		display: none;
	}
</style>
</head>
<body>
	Zalogowano w aplikacji jako użytkownik: 
	
<?php
$przegladarka = $_SERVER['HTTP_USER_AGENT']; //pobranie informacji o przegladarce goscia strony
$nazwa_przegladarki = wytnij_nazwe_przegladarki($przegladarka); //wywolanie funkcji do pobierania nazwy przegladarki

//przegladarka nazwa
function wytnij_nazwe_przegladarki($user_agent)
{
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edg')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
   
    return 'Other'; //nieznana
}
//pobieranie danych o przegladarce goscia w czasie rzeczywistym (aktualizacja przez odswiezenie strony)
$screen_width_height = "<script>document.write(screen.width);</script>".'x'."<script>document.write(screen.height);</script>";
$browser_width_height = "<script>document.write(window.innerWidth);</script>".'x'."<script>document.write(window.innerHeight);</script>";
$screen_colors = "<script>document.write(screen.colorDepth);</script>";
$cookies_enabled = "<script>document.write(navigator.cookieEnabled);</script>";
$java_enabled = "<script>document.write(navigator.javaEnabled());</script>";
$browser_language = "<script>document.write(navigator.language);</script>";

date_default_timezone_set('Europe/Warsaw');

echo $_SESSION['username']; //wyswietlenie nazwy aktualnie zalogowanego uzytkownika

$ipaddress = $_SERVER["REMOTE_ADDR"]; //ip goscia
function ip_details($ip) { //funkcja do wyodrebniania szczegolow na podstawie ip
	$json = file_get_contents ("http://ipinfo.io/{$ip}/geo");
	$details = json_decode ($json);
	return $details;
}

$details = ip_details($ipaddress); //szczegoly wyodrebnione z adresu ip
$loc = $details -> loc; //aktualna lokalizacja goscia
$dateTime= date('Y-m-d H:i:s');

$username =  $_SESSION['username'];
$result = mysqli_query($link, "SELECT * FROM goscieportalu WHERE username='$username'"); //wiersze, w którym login=login z formularza
$rekord = mysqli_fetch_array($result); //wiersza z BD, struktura zmiennej jak w BD

echo "<br/ >Bieżące dane z sesji:<br /> ";
echo "<table border='1'>
<tr>
<th>ipaddress</th>
<th>datetime</th>
<th>country</th>
<th>city</th>
<th>link</th>
<th>przegladarka</th>
<th>screen resolution</th>
<th>browser resolution</th>
<th>colors</th>
<th>cookies enabled</th>
<th>java enabled</th>
<th>language</th>
</tr>";

// echo $details -> country;
// foreach ($result as $row) {
	echo "<tr>";
	echo "<td>" .$ipaddress."</td>";
	echo "<td>" .$dateTime."</td>";
	echo "<td>" .$details -> country."</td>";
	echo "<td>" .$details -> city."</td>";
	echo "<td>"."<a href='https://www.google.pl/maps/place/$loc'>LINK</a>"."</td>";
	echo "<td>" .wytnij_nazwe_przegladarki($przegladarka)."</td>";
	echo "<td>" .$screen_width_height."</td>";
	echo "<td>" .$browser_width_height."</td>";
	echo "<td>" .$screen_colors."</td>";
	echo "<td>" .$cookies_enabled."</td>";
	echo "<td>" .$java_enabled."</td>";
	echo "<td>" .$browser_language."</td>";
	// }
echo "</table>";

echo "<br><a href='index.php'>Powrót do index.php</a><br/>";

//wlamania:
$result1 = mysqli_query($link, "SELECT * FROM break_ins ORDER BY datetime DESC LIMIT 1"); //wiersze, w którym login=login z formularza
$rekord1 = mysqli_fetch_array($result1); //wiersza z BD, struktura zmiennej jak w 

foreach($result1 as $row){
	echo " <p style= 'color:red';>Ostatnie błędne zalogowanie ".$row['datetime']. " Adres IP: ". $row['ip']. "</p>";
}


mysqli_close($link);
?>

<a href ="mainPage.php">E-LEARNING</a><br/><br/>
<a href ="logout.php">Wyloguj</a><br/>
</body>
</html>