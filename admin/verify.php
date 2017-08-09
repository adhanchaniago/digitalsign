<?php
// get ID of the product to be read
$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');
 
// include database and object files
include_once 'config/database.php';
include_once 'objects/product.php';
include_once 'objects/category.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare objects
$product = new Product($db);
$category = new Category($db);
 
// set ID property of product to be read
$product->id = $id;
 
// read the details of product to be read
$product->readOne();

// set page headers
$page_title = "Verify PDF";
include_once "header.php";
 
// read products button
echo "<div class='right-button-margin'>";
    echo "<a href='index.php' class='btn btn-primary pull-right'>";
        echo "<span class='glyphicon glyphicon-list'></span> Show All PDF";
    echo "</a>";
echo "</div>";

// HTML table for displaying a product details
echo "<table class='table table-hover table-responsive table-bordered'>";
 
    echo "<tr>";
        echo "<td>Insurance No.</td>";
        echo "<td>{$product->name}</td>";
    echo "</tr>";
 
    echo "<tr>";
        echo "<td>Description</td>";
        echo "<td>{$product->description}</td>";
    echo "</tr>";

    echo "<tr>";
    	echo "<td>PDF</td>";
    	echo "<td>";
        	echo $product->sign_pdf ? "<iframe src='http://docs.google.com/gview?url={$product->sign_pdf}' style='width:600px; height:500px;' frameborder='0' /></iframe>" : "No image found.";
    	echo "</td>";
	echo "</tr>";
 
    echo "<tr>";
        echo "<td>Category</td>";
        echo "<td>";
            // display category name
            $category->id=$product->category_id;
            $category->readName();
            echo $category->name;
        echo "</td>";
    echo "</tr>";
 
echo "</table>";
 
// set footer
include_once "footer.php";
?>