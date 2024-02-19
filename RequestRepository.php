<?php
namespace marianojwl\MediaProcessor {
    class RequestRepository extends Repository {
        /*
        protected $table;
        public function __construct($tableName) {
            parent::__construct();
            $this->table = $tableName;
        }
        */
        public function getById(int $id) {
            $obj = null;
            $query = "SELECT * FROM ".$this->table." WHERE id='".$id."'";
            $result = $this->conn->query($query);
            if($row = $result->fetch_assoc()) {
                $rsr = $this->mp->getMediaSourcesRepository();
                $resource = $rsr->getById($row["media_source_id"]);
                $rsr->closeConnection();

                $tr = $this->mp->getTemplateRepository();
                $template = $tr->getById($row["template_id"]);
                $tr->closeConnection();

                $obj = new Request($row["id"], $resource, $template, $row["status"], $row["processed_path"], $row["processed_thumb_path"], $this);
            }
                
            return $obj;
        }

        public function getNextN(int $n) {
           
            $objs = [];
            $query = "SELECT * FROM ".$this->table." WHERE status='pending' ORDER BY id ASC LIMIT ".$n;
            $result = $this->conn->query($query);
            
            while($row = $result->fetch_assoc()) {
                $rsr = $this->mp->getMediaSourcesRepository();
                $resource = $rsr->getById($row["media_source_id"]);
                //$rsr->closeConnection();
                
                $tr = $this->mp->getTemplateRepository();
                $template = $tr->getById($row["template_id"]);
                
                $newRequest = new Request($row["id"], $resource, $template, $row["status"], $row["processed_path"], $row["processed_thumb_path"], $this);
                $newRequest->setSettings($row["settings"]);
                $template->setRequest($newRequest);
                $objs[] = $newRequest;
                //$tr->closeConnection();
            }
            
            return $objs;
        }
        /*
        public function setStatus(string $status,$request) {
            $sql = "UPDATE ".$this->table." SET status='".$status."' WHERE id='".$request->getId()."'";
            $result = $this->conn->query($sql);
            if($result)
                $request->setStatus($status);
            return $request;
        }
        */
        public function save(Request $request) {
            $sql = "UPDATE ".$this->table." SET status='".$request->getStatus()."', processed_path='".$request->getProcessedPath()."' , processed_thumb_path='".$request->getProcessedThumbPath()."' WHERE id='".$request->getId()."'";
            $result = $this->conn->query($sql);
        }

    }
}