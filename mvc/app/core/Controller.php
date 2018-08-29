<?php

class Controller
{
    public function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }
    public function view($view, $data = false)
    {
        require_once '../app/views/' . $view . '.php';
    }
    public function check_login(){
        if(isset($_SESSION['user'])==false){
            die('You are not logged in!');
        }
    }
    public function session_extract($session_variable_name){
        if(isset($_SESSION[$session_variable_name])==true){
            $variable=$_SESSION[$session_variable_name];
            unset($_SESSION[$session_variable_name]);
        }else{
            $variable="";
        }
        return $variable;
    }
}