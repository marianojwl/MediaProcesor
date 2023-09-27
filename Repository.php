<?php
namespace marianojwl\MediaProcessor {
    class Repository {
        protected $mp;
        protected $conn;
        protected $table;
        public function __construct(MediaProcessor $mp, $tableName) {
            $this->mp = $mp;
            $this->conn = $mp->getConn();
            $this->table = $tableName;
        }
        public function closeConnection() {
            $this->conn->close();
        }
    }
}