<?php
class Register extends Controller
{
    public function index()
    {
        if(isset($_SESSION["error"])==false){
            $error="";
        }else{
            $error=$_SESSION["error"];
        }
        if(isset($_SESSION['userId'])==false){
            $this->view('home/register',['error' => $error]);
        }else{
            echo "You must first logut!";
        }
        unset($_SESSION["error"]);
    }
    public function reload($data=''){
        $_SESSION["error"]=$data;
        $newURL="../register";
        header('Location: '.$newURL);
        die;
    }
    
    public function process(){
        if(empty($_POST["nameField"])==1 ||
            empty($_POST["lnameField"])==1 ||
            empty($_POST["emailField"])==1 ||
            empty($_POST["dobField"])==1 ||
            empty($_POST["passField1"])==1 ||
            empty($_POST["passField2"])==1)
        {
            $this->reload("All fields required!");
        }
        $name=$_POST["nameField"];
        $lname=$_POST["lnameField"];
        $email=$_POST["emailField"];
        $dob=$_POST["dobField"];
        $pass1=$_POST["passField1"];
        $pass2=$_POST["passField2"];

        if(strcmp($pass1,$pass2)!=0){
            $this->reload("Passwords do not match!");
            
        }
        $pass1=md5($pass1);
        $this->create_account($name,$lname,$email,$dob,$pass1);
    }
    public function create_account($name,$lname,$email,$dob,$pass){
        $link = $this->auctiox_db_connect();//simplify connection to the database
 
        $sql = $link->prepare('INSERT INTO USERS (name,lname,email,dob,passw,type,date_created) VALUES (?,?,?,?,?, 1, now())');

        $sql->bind_param('sssss', $name,$lname,$email,$dob,$pass); 
        if($sql->execute() == true){
            $newURL="../login";
            $_SESSION["error"]="Please login!";
            header('Location: '.$newURL);
            $_SESSION["userId"]=$userId;
        }else{
            $this->reload("Email address already in use!");
        }
        
        mysqli_close($link);
    }


}