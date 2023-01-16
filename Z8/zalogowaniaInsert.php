<?php
date_default_timezone_set('Europe/Warsaw');

$ipaddress = $_SERVER["REMOTE_ADDR"]; //ip goscia
function ip_details($ip) { //funkcja do wyodrebniania szczegolow na podstawie ip
	$json = file_get_contents ("http://ipinfo.io/{$ip}/geo");
	$details = json_decode ($json);
	return $details;
}

$details = ip_details($ipaddress); //szczegoly wyodrebnione z adresu ip
$loc = $details -> loc; //aktualna lokalizacja goscia
$dateTime= date('Y-m-d H:i:s');

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

$link = mysqli_connect(); //polaczenie z baza danych
if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } // obsługa błędu połączenia z BD

//odebranie wszystkich zmiennych i wrzucenie ich do tabeli goscieportalu
session_start();
$username = $_SESSION['username'];
$screen_res = $_POST['screen_res'];
$browser_res = $_POST['browser_res'];
$color_depth = $_POST['color_depth'];
$cookie_enabled = $_POST['cookie_enabled'];
$java_enabled = $_POST['java_enabled'];
$navigator_language = $_POST['navigator_language'];

$query = "INSERT INTO goscieportalu (ipaddress, datetime, przegladarka, username, screen_res, browser_res, color_depth, cookie_enabled, java_enabled, navigator_language) 
VALUES ('$ipaddress','$dateTime','$nazwa_przegladarki','$username', '$screen_res', '$browser_res', '$color_depth', '$cookie_enabled', '$java_enabled', '$navigator_language')";
mysqli_query($link, $query);
mysqli_close($link);
?>