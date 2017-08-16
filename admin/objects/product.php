<?php
class Product{
 
    // database connection and table name
    private $conn;
    private $table_name = "products";
 
    // object properties
    public $id;
    public $name;
    public $description;
    public $pdf_url;
    public $pdf_password;
    public $org_pdf;
    public $sign_pdf;
    public $pdf;
    public $category_id;
    public $timestamp;
    public $created;
 
    public function __construct($db){
        $this->conn = $db;
    }
 
    // create product
    function create(){
 
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
            SET name=:name, description=:description,
                category_id=:category_id, pdf_url=:pdf_url, pdf_password=:pdf_password, org_pdf=:org_pdf, sign_pdf=:sign_pdf, created=:created";
 
        $stmt = $this->conn->prepare($query);
 
        // posted values
        $this->name=htmlspecialchars(strip_tags($this->name));
        //$this->price=htmlspecialchars(strip_tags($this->price));
        $this->description=htmlspecialchars(strip_tags($this->description));

        $this->pdf_url=htmlspecialchars(strip_tags($this->pdf_url));
        $this->pdf_password=htmlspecialchars(strip_tags($this->pdf_password));
        $this->org_pdf=htmlspecialchars(strip_tags($this->org_pdf));
        $this->sign_pdf=htmlspecialchars(strip_tags($this->sign_pdf));

        $this->category_id=htmlspecialchars(strip_tags($this->category_id));

 
        // to get time-stamp for 'created' field
        $this->timestamp = date('Y-m-d H:i:s');
 
        // bind values 
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);

        $stmt->bindParam(":pdf_url", $this->pdf_url);
        $stmt->bindParam(":pdf_password", $this->pdf_password);

        $stmt->bindParam(":org_pdf", $this->org_pdf);

        $stmt->bindParam(":sign_pdf", $this->sign_pdf);

        $stmt->bindParam(":category_id", $this->category_id);

