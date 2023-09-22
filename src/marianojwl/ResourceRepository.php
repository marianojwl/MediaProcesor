<?php
namespace marianojwl {
    class ResourceRepository extends Repository {
        protected $table;
        public function __construct() {
            parent::__construct();
            $this->table = "resources";
        }
        public function getById(int $id) {
            $obj = null;
            $query = "SELECT * FROM ".$this->table." WHERE id='".$id."'";
            $result = $this->conn->query($query);
            if($row = $result->fetch_assoc())
                $obj = new Resource($row["id"],$row["foreign_id"],$row["path"],$row["mime_type"],$row["original"]=="1", $row["template_id"] );
            return $obj;
        }

        public function store(Resource $resource) {
            $sql = "INSERT INTO ".$this->table." (foreign_id,path,mime_type,original,template_id) VALUES ('".$resource->getForeignId()."','".$resource->getPath()."','".$resource->getMimeType()."','".($resource->isOriginal()?1:0)."','".$resource->getTemplateId()."')";
            $result = $this->conn->query($sql);
            if($result)
                return $resource->setId( $this->conn->insert_id );
            else
                return null;
 
        }
    }
}