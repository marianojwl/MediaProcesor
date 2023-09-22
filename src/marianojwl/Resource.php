<?php
namespace marianojwl {
    class Resource {
        protected $id;
        protected $foreign_id;
        protected $path;
        protected $mime_type;
        protected $original;
        protected $template_id;

        public function __construct($id, int $foreign_id, string $path, string $mime_type, bool $original, $template_id) {
            $this->id = $id;
            $this->foreign_id = $foreign_id;
            $this->path =  $path;
            $this->mime_type = $mime_type;
            $this->original = $original;
            $this->template_id = $template_id;
        }
        

        public function getTemplateId()
        {
                return $this->template_id;
        }

        public function setTemplateId($template_id): self
        {
                $this->template_id = $template_id;

                return $this;
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
         * Get the value of foreign_id
         */
        public function getForeignId()
        {
                return $this->foreign_id;
        }

        /**
         * Set the value of foreign_id
         */
        public function setForeignId($foreign_id): self
        {
                $this->foreign_id = $foreign_id;

                return $this;
        }

        /**
         * Get the value of path
         */
        public function getPath()
        {
                return $this->path;
        }

        /**
         * Set the value of path
         */
        public function setPath($path): self
        {
                $this->path = $path;

                return $this;
        }


        public function getMimeType()
        {
                return $this->mime_type;
        }

        public function setMimeType($mime_type): self
        {
                $this->mime_type = $mime_type;

                return $this;
        }

        public function isOriginal()
        {
                return $this->original;
        }

        public function setOriginal($original): self
        {
                $this->original = $original;

                return $this;
        }
        
    }

}