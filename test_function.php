<?php
require_once "tcpdf/tcpdf.php";
require_once "FPDI/fpdi.php";

# Original File
$pdf = new TCPDF( '', 'mm', '' ); //FPDI extends TCPDF

print_r($pdf);