        $stmt->bindParam(":created", $this->timestamp);
 
        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
 
    }

    function readAll($from_record_num, $records_per_page){
 
        $query = "SELECT
                    id, name, description, pdf_url, org_pdf, sign_pdf, category_id, created
                FROM
                    " . $this->table_name . "
                ORDER BY
                    id DESC
                LIMIT
                    {$from_record_num}, {$records_per_page}";
     
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
     
        return $stmt;
    }

    // used for paging products
    public function countAll(){
     
        $query = "SELECT id FROM " . $this->table_name . "";
     
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
     
        $num = $stmt->rowCount();
     
        return $num;
    }

    function readOne(){
 
        $query = "SELECT name, description, pdf_url, pdf_password, org_pdf, sign_pdf, category_id, created
            FROM " . $this->table_name . "
            WHERE id = ?
            LIMIT 0,1";
     
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
     
        $this->name = $row['name'];
        $this->description = $row['description'];
        $this->pdf_password = $row['pdf_password'];
        $this->sign_pdf = $row['sign_pdf'];
        $this->category_id = $row['category_id'];
        $this->image = $row['pdf_url'];
        $this->crtated = $row['created'];
    }

    function update(){

        // Check Update PDF File
        if(!empty($this->pdf)) {
            $query = "UPDATE
                    " . $this->table_name . "
                SET
                    name = :name,
                    description = :description,
                    pdf_password = :pdf_password,
                    org_pdf = :org_pdf,
                    sign_pdf = :sign_pdf,
                    category_id  = :category_id
                WHERE
                    id = :id";
            
        }
        else {
            $query = "UPDATE
                    " . $this->table_name . "
                SET
                    name = :name,
                    description = :description,
                    pdf_password = :pdf_password,
                    category_id  = :category_id
                WHERE
                    id = :id";

        }

     
        $stmt = $this->conn->prepare($query);
     
        // posted values
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->pdf_password=htmlspecialchars(strip_tags($this->pdf_password));

        $this->org_pdf=htmlspecialchars(strip_tags($this->org_pdf));
        $this->sign_pdf=htmlspecialchars(strip_tags($this->sign_pdf));

        $this->category_id=htmlspecialchars(strip_tags($this->category_id));
        $this->id=htmlspecialchars(strip_tags($this->id));
     
        // bind parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':pdf_password', $this->pdf_password);


        // Check Update PDF File
        if(!empty($this->pdf)) {
            $stmt->bindParam(":org_pdf", $this->org_pdf);
            $stmt->bindParam(":sign_pdf", $this->sign_pdf);
            print_r($this);
        }

        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);
     
        // execute the query
        if($stmt->execute()){
            return true;
        }
     
        return false;
         
    }

    // delete the product
    function delete(){
     
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
         
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
     
        if($result = $stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    // read products by search term
    public function search($search_term, $from_record_num, $records_per_page){
     
        // select query
        $query = "SELECT
                    c.name as category_name, p.id, p.name, p.description, p.org_pdf, p.sign_pdf, p.category_id, p.created
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN
                        categories c
                            ON p.category_id = c.id
                WHERE
                    p.name LIKE ? OR p.description LIKE ?
                ORDER BY
                    p.name ASC
                LIMIT
                    ?, ?";
     
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
     
        // bind variable values
        $search_term = "%{$search_term}%";
        $stmt->bindParam(1, $search_term);
        $stmt->bindParam(2, $search_term);
        $stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);
     
        // execute query
        $stmt->execute();
     
        // return values from database
        return $stmt;
    }
     
    public function countAll_BySearch($search_term){
     
        // select query
        $query = "SELECT
                    COUNT(*) as total_rows
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN
                        categories c
                            ON p.category_id = c.id
                WHERE
                    p.name LIKE ?";
     
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
     
        // bind variable values
        $search_term = "%{$search_term}%";
        $stmt->bindParam(1, $search_term);
     
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
     
        return $row['total_rows'];
    }

    // will upload image file to server
    function uploadPhoto(){
     
        $result_message="";
     
        // now, if image is not empty, try to upload the image
        if($this->image){
     
            // sha1_file() function is used to make a unique file name
            $target_directory = "uploads/";
            $target_file = $target_directory . $this->image;
            $file_type = pathinfo($target_file, PATHINFO_EXTENSION);
     
            // error message is empty
            $file_upload_error_messages="";

            // make sure that file is a real image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if($check!==false){
                // submitted file is an image
            }else{
                $file_upload_error_messages.="<div>Submitted file is not an image.</div>";
            }
             
            // make sure certain file types are allowed
            $allowed_file_types=array("jpg", "jpeg", "png", "gif");
            if(!in_array($file_type, $allowed_file_types)){
                $file_upload_error_messages.="<div>Only JPG, JPEG, PNG, GIF files are allowed.</div>";
            }
             
            // make sure file does not exist
            if(file_exists($target_file)){
                $file_upload_error_messages.="<div>Image already exists. Try to change file name.</div>";
            }
             
            // make sure submitted file is not too large, can't be larger than 1 MB
            if($_FILES['image']['size'] > (1024000)){
                $file_upload_error_messages.="<div>Image must be less than 1 MB in size.</div>";
            }
             
            // make sure the 'uploads' folder exists
            // if not, create it
            if(!is_dir($target_directory)){
                mkdir($target_directory, 0777, true);
            }

     
        }

        // if $file_upload_error_messages is still empty
        if(empty($file_upload_error_messages)){
            // it means there are no errors, so try to upload the file
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
                // it means photo was uploaded
            }else{
                $result_message.="<div class='alert alert-danger'>";
                    $result_message.="<div>Unable to upload photo.</div>";
                    $result_message.="<div>Update the record to upload photo.</div>";
                $result_message.="</div>";
            }
        }
         
        // if $file_upload_error_messages is NOT empty
        else{
            // it means there are some errors, so show them to user
            $result_message.="<div class='alert alert-danger'>";
                $result_message.="{$file_upload_error_messages}";
                $result_message.="<div>Update the record to upload photo.</div>";
            $result_message.="</div>";
        }
     
        return $result_message;
    }

    // will upload PDF file to server
    function uploadPDF(){
     
        $result_message="";
     
        // now, if PDF is not empty, try to upload the PDF
        if($this->pdf){
     
            // sha1_file() function is used to make a unique file name
            $target_directory = "uploads/";
            $target_file = $target_directory . $this->pdf;
            $file_type = pathinfo($target_file, PATHINFO_EXTENSION);
     
            // error message is empty
            $file_upload_error_messages="";
             
            // make sure certain file types are allowed
            $allowed_file_types=array("pdf");
            if(!in_array($file_type, $allowed_file_types)){
                $file_upload_error_messages.="<div>Only PDF files are allowed.</div>";
            }
             
            // make sure file does not exist
            //if(file_exists($target_file)){
            //    $file_upload_error_messages.="<div>Image already exists. Try to change file name.</div>";
            //}
             
            // make sure submitted file is not too large, can't be larger than 1 MB
            if($_FILES['pdf']['size'] > (10240000)){
                $file_upload_error_messages.="<div>Image must be less than 10 MB in size.</div>";
            }
             
            // make sure the 'uploads' folder exists
            // if not, create it
            if(!is_dir($target_directory)){
                mkdir($target_directory, 0777, true);
            }

     
        }

        // if $file_upload_error_messages is still empty
        if(empty($file_upload_error_messages)){
            // it means there are no errors, so try to upload the file
            if(move_uploaded_file($_FILES["pdf"]["tmp_name"], $target_file)){
                // it means PDF was uploaded
                // Call Digital Sign API
                $sign_url = "http://localhost/~ton/digitalsign/index_json.php?url=http://localhost/~ton/digitalsign/admin/"."$target_file"."&pdf_password="."$this->pdf_password";

                //print_r($this);

                //echo "$api_url <br>\n";
                //$a = file_get_contents($api_url);
                //$result_message.="$a";

                //echo "$sign_url";
                $sign_api = json_decode(file_get_contents($sign_url));
                //print_r($sign_api);

                // Insert URL Original - Signed PDF to DB
                $this->org_pdf = $sign_api->org_url;
                $this->sign_pdf = $sign_api->sign_url;

                //$result_message.= "$this->org_pdf : $this->sign_pdf : <br> $sign_api->org_url : $sign_api->sign_url\n";

                //$result_message.=print_r($product);


            }else{
                $result_message.="<div class='alert alert-danger'>";
                    $result_message.="<div>Unable to upload PDF.</div>";
                    $result_message.="<div>Update the record to upload PDF.</div>";
                $result_message.="</div>";
            }
        }
         
        // if $file_upload_error_messages is NOT empty
        else{
            // it means there are some errors, so show them to user
            $result_message.="<div class='alert alert-danger'>";
                $result_message.="{$file_upload_error_messages}";
                $result_message.="<div>Update the record to upload PDF.</div>";
            $result_message.="</div>";
        }
     
        return $result_message;
    }
}
?>