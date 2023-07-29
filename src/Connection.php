<?php
class Connection {
    private $conn;

    public function __construct($host, $user, $pass, $db) {
        $this->conn = new mysqli($host, $user, $pass, $db);

        if ($this->conn->connect_error) {
            die("Connection failed {$this->conn->connect_error}");
            return;
        }
    }

    protected function getConn() 
    {
        return $this->conn;
    }
}