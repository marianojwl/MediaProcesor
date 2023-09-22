<?php
namespace marianojwl {
    class Repository {
        protected $conn;
        public function __construct() {
            $this->conn = new \mysqli("localhost","root","","mediaprocessor");
        }
        public function closeConnection() {
            $this->conn->close();
        }
    }
}