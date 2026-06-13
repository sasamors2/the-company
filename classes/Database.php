<?php

class Database{
    private $severname = "localhost";
    private $username = "root";
    private $password =""; //FOR MAMP, use 'root' as password
    private $dbname = "the_company_shunsuke";
    protected $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->severname, $this->username, $this->password, $this->dbname);

        if($this->conn->connect_error){
            die('Error connecting to the databese:' . $this->conn->connect_error);
        }
    }
}

?>