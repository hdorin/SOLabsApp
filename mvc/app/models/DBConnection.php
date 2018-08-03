<?php

class DBConnection
{
    private $connection;
    public function connect($host,$user,$pass,$db_name){
        $this->connection = mysqli_connect($host, $user, $pass, $db_name);
 
        if($this->connection === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
        return $this->connection;
    }
    public function close(){
        mysqli_close($this->connection);
    }
}