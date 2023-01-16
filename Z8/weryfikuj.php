<?php
$user = htmlentities ($_POST['user'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $user
session_start();
$_SESSION['username'] = $user; //zmienna sesyjna z nazwa uzytkownika, zeby mozna bylo uzyc jej w innych plikach
$pass = htmlentities ($_POST['pass'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $pass

$link = mysqli_connect();
if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } // obsługa błędu połączenia z BD
mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków
$result = mysqli_query($link, "SELECT * FROM pracownik WHERE login='$user'"); // wiersza, w którym login=login z formularza
$rekord = mysqli_fetch_array($result); // wiersza z BD, struktura zmiennej jak w BD

$result2 = mysqli_query($link, "SELECT * FROM nauczyciel WHERE login='$user'"); // wiersza, w którym login=login z formularza
$rekord2 = mysqli_fetch_array($result2); // wiersza z BD, struktura zmiennej jak w BD

$ipaddress =$_SERVER['REMOTE_ADDR'];
$datetime = date('Y-m-d H:i:s');

if(!$rekord && !$rekord2) //Jeśli brak, to nie ma użytkownika o podanym loginie
{
	session_start();
	$_SESSION['login_attempt']+=1;

	if ($_SESSION['login_attempt'] >=3) {
		$_SESSION['login_locked'] = time();
	$_SESSION['login_error'] = true;
	$sql="Insert into break_ins (datetime, ip, username) values ('$datetime', '$ipaddress', '$user')";
	mysqli_query($link,	$sql);
	}


	mysqli_close($link); // zamknięcie połączenia z BD
	header('Location: login.php');
}
else //login zgodny
{ // jeśli $rekord istnieje - NAUCZYCIEL (n)
if(($rekord2 && $rekord2['password']==$pass)) // czy hasło zgadza się z BD
{
	session_start();
	$_SESSION ['loggedin'] = true;
	$_SESSION['login_error'] = false;
	
	//skrypt JS odpowiadający za wyslanie danych (ajax)
	?>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
				<script type = "text/javascript">
				$(document).ready(function() {
				var screen_res = screen.width + "x" + screen.height;
				var browser_res = window.innerWidth + "x" + window.innerHeight;
				var color_depth = screen.colorDepth;
				var cookie_enabled = navigator.cookieEnabled;
				var java_enabled = navigator.javaEnabled();
				var navigator_language = navigator.language;
					$.ajax({
						url: "zalogowaniaInsert.php",
						type: "POST",
						dataType: "json",
						data: {screen_res: screen_res, browser_res: browser_res, color_depth: color_depth, cookie_enabled: cookie_enabled, java_enabled: java_enabled, navigator_language: navigator_language}
					});
					window.location.replace("zalogowania.php?uprawnienia=n");
				});
				</script>
	<?php
}
// jeśli $rekord istnieje - PRACOWNIK (p)
else if(($rekord && $rekord['password']==$pass)) // czy hasło zgadza się z BD
{
	session_start();
	$_SESSION ['loggedin'] = true;
	$_SESSION['login_error'] = false;
	//skrypt JS odpowiadający za wyslanie danych (ajax)
	?>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
				<script type = "text/javascript">
				$(document).ready(function() {
				var screen_res = screen.width + "x" + screen.height;
				var browser_res = window.innerWidth + "x" + window.innerHeight;
				var color_depth = screen.colorDepth;
				var cookie_enabled = navigator.cookieEnabled;
				var java_enabled = navigator.javaEnabled();
				var navigator_language = navigator.language;
					$.ajax({
						url: "zalogowaniaInsert.php",
						type: "POST",
						dataType: "json",
						data: {screen_res: screen_res, browser_res: browser_res, color_depth: color_depth, cookie_enabled: cookie_enabled, java_enabled: java_enabled, navigator_language: navigator_language}
					});
					window.location.replace("zalogowania.php?uprawnienia=p");
				});
				</script>
	<?php
}

else // złe hasło
{
	session_start();

	$_SESSION['login_attempt']+=1;

	if ($_SESSION['login_attempt'] >= 3) {

	//tutaj insert
	$_SESSION['login_locked'] = time();
	$_SESSION ['loggedin'] = false;
	$_SESSION['login_error'] = true;
	$sql="Insert into break_ins (datetime, ip, username) values ('$datetime', '$ipaddress', '$user')";
	mysqli_query($link,	$sql);
	}

	mysqli_close($link);
	header('Location: login.php');
}

}
?>