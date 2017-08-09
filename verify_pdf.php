<?php
require_once "tcpdf/tcpdf.php";
require_once "FPDI/fpdi.php";

//$file = "test.pdf";

    //from: http://stackoverflow.com/a/9059073/284932
        function isStringInFile($file,$string){

        $handle = fopen($file, 'r');
        $valid = false; // init as false
        while (($buffer = fgets($handle)) !== false) {
            if (strpos($buffer, $string) !== false) {
                $valid = TRUE;
                break; // Once you find the string, you should break out the loop.
            } 
            echo $buffer;     
        }
        

        fclose($handle);

        return $valid;

    }

    if (isStringInFile('20170725094048_sign_cmi600030486520.pdf', 'adbe.pkcs7.detached')) {
        echo "SIGN";
    }
    else {
        echo "Invalid";
    }