<?php
class AddCreditCardForm extends Controller
{
    public function index()
    {
    	if(isset($_SESSION["response"])==false){
            $resp="";
        }else{
            $resp=$_SESSION["response"];
        }

        $this->view('home/AddCreditCardForm',['resp' => $resp]);

        unset($_SESSION["response"]);
    }

	function check_cc($cc, $extra_check = FALSE){
    $cards = array(
        "visa" => "(4\d{12}(?:\d{3})?)",
        "amex" => "(3[47]\d{13})",
        "jcb" => "(35[2-8][89]\d\d\d{10})",
        "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
        "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
        "mastercard" => "(5[1-5]\d{14})",
        "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
    );
    $names = array("Visa", "American Express", "JCB", "Maestro", "Solo", "Mastercard", "Switch");
    $matches = array();
    $pattern = "#^(?:".implode("|", $cards).")$#";
    $result = preg_match($pattern, str_replace(" ", "", $cc), $matches);
    if($extra_check && $result > 0){
        $result = (validatecard($cc))?1:0;
    }
    return ($result>0)?$names[sizeof($matches)-2]:FALSE;
}

    public function process()
    {
    	if(empty($_POST["ccname"])==1 || empty($_POST["ccnumber"])==1 || empty($_POST["ccmonth"])==1
    		|| empty($_POST["ccyear"])==1 || empty($_POST["ccver"])==1)
    	{
            $this->reload("All fields required!");
        }


    	$name = $_POST["ccname"];
    	$number = $_POST["ccnumber"];
    	$copy = $number;

    	$expmonth = $_POST["ccmonth"];
    	$expyear = $_POST["ccyear"];
    	$cvv = $_POST["ccver"];

    	if (preg_match('/[^A-Za-z ]/', $name))
            $this->reload("Name must contain english characters only!");
    	
    	$cc_checked = $this->check_cc($copy);

    	if ($cc_checked == FALSE)
            $this->reload("Credit card number is not VALID!");

    	if (preg_match('/[^0-9]/', $cvv) || strlen((string)$cvv) != 3)
            $this->reload("CVV not valid!");

    	$this->add_cc($number,$name,$expmonth,$expyear,$cvv);

    	$resp = "CC detected: " . $cc_checked . " -- Credit card added successfully...";
    	
    	$this->reload($resp);
    }

 	public function reload($data=''){
        $_SESSION["response"]=$data;
        $newURL="../addCreditCardForm";
        header('Location: '.$newURL);
        die;
    }

    public function add_cc($number,$name,$expmonth,$expyear,$cvv){
        $link = $this->auctiox_db_connect();//simplify connection to the database
 
        $sql = $link->prepare('INSERT INTO creditcards VALUES (?, ?, ?, ?, ?, ?)');
        $sql->bind_param('ssssss', $_SESSION['userId'], $number, $name, $expmonth, $expyear, $cvv); 
        $status = $sql->execute();

        if ($status == false)
            $this->reload("SQL query failed...");

        mysqli_close($link);
    }
}