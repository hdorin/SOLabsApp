<?php

class DBConnection
{
    private $connection;
    public function connect(){
        $this->connection = mysqli_connect("127.0.0.1", "bot", "123", "AplicatieSO");/*hadcoded*/
 
        if($this->connection === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
        return $this->connection;
    }
    public function close(){
        mysqli_close($this->connection);
    }
}