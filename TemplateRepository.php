<?php
namespace marianojwl\MediaProcessor {
    class TemplateRepository extends Repository {
        /*
        protected $table;
        public function __construct() {
            parent::__construct();
            $this->table = $tableName;
        }
        */
        public function getById(int $id) {
            $obj = null;
            $query = "SELECT * FROM ".$this->table." WHERE id='".$id."'";
            $result = $this->conn->query($query);
            if($row = $result->fetch_assoc()) {
                switch($row["type"]) {
                    case "IMGSCA":
                        $obj = new ScalingTemplate($this->mp, $row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
                        break;
                    case "SOVER":
                        $obj = new SimpleOverlayTemplate($this->mp, $row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
                        break;
                    case "ROVER":
                        $obj = new ResizedOverlayTemplate($this->mp, $row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
                        break;
                    case "PUTONTOP":
                        $obj = new PutOnTopTemplate($this->mp, $row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
                        break;
                    case "RBLUR":
                        $obj = new ResizedBlurTemplate($this->mp, $row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
                        break;
                    case "SIDEO":
                        $obj = new OverlaySideTemplate($this->mp, $row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
                        break;
                    case "SIDEO2":
                        $obj = new OverlaySideTemplate2($this->mp, $row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
                        break;
                    case "TEXTS":
                        $obj = new TextTemplate($this->mp, $row["id"],$row["description"],$row["sufix"],$row["type"],$row["settings"]);
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