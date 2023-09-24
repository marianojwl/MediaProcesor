<?php
namespace marianojwl\MediaProcessor {
    class TemplateRepository extends Repository {
        protected $table;
        public function __construct() {
            parent::__construct();
            $this->table = "templates";
        }
        public function getById(int $id) {
            $obj = null;
            $query = "SELECT * FROM ".$this->table." WHERE id='".$id."'";
            $result = $this->conn->query($query);
            if($row = $result->fetch_assoc()) {
                switch($row["type"]) {
                    case "IMGSCA":
                        $obj = new ScalingTemplate($row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
                        break;
                    case "SOVER":
                        $obj = new SimpleOverlayTemplate($row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
                        break;
                    case "ROVER":
                        $obj = new ResizedOverlayTemplate($row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
                        break;
                    default:
                        return $obj;
                }
                //$obj = new Template($row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
            }
                
            return $obj;
        }
    }
}