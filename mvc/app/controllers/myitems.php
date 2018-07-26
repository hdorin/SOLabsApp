<?php

class Myitems extends Controller
{

    public function index()
    {
        if(isset($_SESSION["response"])==false){
            $resp="";
        }else{
            $resp=$_SESSION["response"];
        }

        if (isset($_SESSION['userId'])==false)
    		die('You must first login to access this page!');
        else 
        	$this->view('home/myitems',['resp' => $resp]);

        unset($_SESSION["response"]);
    }

    public function reload($data = '')
    {
        $_SESSION["response"]=$data;
        $newURL = "../myitems";
        header('Location: ' . $newURL);
        die;
    }

    public function process()
    {
        if (empty($_POST["titleField"]) == 1 ||
            empty($_POST["descriptionField"]) == 1 ||
            empty($_POST["keywordsField"]) == 1 ||
            empty($_POST["conditionField"]) == 1 ||
            empty($_POST["brandField"]) == 1 ||
            empty($_POST["countryField"]) == 1 ||
            empty($_POST["curr_priceField"]) == 1 ||
            empty($_POST["next_priceField"]) == 1 ||
            empty($_POST["expires_onField"]) == 1)
        {
            $this->reload("All fields required!");
        }

        $title = $_POST["titleField"];
        $desc = $_POST["descriptionField"];
        $keyw = $_POST["keywordsField"];
        $cond = $_POST["conditionField"];
        $brand = $_POST["brandField"];
        $country = $_POST["countryField"];
        $price = intval($_POST["curr_priceField"]);
        $step = intval($_POST["next_priceField"]);
        $exp = $_POST["expires_onField"]; 
        $image = basename($_FILES["fileField"]["name"]);   

        $this->add_item($title,$desc,$keyw,$cond,$brand,$country,$price,$step,$exp,$image);

        $this->reload("Item added successfully...");
    }

    public function add_item($title,$desc,$keyw,$cond,$brand,$country,$price,$step,$exp,$image){
        $link = $this->auctiox_db_connect();//simplify connection to the database
        
        $next_price = $price + $step;

        $sql = $link->prepare("INSERT INTO products(sellerId, title, description, keywords, `condition`, brand, country, curr_price, next_price, expires_on, image, is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,1)");

        $sql->bind_param('sssssssssss', $_SESSION['userId'], $title,$desc,$keyw,$cond,$brand,$country,$price,$next_price,$exp,$image); 
        $status = $sql->execute();

        if ($status == false){
            $this->reload("SQL query failed...");
            
        }

        mysqli_close($link);
    }

    
}
