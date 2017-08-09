<?php
require_once "tcpdf/tcpdf.php";
require_once "FPDI/fpdi.php";

### File Original / Sign Name ###
$filepath = '/Users/ton/Sites/signfiles/';
$filename_org = date('YmdHis').'.pdf';
$filename_sign = date('YmdHis').'_sign.pdf';
$filesave_org = $filepath.$filename_org;
$filesave_sign = $filepath.$filename_sign;

# Source URL
//$url = $_GET['url'];
//$src_file = file_get_contents($url);
//$src_pdf = file_put_contents('signfiles/a.pdf', $src_file);
//echo "$src_file <br>";

//$pdf = new FPDI( 'L', 'mm', 'LETTER' ); //FPDI extends TCPDF


$pdf = new FPDI();
$pdf->setPrintHeader(FALSE);
$pdf->setPrintFooter(FALSE);

$pageCount = $pdf->setSourceFile('a.pdf');
$tplIdx = $pdf->importPage(1);

$pdf->addPage();
$pdf->useTemplate($tplIdx);

$pdf->Output();

exit;

# Original File
$pdf = new FPDI( '', 'mm', '' ); //FPDI extends TCPDF
$pdf->setPrintHeader(FALSE);
$pdf->setPrintFooter(FALSE);

$pdf->AddPage();
$pages = $pdf->setSourceFile( 'a.pdf' );
//$pages = $pdf->setSourceFile( 'signfiles/a.pdf' );
$page = $pdf->ImportPage( 1 );
$pdf->useTemplate( $page, 0, 0 );

# Save Original File
$pdf->Output( $filesave_org, 'F' );

//$pdf->cleanUp();

# Sign FIle
$pdf = new FPDI( '', 'mm', '' ); //FPDI extends TCPDF


### Digital Signature ###
// set certificate file
$certificate = 'file://tcpdf.crt';

// set additional information
$info = array(
    'Name' => 'Management Information System Dept',
    'Location' => 'The Viriyah Insurance Public Company Limited',
    'Reason' => 'Digital Signature',
    'ContactInfo' => 'https://www.viriyah.co.th',
    );

// set document signature
$pdf->setSignature($certificate, $certificate, 'Digital Signature Demo', '', 2, $info);

$pdf->AddPage();
$pages = $pdf->setSourceFile( 'a.pdf' );
$page = $pdf->ImportPage( 1 );
$pdf->useTemplate( $page, 0, 0 );


// *** set signature appearance ***

// create content for signature (image and/or text)
//$pdf->Image('images/viriyah_logo.png', 20, 10, 15, 15, 'PNG');

// define active area for signature appearance
//$pdf->setSignatureAppearance(20, 10, 15, 15);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// *** set an empty signature appearance ***
//$pdf->addEmptySignatureAppearance(180, 80, 15, 15);

# Save Sign File
$pdf->Output( $filesave_sign, 'F' );
//$pdf->cleanUp();

# Show File URL
echo "$filesave_org <br>";
echo '<a href=http://localhost/~ton/signfiles/'.$filename_org.'>ORG DOWNLOAD</a> <br>';
echo "$filesave_sign <br>";
echo '<a href=http://localhost/~ton/signfiles/'.$filename_sign.'>SIGN DOWNLOAD</a>'

//$pdf->Output( 'sss.pdf', 'I' );
?>