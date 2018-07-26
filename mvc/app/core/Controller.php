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
    public function auctiox_db_connect(){
        $link = mysqli_connect("localhost", "bot", "12345", "auctiox_db");
 
        if($link === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
        return $link;
    }
}