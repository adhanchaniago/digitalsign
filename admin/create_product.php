<?php
// include database and object files
include_once 'config/database.php';
include_once 'objects/product.php';
include_once 'objects/category.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// pass connection to objects
$product = new Product($db);
$category = new Category($db);

// set page headers
$page_title = "Sign PDF";
include_once "header.php";
 
// contents will be here
echo "<div class='right-button-margin'>";
    echo "<a href='index.php' class='btn btn-default pull-right'>Read Products</a>";
echo "</div>";

// if the form was submitted - PHP OOP CRUD Tutorial
if(!empty($_POST)){

    //print_r($_POST);

    if($_POST['name'] != NULL) { // check name
        // set product property values
        $product->name = $_POST['name'];
        $product->pdf_password = $_POST['pdf_password'];
        //$product->price = $_POST['price'];
        $product->description = $_POST['description'];
        $product->category_id = $_POST['category_id'];

        //print_r($_FILES);
        //exit;

        $pdf=!empty($_FILES["pdf"]["name"]) ? basename($_FILES["pdf"]["name"]) : "";

        $product->pdf = $pdf;

        // try to upload the submitted file
        // uploadPhoto() method will return an error message, if any.
        if(!empty($pdf)) {
            echo $product->uploadPDF();
        }
    
        // create the product
        if($product->create()){

            echo "<div class='alert alert-success'>Product was created.</div>";
        }
    
        // if unable to create the product, tell the user
        else{
            echo "<div class='alert alert-danger'>Unable to create product.</div>";
        }

    }
    else {
        echo "<div class='alert alert-danger'>Unable to create product, please insert 
Insurance No.</div>";
    }
 
    
}


?>

<!-- HTML form for creating a product -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
 
    <table class='table table-hover table-responsive table-bordered'>
 
        <tr>
            <td>Insurance No.</td>
            <td><input type='text' name='name' class='form-control' /></td>
        </tr>
 

        <tr>
    		<td>PDF</td>
    		<td><input type="file" name="pdf" /></td>
		</tr>

        <tr>
            <td>PDF Password</td>
            <td><input type='text' name='pdf_password' class='form-control' /></td>
        </tr>
 
        <tr>
            <td>Description</td>
            <td><textarea name='description' class='form-control'></textarea></td>
        </tr>
 
        <tr>
            <td>Category</td>
            <td>
            <!-- categories from database will be here -->
            <?php
			// read the product categories from the database
			$stmt = $category->read();
			 
			// put them in a select drop-down
			echo "<select class='form-control' name='category_id'>";
			    echo "<option>Select category...</option>";
			 
			    while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)){
			        extract($row_category);
			        echo "<option value='{$id}'>{$name}</option>";
			    }
			 
			echo "</select>";
			?>
            </td>
        </tr>
 
        <tr>
            <td></td>
            <td>
                <button type="submit" class="btn btn-primary">Sign PDF</button>
            </td>
        </tr>
 
    </table>
</form>


<?php
// footer
include_once "footer.php";
?>