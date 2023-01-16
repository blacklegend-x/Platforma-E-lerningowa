<?php 
    session_start();

if(!isset($_SESSION['login_attempt'])){
    $_SESSION['login_attempt']=0;
}

    if (isset($_SESSION['login_locked']))
    {
        if (time() - $_SESSION['login_locked'] > 60) //czy minelo 60s
        {
            unset($_SESSION['login_locked']);
            unset($_SESSION['login_error']);
        }
    }
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<BODY>
Formularz logowania
<form method="post" action="weryfikuj.php">
Login:<input type="text" name="user" maxlength="20" size="20"><br>
Hasło:<input type="password" name="pass" maxlength="20" size="20"><br>
<?php     
if(isset($_SESSION['login_error']))
{
    echo "Nieudane logowanie - poczekaj minutę!";
}
else
{
?>
<input type="submit" value="Send"/><br />
<?php 
} 
?>
<br><a href="index.php">Powrót do index.php</a><br/>
<a href="rejestruj.php">Rejestruj</a></br>
<a href="mainPage.php">Przegladaj jako gość</a>
</form>
</BODY>
</HTML>