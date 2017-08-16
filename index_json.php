<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

require_once "tcpdf/tcpdf.php";
require_once "FPDI/fpdi.php";

# Source URL
//$url = $_GET['url'];
isset($_GET['url']) ? $url = $_GET['url'] : $url = '';
//$pdf_password = $_GET['pdf_password'];
isset($_GET['pdf_password']) ? $pdf_password = $_GET['pdf_password'] : $pdf_password = '';

//print_r(pathinfo($url));
//exit;

if(empty($url)) {
	echo '{';
		echo '"message": "Invalid PDF URL to sign."';
	echo '}';
	exit;
}

if(pathinfo($url, PATHINFO_EXTENSION) != 'pdf') {
	echo '{';
		echo '"message": "Invalid PDF File to sign."';
	echo '}';
	exit;
}

// Check source File
$src_file = @file_get_contents($url);
if($src_file === FALSE) {
	echo '{';
		echo '"message": "Can not get PDF File to sign."';
	echo '}';
	exit;
}

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
	//echo "Found Dir: $dir_date <br>\n";
}
else {
	mkdir($dir_date, 0777, true);
	//echo "Create Dir: $dir_date Complete <br>\n";
}
//exit;

//$pdf = new FPDI( 'L', 'mm', 'LETTER' ); //FPDI extends TCPDF

# Original File
$pdf = new FPDI( '', 'mm', '' ); //FPDI extends TCPDF
$pdf->setPrintHeader(FALSE);
$pdf->setPrintFooter(FALSE);

/*
// One Page
$pdf->AddPage();
//$pages = $pdf->setSourceFile( 'test.pdf' );
$pages = $pdf->setSourceFile( 'signfiles/a.pdf' );
$page = $pdf->ImportPage( 1 );
$pdf->useTemplate( $page, 0, 0 );
*/

// Multiple Page
$pdf->AddPage();
$pdf->setSourceFile( 'signfiles/a.pdf' );
$pdf->numPages = $pdf->setSourceFile( 'signfiles/a.pdf' );
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0, 0);
if($pdf->numPages>1) {
	for($i=2;$i<=$pdf->numPages;$i++) {
		$pdf->AddPage();
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, 0, 0, 0);
	}
}



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

/*
// One Page
$pdf->AddPage();
$pages = $pdf->setSourceFile( 'signfiles/a.pdf' );
$page = $pdf->ImportPage( 1 );
$pdf->useTemplate( $page, 0, 0 );
*/

// Multiple Page
$pdf->AddPage();
$pdf->setSourceFile( 'signfiles/a.pdf' );
$pdf->numPages = $pdf->setSourceFile( 'signfiles/a.pdf' );
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0, 0);
if($pdf->numPages>1) {
	for($i=2;$i<=$pdf->numPages;$i++) {
		$pdf->AddPage();
		$tplIdx = $pdf->importPage($i);
		$pdf->useTemplate($tplIdx, 0, 0, 0);
	}
}


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

# Show File URL JSON


$org_url = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["HTTP_HOST"].$_SERVER["CONTEXT_PREFIX"].'/digitalsign/signfiles'.date('/Y/m/d/').$filename_org;
$sign_url = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["HTTP_HOST"].$_SERVER["CONTEXT_PREFIX"].'/digitalsign/signfiles'.date('/Y/m/d/').$filename_sign;

echo '{';
	echo '"message": "PDF was signed.",';
	echo '"org_url": "'.$org_url.'",';
	echo '"sign_url": "'.$sign_url.'"';
echo '}';

//$pdf->Output( 'sss.pdf', 'I' );
?>