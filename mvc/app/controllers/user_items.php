<?php

class user_items extends Controller
{
    public function index()
    {

        $this->view('home/User_items/user_items', []);
    }


    public function reload($data=''){
        $_SESSION["error"]=$data;
        $newURL="../user_items";
        header('Location: '.$newURL);
        die;
    }

/*
    public function process()
    {
        $link = $this->auctiox_db_connect();

        if ($_SESSION["tab_option"] == "my_items")
            $sql = "SELECT * FROM products WHERE winnerId = ".$_SESSION['userId'];
        else if ($_SESSION["tab_option"] == "my_uploaded_items")
            $sql = "SELECT * FROM products WHERE sellerId = ".$_SESSION['userId'];

        $result = mysqli_query($link, $sql);
        $link->close();
        echo '<pre>';
        $this->view('home/User_items/user_items', ['result' => $result]);
    }
*/

}