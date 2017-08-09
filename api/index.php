<?php
    // get ID of the product to be read
    $params = isset($_GET['params']) ? $_GET['params'] : die('ERROR: missing params.');
  #keeps users from requesting any file they want
  $safe_pages = array("create", "search", "thread");
   
    print_r($params);
    exit;
  if(in_array($params[0], $safe_pages)) {
    include("product/".$params[0].".php");
  } else {
    include("404.php");
  }
?>