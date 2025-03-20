<?php
class DBConnection {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = 'm4ster';
    private $dbName = 'online_course';
    public $conn;

    // Constructor: Establishes the database connection
    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbName);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Executes a standard SQL query
    public function query($sql) {
        return $this->conn->query($sql);
    }

    // Prepares a SQL statement for execution
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    // Closes the database connection
    public function close() {
        $this->conn->close();
    }
}
?>
