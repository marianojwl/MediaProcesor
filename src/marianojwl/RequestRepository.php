<?php
namespace marianojwl {
    class RequestRepository extends Repository {
        protected $table;
        public function __construct() {
            parent::__construct();
            $this->table = "requests";
        }
        public function getById(int $id) {
            $obj = null;
            $query = "SELECT * FROM ".$this->table." WHERE id='".$id."'";
            $result = $this->conn->query($query);
            if($row = $result->fetch_assoc()) {
                $rsr = new ResourceRepository();
                $resource = $rsr->getById($row["resouce_id"]);
                $rsr->closeConnection();

                $tr = new TemplateRepository();
                $template = $tr->getById($row["template_id"]);
                $tr->closeConnection();

                $obj = new Request($row["id"],$row["foreign_id"], $resource,$template,$row["status"]);
            }
                
            return $obj;
        }

        public function getNextN(int $n) {
            $objs = [];
            $query = "SELECT * FROM ".$this->table." WHERE status IS NULL ORDER BY id ASC LIMIT ".$n;
            $result = $this->conn->query($query);
            while($row = $result->fetch_assoc()) {
                $rsr = new ResourceRepository();
                $resource = $rsr->getById($row["resource_id"]);
                $rsr->closeConnection();

                $tr = new TemplateRepository();
                $template = $tr->getById($row["template_id"]);
                $objs[] = new Request($row["id"],$row["foreign_id"], $resource,$template,$row["status"]);
                $tr->closeConnection();
            }
                
            return $objs;
        }

        public function setStatus(string $status,$request) {
            $sql = "UPDATE ".$this->table." SET status='".$status."' WHERE id='".$request->getId()."'";
            $result = $this->conn->query($sql);
            if($result)
                $request->setStatus($status);
            return $request;
        }
    }
}