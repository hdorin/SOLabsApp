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
    private function reload($data=''){
        $_SESSION["error_msg"]=$data;
        $new_url="../login";
        header('Location: '.$new_url);
        $this->my_sem_release();
        die;
    }
    private function generate_random_str() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 20; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function process(){
        $this->my_sem_acquire(-1);
        if(empty($user=$_POST["user_field"])==1){
            $this->reload("You did not enter a username!");
        }
        if(empty($pass=$_POST["pass_field"])==1){
            $this->reload("You did not enter a password!");
        }
        /*Check if user has an account on  our Linux machine*/
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $ssh_connection=$this->model('SSHConnection');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare('SELECT id,is_admin,pass_hash FROM users WHERE `user_name`=?');
        $sql->bind_param('s', $user);
        $sql->execute();
        $sql->bind_result($user_id,$is_admin,$pass_hash);
        if(!$sql->fetch()){/*If not, create one*/
            $external_ssh_check=$config->get('external_ssh','check');
            if($external_ssh_check=="true"){/*false = does not ckeck the external ssh connection*/
                $external_ssh_host=$config->get('external_ssh','host');
                $external_ssh_port=$config->get('external_ssh','port');
                $ssh_connection->configure($external_ssh_host,$external_ssh_port);/*Check external Linux machine, e.g. fenrir*/
                try{    
                    if(!$ssh_connection->connect($user,$pass)){
                        $ssh_connection->close();
                        $this->reload("Invalid username/password!");
                    }   
                }catch(Exception $e){
                    $this->reload($e->getMessage());
                }
                $ssh_connection->close();
            }
            /*The account was found on the external Linux machine, creating one on our Linux machine*/
            $pass_hash=password_hash($pass, PASSWORD_DEFAULT);
            $sql=$link->prepare('INSERT INTO users (`user_name`,date_created,pass_hash) VALUES (?,now(),?)');
            $sql->bind_param('ss', $user,$pass_hash);
            $sql->execute();
            $sql->close();    
        }else{
            $sql->close();
            if(password_verify($pass,$pass_hash)==false){/*The password user provided does not match the hashed one*/
                $external_ssh_check=$config->get('external_ssh','check');
                if($external_ssh_check=="true"){/*false = does not ckeck the external ssh connection*/
                    $external_ssh_host=$config->get('external_ssh','host');
                    $external_ssh_port=$config->get('external_ssh','port');
                    $ssh_connection->configure($external_ssh_host,$external_ssh_port);/*Check external Linux machine, e.g. fenrir*/
                    try{    
                        if(!$ssh_connection->connect($user,$pass)){
                            $ssh_connection->close();
                            $this->reload("Invalid username/password!");
                        }   
                    }catch(Exception $e){
                        $this->reload($e->getMessage());
                    }
                    $ssh_connection->close();
                }
                /*Rehash the password for the user*/
                $pass_hash=password_hash($pass, PASSWORD_DEFAULT);
                $sql=$link->prepare('UPDATE users SET pass_hash=? WHERE `user_name`=?');
                $sql->bind_param('ss',$pass_hash,$user);
                $sql->execute();
                $sql->close();                
            }
        }
        $db_connection->close();
        /*Authenticate user on our Linux machine*/
        $ssh_host=$config->get('ssh','host');
        $ssh_port=$config->get('ssh','port');
        $ssh_user=$config->get('ssh','user');
        $ssh_pass=$config->get('ssh','pass');
        $ssh_connection->configure($ssh_host,$ssh_port);
        try{
            
            if(!$ssh_connection->connect($ssh_user,$ssh_pass)){
                $ssh_connection->close();
                $this->reload("Could not connect via SSH!");
            }
        }catch(Exception $e){
            $this->reload($e->getMessage());
        }
        $ssh_connection->close();
        $_SESSION['user_id']=$user_id;
        $_SESSION['user']=$user;
        $_SESSION['is_admin']=$is_admin;
        header('Location: ../');/*redirect to home controller after login*/
        $this->my_sem_release();
    }
}