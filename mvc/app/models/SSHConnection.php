<?php

class SSHConnection
{
    private $host,$port;
    private $connection;
    private $execution_user;
    public function configure($host,$port){
        $this->host=$host;
        $this->port=$port;
    }
    public function connect($user,$pass){
        $this->execution_user=$user;
        if (!($this->connection = ssh2_connect($this->host, $this->port))) {
            throw new Exception('Could not establish SSH connection!');
        }
        if (!ssh2_auth_password($this->connection, $user, $pass)){
            unset($this->connection);
            return false;
        }
        return true;
    }
    public function close(){
        //ssh2_disconnect($this->connection);
        unset($this->connection);
    }
    public function execute($command,$use_strace=false){
        $stream = ssh2_exec($this->connection, $command); 
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $out_msg=stream_get_contents($stream_out);
        if($use_strace==true){
            $stream_err = ssh2_fetch_stream($stream,SSH2_STREAM_STDERR);
            $err_msg=stream_get_contents($stream_err);    
            return $err_msg;
        }
        if(empty($out_msg)==true){
            $stream_err = ssh2_fetch_stream($stream,SSH2_STREAM_STDERR);
            $err_msg=stream_get_contents($stream_err);    
            if(empty($err_msg)==false){
                throw new Exception($err_msg);
            }
        }
        return $out_msg;
    }
    public function send_code_file($local_file,$remote_file){
        if(!ssh2_scp_send($this->connection,$local_file, $remote_file)){
            throw new Exception("Could not send file for exection!");
        }
    }
    public function read_file($remote_output_file){
        $stream = ssh2_exec($this->connection, "cat " . $remote_output_file); 
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $out_msg=stream_get_contents($stream_out);
        if(ord($out_msg[0])==10){
            return null;
        }
        return $out_msg;
        
    }
}