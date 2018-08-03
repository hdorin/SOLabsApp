<?php

class JSONConfig
{
    private $path='../app/config.json';
    private $json;
    public function __construct(){
        $json = file_get_contents($this->path);
        $this->string = json_decode($json, true);
    }
    public function get($category,$data){
        $child=$this->string[$category];
        $value=$child[$data];
        return $value;
    }
}

