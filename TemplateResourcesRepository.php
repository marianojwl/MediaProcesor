<?php
namespace marianojwl\MediaProcessor {
    class TemplateResourcesRepository extends Repository {
        public function getById(int $id) {
            $obj = null;
            $query = "SELECT * FROM ".$this->table." WHERE id='".$id."'";
            $result = $this->conn->query($query);
            if($row = $result->fetch_assoc())
                $obj = new Resource($row["id"], null ,$row["path"],$row["mime_type"], null, null );
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