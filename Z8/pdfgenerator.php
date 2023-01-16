<?php declare(strict_types=1); // włączenie typowania zmiennych
session_start();
if (!isset($_SESSION['loggedin']))
{
	header('Location: logowanie.php');
	exit();
}

//pdf
require_once('tcpdf/tcpdf.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setFont('dejavusans', '', 10);

//$html = $_GET['contents']; //zawartosc pliku
$html = $_SESSION['contents']; //zawartosc pliku

$plik_pdf = $_SESSION['plik_pdf'];
$directory = '/pliki/' . $plik_pdf;

$pdf->SetTitle($plik_pdf);

$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->lastPage();

$pdf->Output(__DIR__ . $directory,'F');
$pdf->Output();

//header('Location: mainPage.php');
?>
