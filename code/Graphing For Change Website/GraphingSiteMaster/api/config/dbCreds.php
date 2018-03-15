<?php
class Database{

  // specify your own database credentials
  private $host = "cosc304.ok.ubc.ca";
  private $db_name = "db_rwalsh";
  private $username = "rwalsh";
  private $password = "33366155";
  public $conn;

  // get the database connection
  public function getConnection(){

    $this->conn = null;

    try{
      $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name."", $this->username, $this->password);
      $this->conn->exec("set names utf8");
      //echo "Successful Database Connection";
    }catch(PDOException $exception){
      echo "Connection error: " . $exception->getMessage();
    }

    return $this->conn;
  }
}
?>