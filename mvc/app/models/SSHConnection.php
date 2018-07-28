<?php

class SSHConnection
{

    public function connect($host,$port,$user,$pass){

    set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');

    include('phpseclib/Net/SSH2.php');

        $ssh = new Net_SSH2($host,$port);
        $ssh->login($user,$pass);
        return $ssh;
 
       
    }
}