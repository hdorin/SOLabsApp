<?php

class Controller
{
    protected $session_user_id;
    protected $session_user;
    protected $session_pass;
    protected $session_is_admin;
    protected $semaphore;
    protected function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }
    protected function view($view, $data = false)
    {
        require_once '../app/views/' . $view . '.php';
    }
    protected function check_login(){
        if(isset($_SESSION['user'])==false){
            die('You are not logged in!');
        }
        $this->session_user_id=$_SESSION['user_id'];
        $this->session_user=$_SESSION['user'];
        $this->session_pass=$_SESSION['pass'];
        $this->session_is_admin=$_SESSION['is_admin'];
        if(isset($_SESSION['user'])==false){
            die('You are not logged in!');
        }
    }
    protected function session_extract($session_variable_name,$unset=false){
        if(isset($_SESSION[$session_variable_name])==true){
            $variable=$_SESSION[$session_variable_name];
            if($unset==true){
                unset($_SESSION[$session_variable_name]);
            }
        }else{
            $variable="";
        }
        return $variable;
    }
    protected function my_sem_acquire($user_id){
        // exclusive control
	    $semaphore_key = 2112;		// unique integer key for this semaphore (Rush fan!)
	    $semaphore_max = $user_id;		// The number of processes that can acquire this semaphore
	    $semaphore_permissions = 0666;	// Unix style permissions for this semaphore
	    $semaphore_autorelease = 1;	// Auto release the semaphore if the request shuts down
 
        // open a new or get an existing semaphore
	    $this->semaphore = sem_get($semaphore_key, $semaphore_max, $semaphore_permissions, $semaphore_autorelease);
	    if(!$this->semaphore) 
	    {
    		die( "Failed to get semaphore!");
	    }
        // acquire exclusive control	
        sem_acquire($this->semaphore);
    }
    protected function my_sem_release(){
        sem_release($this->semaphore);
    }
    protected function check_chapter_posted($chapter_id){
        $config=$this->model('JSONConfig');
        $db_host=$config->get('db','host');
        $db_user=$config->get('db','user');
        $db_pass=$config->get('db','pass');
        $db_name=$config->get('db','name');
        $db_connection=$this->model('DBConnection');
        $link=$db_connection->connect($db_host,$db_user,$db_pass,$db_name);
        $sql=$link->prepare("SELECT `name` FROM chapters WHERE `status`='posted' AND id=?");
        $sql->bind_param('i',$chapter_id);
        $sql->execute();
        $sql->bind_result($chapter_name);
        if(!$sql->fetch()){
            $sql->close();
            die("You cannot access this!");
        }
        $sql->close();
    }
    
}