<?php
//require_once '../core/Controller.php';

class Products extends Controller
{
    public function index()
    {
        $link = new DataBase();

        $sth = $link->prepare("SELECT * FROM products");
        $sth->execute();
        $products = $sth->fetchAll();

        $this->view('home/products', ['products' => $products]);
    }

}