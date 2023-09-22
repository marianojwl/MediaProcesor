<?php
namespace marianojwl {
    class Template {
        protected $id;
        protected $description;
        protected $sufix;
        protected $type;
        protected $settings;

        public function __construct(int $id, string $description, string $sufix, $type, $settings) {
            $this->id = $id;
            $this->description = $description;
            $this->sufix = $sufix;
            $this->type = $type;
            $this->settings = $settings;
        }

        public function process(Resource $r)  {
            return $r;
        }
        public function storeResource($resource) {
                $rsr = new ResourceRepository();
                $resource = $rsr->store($resource);
                $rsr->closeConnection();
                return $resource;
        }

        public function getOutputPath(Resource $r) {
                $path_to_file = $r->getPath();
                $pathInfo = pathinfo($path_to_file);
                $directory = dirname($path_to_file);
                $filename = $pathInfo['filename'];
                $extension = $pathInfo['extension'];
                $outputPath = $directory . '/' . $filename . '_' . $this->sufix . '.' . $extension;
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
        
    }


}