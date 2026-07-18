<?php

    class Database{
        private $host = 'localhost';
        private $user = 'root';
        private $password = '';
        private $dbname = 'studentprofiledb';
        private $conn;

        public function __construct(){
            $this->connect();
        }

        private function connect(){
            try{
                $this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbname);

                if($this->conn->connect_error){
                    die("Connection failed " . $this->conn->connect_error);
                }
            }catch(Exception $e){
                die("Connection failed " . $e->getMessage());
            }
            $this->conn->set_charset("utf8mb4");    
        }

        public function getConnection(){
            return $this->conn;
        }

        public function closeConnection(){
            if($this->conn){
                $this->conn->close();
            }
        }
    }

    //========= bootstrap the database ========== //
    try{
        $db = new Database();
        $con = $db->getConnection();
    }catch(Exception $e){
        throw new Exception("Error " . $e->getMessage(), 500);
    }