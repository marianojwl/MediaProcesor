<?php
namespace marianojwl\MediaProcessor {
    class Template {
        protected $id;
        protected $description;
        protected $sufix;
        protected $type;
        protected $settings;
        protected $mime_type;
        protected $quality;
        protected $mp;

        public function __construct(MediaProcessor $mp, int $id, string $description, string $sufix, $type, $settings) {
            $this->mp = $mp;
            $this->id = $id;
            $this->description = $description;
            $this->sufix = $sufix;
            $this->type = $type;
            $this->settings = $settings;
            $setts = json_decode( $settings , true);
            $this->quality = $setts["quality"]??100;
        }

        protected function imageCreateFromResource(Resource $r) {
                $path_to_file = str_replace( dirname($_SERVER["SCRIPT_NAME"]). '/', "", $r->getPath() );
                //$path_to_file =  $r->getPath();
                //echo $path_to_file;
                $originalImage = null;
                switch($r->getMimeType()) {
                case "image/jpeg":
                        $originalImage = imagecreatefromjpeg($path_to_file);
                        break;
                case "image/png":
                        $originalImage = imagecreatefrompng($path_to_file);
                        break;
                }
                return $originalImage;
        }

        protected function imageSave($gdImage, $outputPath, $mime_type) {
                if($outputPath === null)
                        header('Content-type:'.$mime_type);
                else
                        $outputPath = str_replace( dirname($_SERVER["SCRIPT_NAME"]). '/' , "", $outputPath);
                //echo $outputPath;
                switch($mime_type) {
                        case "image/jpeg":
                                if($outputPath === null)
                                        imagejpeg($gdImage); //, $outputPath); //, $this->quality);
                                else
                                        imagejpeg($gdImage, $outputPath);
                                break;
                        case "image/png":
                                if($outputPath === null)
                                        imagepng($gdImage); //, $outputPath); //, ceil( $this->quality * 9 / 100 ) );
                                else
                                        imagepng($gdImage, $outputPath);
                                break;
                        case "image/gif":
                                if($outputPath === null)
                                        imagegif($gdImage); //, $outputPath); //, ceil( $this->quality * 9 / 100 ) );
                                else
                                        imagegif($gdImage, $outputPath);
                                break;
                }
        }


        public function process(Request $request)  {
                return $this->prepare($request);
        }
        public function prepare(Request $r)  {
                return $r; //->getResource();
        }
        
        public function storeResource($resource) {
                $rsr = $this->mp->getRequestRepository();
                $resource = $rsr->store($resource);
                $rsr->closeConnection();
                return $resource;
        }
        
        public function getOutputPath(Resource $r, string $extension) {
                $path_to_file = $r->getPath();
                $pathInfo = pathinfo($path_to_file);
                //$directory = dirname($path_to_file);
                $directory = $this->mp->getProcessedDir();
                $filename = $pathInfo['filename'];
                //$extension = $pathInfo['extension'];
                $outputPath = $directory . $filename . '_' . $this->sufix . '.' . $extension;
                return $outputPath;
            }
        /**
         * Get the value of id
         */
        public function getId()
        {
                return $this->id;
        }

        /**
         * Set the value of id
         */
        public function setId($id): self
        {
                $this->id = $id;

                return $this;
        }

        /**
         * Get the value of description
         */
        public function getDescription()
        {
                return $this->description;
        }

        /**
         * Set the value of description
         */
        public function setDescription($description): self
        {
                $this->description = $description;

                return $this;
        }

        /**
         * Get the value of sufix
         */
        public function getSufix()
        {
                return $this->sufix;
        }

        /**
         * Set the value of sufix
         */
        public function setSufix($sufix): self
        {
                $this->sufix = $sufix;

                return $this;
        }

        /**
         * Get the value of type
         */
        public function getType()
        {
                return $this->type;
        }

        /**
         * Set the value of type
         */
        public function setType($type): self
        {
                $this->type = $type;

                return $this;
        }

        /**
         * Get the value of settings
         */
        public function getSettings()
        {
                return $this->settings;
        }

        /**
         * Set the value of settings
         */
        public function setSettings($settings): self
        {
                $this->settings = $settings;

                return $this;
        }
        /**
         * Get the value of mime_type
         */
        public function getMimeType()
        {
                return $this->mime_type;
        }

        /**
         * Set the value of mime_type
         */
        public function setMimeType($mime_type): self
        {
                $this->mime_type = $mime_type;

                return $this;
        }
        /**
         * Get the value of quality
         */
        public function getQuality()
        {
                return $this->quality;
        }

        /**
         * Set the value of quality
         */
        public function setQuality($quality): self
        {
                $this->quality = $quality;

                return $this;
        }
    }





}