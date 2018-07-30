<?php
class Login extends Controller
{
    public function index()
    {
        if(isset($_SESSION["error_msg"])==false){
            $error_msg="";
        }else{
            $error_msg=$_SESSION["error_msg"];
        }
        unset($_SESSION["error_msg"]);
        if(isset($_SESSION['user_name'])==true && empty($error_msg)){
            $error_msg='You are already logged in!';
        }
            $this->view('home/login',['error_msg' => $error_msg]);
    }
    public function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../login";
        header('Location: '.$new_url);
        die;
    }
    public function process(){
        if(empty($user=$_POST["user_field"])==1){
            $this->reload("You did not enter a username!");
        }
        if(empty($pass=$_POST["pass_field"])==1){
            $this->reload("You did not enter a password!");
        }
        
        /*Check if user has an account on our Linux machine*/
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect();
        $sql=$link->prepare('SELECT id FROM users WHERE `user_name`=?');
        $sql->bind_param('s', $user);
        $sql->execute();
        $sql->bind_result($id_aux);
        if(!$sql->fetch()){/*If not, create one*/
            $ssh_connection=$this->model('SSHConnection');
            $ssh_connection->configure('students.info.uaic.ro','22');/*Check external Linux machine, e.g. fenrir*/
            try{
                if(!$ssh_connection->check_user($user,$pass)){
                    $this->reload("Invalid username/password!");
                }
            }catch(Exception $e){
                $this->reload($e->getMessage());
            }
            $ssh_connection=$this->model('SSHConnection');
            $ssh_connection->configure('127.0.0.1','22');
            $ssh_connection->create_user($user,$pass);
            unset($ssh_connection);
            $sql=$link->prepare('INSERT INTO users (user_name,date_created) VALUES (?,now())');
            $sql->bind_param('s', $user);
            $sql->execute();
            
        }
        $db_connection->close();
        /*Authenticate user on our Linux machine*/
        $ssh_connection=$this->model('SSHConnection');
        $ssh_connection->configure('127.0.0.1','22');
        try{
            if(!$ssh_connection->check_user($user,$pass)){
                $this->reload("Invalid username/password!");
            }
        }catch(Exception $e){
            $this->reload($e->getMessage());
        }
        $_SESSION['user_name']=$user;
        die("LOGAT!");
    }
}