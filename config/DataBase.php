<?php

class DataBase {
    //Database Properties
    private $host = 'localhost';
    private $db_name = 'test';
    private $username = 'root';
    private $password = '';
    private $connection = null;
    // function for making connection
    public function connect(){
        try{
            $this->connection=new PDO('mysql:host='.$this->host.';dbname='.$this->db_name,$this->username,$this->password);
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
        return $this->connection;
    }
}