<?php
class Logout extends Controller
{
    public function index()
    {
        unset($_SESSION["user_name"]);
        header('Location: ../public');/*redict to home controller after login*/
    }
    
}