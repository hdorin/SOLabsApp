<?php
class ModifyCreditCard extends Controller
{
    public function index()
    {
    	if(isset($_SESSION["response"])==false){
            $resp="";
        }else{
            $resp=$_SESSION["response"];
        }

        $this->view('home/ModifyCreditCard',['resp' => $resp]);

        unset($_SESSION["response"]);
    }


    public function process()
    {
    	if(empty($_POST["ccname"])==1 || empty($_POST["ccmonth"])==1
    		|| empty($_POST["ccyear"])==1 || empty($_POST["ccver"])==1)
    	{
            $this->reload("All fields required!");
        }


    	$name = $_POST["ccname"];
    	$number = $_POST["ccnumber"];
    	$expmonth = $_POST["ccmonth"];
    	$expyear = $_POST["ccyear"];
    	$cvv = $_POST["ccver"];

    	if (preg_match('/[^A-Za-z ]/', $name))
            $this->reload("Name must contain english characters only!");

    	if (preg_match('/[^0-9]/', $cvv) || strlen((string)$cvv) != 3)
            $this->reload("CVV not valid!");

    	$this->update_cc($number,$name,$expmonth,$expyear,$cvv);

    	$resp = "Your " . $cc_checked . "  credit card was updated successfully...";
    	
    	$this->reload($resp);
    }

 	public function reload($data=''){
        $_SESSION["response"]=$data;
        $newURL="../ModifyCreditCard";
        header('Location: '.$newURL);
        die;
    }

    public function update_cc($number,$name,$expmonth,$expyear,$cvv){
        $link = $this->auctiox_db_connect();//simplify connection to the database
 
        $sql = $link->prepare('UPDATE creditcards SET name=?, exp_month=?, exp_year=?, cvv=? WHERE number='.$number);
        $sql->bind_param('ssss', $name, $expmonth, $expyear, $cvv); 
        $status = $sql->execute();

        if ($status == false)
            $this->reload("SQL query failed...");

        $sql->close();
        mysqli_close($link);
    }
}