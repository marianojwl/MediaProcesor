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
        public function getAllAsArray() {
            $sql = "SELECT * FROM " . $this->table;
            $result = $this->conn->query($sql);
            $rows = array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            return $rows;
        }
        public function getAllActiveAsArray() {
            $sql = "SELECT * FROM " . $this->table . " WHERE active=1";
            $result = $this->conn->query($sql);
            $rows = array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            return $rows;
        }

        public function update($id, $data) {
            $sql = "UPDATE " . $this->table . " SET ";
            $i = 0;
            foreach($data as $key => $value) {
                if($i > 0) {
                    $sql .= ", ";
                }
                $sql .= $key . "='" . $value . "'";
                $i++;
            }
            $sql .= " WHERE id=" . $id;
            return $this->conn->query($sql);
        }
    }
}