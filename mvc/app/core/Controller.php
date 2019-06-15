<?php

class Controller
{
    protected $session_user_id;
    protected $session_user;
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
            $this->view('home/login',['error_msg' => $error_msg]);
            die;
        }
        $this->session_user_id=$_SESSION['user_id'];
        $this->session_user=$_SESSION['user'];
        $this->session_is_admin=$_SESSION['is_admin'];
        if(isset($_SESSION['user'])==false){
            $this->view('home/login',['error_msg' => $error_msg]);
            die;
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
	    $semaphore_key = $user_id;		// unique integer key for this semaphore (Rush fan!)
	    $semaphore_max = 1;		// The number of processes that can acquire this semaphore
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
        if($this->session_is_admin==true){
            return true;
        }
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
        return $chapter_name;
    }
    protected function get_chapter_name($chapter_id){
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
        $sql->fetch();
        $sql->close();
        return $chapter_name;
    }
    protected function replace_html_special_characters($string){
        $string=str_replace("<","&lt",$string);
        $string=str_replace(">","&gt",$string);
        return $string;
    }
    
}