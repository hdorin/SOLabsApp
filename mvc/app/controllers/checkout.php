<?php
class Checkout extends Controller
{
    private $winnerId;
    private $productTitle;
    private $productDesc;
    private $productPrice;
    private $productId;
    private $addressId;
    public function index($data='')
    {
        if(isset($_SESSION['userId'])==false){
            echo "You must login!";
            die;
        }
        $this->productId=$data;
        $this->get_products_details();
        $this->view('/home/checkout',['productTitle' => $this->productTitle,'productDesc' => $this->productDesc,'productCondition' => $this->productCondition,'productPrice' => $this->productPrice,'productId' => $this->productId,'winnerId' => $this->winnerId, 'productImage' => $this->productImage]);
    }

    public function get_products_details(){
        $link = $this->auctiox_db_connect();
 
        $sql = $link->prepare('SELECT winnerId,title,description,`condition`,curr_price,image FROM products WHERE id=?');
        $sql->bind_param('s', $this->productId); 
        $sql->execute();
        $sql->bind_result($this->winnerId,$this->productTitle,$this->productDesc,$this->productCondition,$this->productPrice,$this->productImage);
        $sql->fetch();
        if($this->winnerId!=$_SESSION['userId']){
            echo "You can't access this!";
            die;
        }
        mysqli_close($link);
        
    }


}