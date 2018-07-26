<?php
class AddAddressForm extends Controller
{
    public function index()
    {
    	if(isset($_SESSION["response"])==false){
            $resp="";
        }else{
            $resp=$_SESSION["response"];
        }

        $this->view('home/addAddressForm',['resp' => $resp]);

        unset($_SESSION["response"]);
    }


    public function process()
    {
    	if(empty($_POST["name"])==1 || empty($_POST["phoneno"])==1 || empty($_POST["address"])==1
    		|| empty($_POST["city"])==1 || empty($_POST["county"])==1 || empty($_POST["country"])==1)
    	{
            $this->reload("All fields required!");
        }


    	$name = $_POST["name"];
    	$phoneno = $_POST["phoneno"];
    	$address = $_POST["address"];
    	$city = $_POST["city"];
    	$county = $_POST["county"];
        $country = $_POST["country"];

    	if (preg_match('/[^A-Za-z ]/', $name))
            $this->reload("Name must contain english characters only!");
        
        if (preg_match('/[^0-9]/', $phoneno))
            $this->reload("Phone number must contain numbers only!");

        if (preg_match('/[^A-Za-z ]/', $city))
            $this->reload("City must contain english characters only!");

        if (preg_match('/[^A-Za-z ]/', $county))
            $this->reload("County must contain english characters only!");

        if (preg_match('/[^A-Za-z ]/', $country))
            $this->reload("Country must contain english characters only!");


    	$this->add_address($name,$phoneno,$address,$city,$county,$country);
    	
    	$this->reload("Address added successfully...");
    }

 	public function reload($data=''){
        $_SESSION["response"]=$data;
        $newURL="../addAddressForm";
        header('Location: '.$newURL);
        die;
    }

    public function add_address($name,$phoneno,$address,$city,$county,$country){
        $link = $this->auctiox_db_connect();//simplify connection to the database
 
        $sql = $link->prepare('INSERT INTO addresses(user_id,name,phone_no,address,city,county,country) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $sql->bind_param('sssssss', $_SESSION['userId'], $name,$phoneno,$address,$city,$county,$country); 
        $status = $sql->execute();

        if ($status == false)
            $this->reload("SQL query failed...");

        mysqli_close($link);
    }
}