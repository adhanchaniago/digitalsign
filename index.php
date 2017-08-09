<?php
require_once "tcpdf/tcpdf.php";
require_once "FPDI/fpdi.php";

# Source URL
//$url = $_GET['url'];
isset($_GET['url']) ? $url = $_GET['url'] : $url = '';
//$pdf_password = $_GET['pdf_password'];
isset($_GET['pdf_password']) ? $pdf_password = $_GET['pdf_password'] : $pdf_password = '';

if(empty($url)) {
	echo "invalid url\n";
	exit;
}

$src_file = file_get_contents($url);
$pdf_filename = basename($url);
//echo "$pdf_filename <br>\n";
//exit;

$src_pdf = file_put_contents('signfiles/a.pdf', $src_file);
//echo "$src_file <br>";

### File Original / Sign Name ###
$filepath = $_SERVER["CONTEXT_DOCUMENT_ROOT"].'/digitalsign/signfiles';
//echo $filepath;
//exit;
$filename_org = date('YmdHis')."_".$pdf_filename;
$filename_sign = date('YmdHis')."_sign_".$pdf_filename;

$dir_date = $filepath.date('/Y/m/d/');
$filesave_org = $dir_date.$filename_org;
$filesave_sign = $dir_date.$filename_sign;

### Sub Tree Dir ###
//echo "$dir_date <br>\n";
//exit;

if (is_dir($dir_date)) {
	echo "Found Dir: $dir_date <br>\n";
}
else {
	mkdir($dir_date, 0777, true);
	echo "Create Dir: $dir_date Complete <br>\n";
}
//exit;

//$pdf = new FPDI( 'L', 'mm', 'LETTER' ); //FPDI extends TCPDF

# Original File
$pdf = new FPDI( '', 'mm', '' ); //FPDI extends TCPDF
$pdf->setPrintHeader(FALSE);
$pdf->setPrintFooter(FALSE);

$pdf->AddPage();
//$pages = $pdf->setSourceFile( 'test.pdf' );
$pages = $pdf->setSourceFile( 'signfiles/a.pdf' );
$page = $pdf->ImportPage( 1 );
$pdf->useTemplate( $page, 0, 0 );

# Save Original File
$pdf->Output( $filesave_org, 'F' );


# Sign FIle
$pdf = new FPDI( '', 'mm', '' ); //FPDI extends TCPDF
$pdf->setPrintHeader(FALSE);
$pdf->setPrintFooter(FALSE);


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
// set permission
if(!(empty($pdf_password))) {
	$pdf->SetProtection($permissions=array('print', 'copy'), $pdf_password, null, 0, null);
}

$pdf->AddPage();
$pages = $pdf->setSourceFile( 'signfiles/a.pdf' );
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

# Show File URL
echo "Create: $filesave_org <br>";
echo '<a href='.$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["HTTP_HOST"].$_SERVER["CONTEXT_PREFIX"].'/digitalsign/signfiles/'.date('/Y/m/d/').$filename_org.'>ORG DOWNLOAD</a> <br>';
echo "Sign: $filesave_sign <br>";
echo '<a href='.$_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["HTTP_HOST"].$_SERVER["CONTEXT_PREFIX"].'/digitalsign/signfiles/'.date('/Y/m/d/').$filename_sign.'>SIGN DOWNLOAD</a>'

//$pdf->Output( 'sss.pdf', 'I' );
?>