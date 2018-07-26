<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 6/8/2018
 * Time: 7:48 PM
 */

class All_my_items extends Controller
{
    public function index()
    {
        $this->view('home/my_items',[]);
    }



}