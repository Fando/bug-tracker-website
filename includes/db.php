<?php
class dbcon {
    protected $database;

    function __construct(){
        $this->connect();
    }

    protected function connect() {
        $this->database = mysqli_connect("127.0.0.1", "alex", "bugtracker", "bugtracker") or die("<p>Error connecting to the database.</p>");
    }

    function __destruct(){
        if (!isset($this->database)) mysqli_close($this->database);
    }

    function db(){
        if (!isset($this->database)) { 
            $this->connect(); 
        }
        return $this->database;
    }
}
?>