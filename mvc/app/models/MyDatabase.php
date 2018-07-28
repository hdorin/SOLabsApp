<?php

class MyDatabase
{
    public function connect(){
        $link = mysqli_connect("localhost", "bot", "12345", "auctiox_db");
 
        if($link === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
        return $link;
    }
}