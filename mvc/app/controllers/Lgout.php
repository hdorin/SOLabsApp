<?php
class Logout extends Controller
{
    public function delete_cookie_db($cookie_value=""){
        $link=$this->auctiox_db_connect();
        $sql = $link->prepare('DELETE FROM COOKIES WHERE value=?');
        $sql->bind_param('s', $cookie_value);
        $sql->execute();
        mysqli_close($link);
    }
    public function index()
    {
        if(isset($_SESSION["userId"])==true){
            unset($_SESSION["userId"]);
            unset($_SESSION["userName"]);
        }
        
        if(isset($_COOKIE["AuthenticationId"])==true){
            $cookie_value=md5($_COOKIE["AuthenticationId"]);
            $this->delete_cookie_db($cookie_value);
            setcookie("AuthenticationId", "", time() - 3600, "/");
        }
        $newURL="index";
        header('Location: '.$newURL);
    }
    
}