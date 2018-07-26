<?php
class generate_feed extends Controller {
    public function index() {

        $link = $this->auctiox_db_connect();

        $sql = mysqli_query($link, "SELECT * FROM products");
        $data = mysqli_fetch_all($sql, MYSQLI_ASSOC);
        $result = array($data);

        $this->view('home/Feed/generate_feed',['result' => $result]);
    }



}