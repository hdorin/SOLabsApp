<?php
//session_start();
class Home extends Controller
{
    public function index()
    {
        $this->check_authentication_cookie();
        if(isset($_SESSION["userId"])==true){
            $userId=$_SESSION["userId"];
            $userName=$_SESSION["userName"];
        }else{
            $userName="";
            $userId="";
        }
        $this->view('home/index');
        
    }
    public function check_authentication_cookie(){
        if(isset($_COOKIE["AuthenticationId"])) {
            $cookie_value=$_COOKIE["AuthenticationId"];
            $cookie_value=md5($cookie_value);
            $link = $this->auctiox_db_connect();
 
            $sql = $link->prepare('SELECT user_id,name FROM cookies c JOIN users u ON u.id=c.user_id WHERE value=?');
            $sql->bind_param('s', $cookie_value); 
            $sql->execute();
            $sql->bind_result($userId,$userName);
            $sql->fetch();
            mysqli_close($link);

            if(empty($userId)){
                //echo "User not found!";
            }else{
                $_SESSION["userId"]=$userId;
                $_SESSION["userName"]=$userName;
            }
        }
        
    }

}