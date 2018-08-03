<?php
class Login extends Controller
{
    public function index()
    {
        if(isset($_SESSION["error_msg"])==false){
            if(isset($_SESSION['user'])==true){
                $error_msg='You are already logged in!';
            }else{
                $error_msg="";
            }
        }else{
            $error_msg=$_SESSION["error_msg"];
            unset($_SESSION["error_msg"]);
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
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT id FROM users WHERE `user_name`=?');
        $sql->bind_param('s', $user);
        $sql->execute();
        $sql->bind_result($id_aux);
        if(!$sql->fetch()){/*If not, create one*/
            $config=$this->model('JSONConfig');
            $external_ssh_host=$config->get('external_ssh','host');
            $external_ssh_port=$config->get('external_ssh','port');
            $external_ssh_connection=$this->model('SSHConnection');
            $external_ssh_connection->configure($external_ssh_host,$external_ssh_port);/*Check external Linux machine, e.g. fenrir*/
            try{
                if(!$external_ssh_connection->connect($user,$pass)){
                    $external_ssh_connection->close();
                    $this->reload("Invalid username/password!");
                }
            }catch(Exception $e){
                $this->reload($e->getMessage());
            }
            $external_ssh_connection->close();
            /*The account was found on the external Linux machine, creating one on our Linux machine*/
            $config=$this->model('JSONConfig');
            $ssh_host=$config->get('ssh','host');
            $ssh_port=$config->get('ssh','port');
            $ssh_sudo_user=$config->get('ssh','sudo_user');
            $ssh_sudo_pass=$config->get('ssh','sudo_pass');
            $ssh_newuser_script_path=$config->get('ssh','newuser_script_path');
            $ssh_connection=$this->model('SSHConnection');
            $ssh_connection->configure($ssh_host,$ssh_port);
            try{
                if(!$ssh_connection->connect($ssh_sudo_user,$ssh_sudo_pass)){
                    $ssh_connection->close();
                    $this->reload("Could not access administrator account!");
                }
            }catch(Exception $e){
                $this->reload($e->getMessage());
            }
            $ssh_connection->create_user($user,$pass,$ssh_newuser_script_path);
            $ssh_connection->close();
            $sql=$link->prepare('INSERT INTO users (user_name,date_created) VALUES (?,now())');
            $sql->bind_param('s', $user);
            $sql->execute();
            
        }
        $db_connection->close();
        /*Authenticate user on our Linux machine*/
        $config=$this->model('JSONConfig');
        $ssh_host=$config->get('ssh','host');
        $ssh_port=$config->get('ssh','port');
        $ssh_connection=$this->model('SSHConnection');
        $ssh_connection->configure($ssh_host,$ssh_port);
        try{
            if(!$ssh_connection->connect($user,$pass)){
                $ssh_connection->close();
                $this->reload("Invalid username/password!");
            }
        }catch(Exception $e){
            $this->reload($e->getMessage());
        }
        $ssh_connection->close();
        $_SESSION['user']=$user;
        $_SESSION['pass']=$pass;
        header('Location: ../');/*redict to home controller after login*/
    }
}