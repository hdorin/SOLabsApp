<?php
class Logout extends Controller
{
    public function index()
    {
        unset($_SESSION["user"]);
        unset($_SESSION["pass"]);
        header('Location: ../public');/*redict to home controller after login*/
    }
    
}