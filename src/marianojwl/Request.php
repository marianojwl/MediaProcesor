<?php
namespace marianojwl {
    class Request {
        protected $id;
        protected $foreign_id;
        protected $resource;
        protected $template;
        protected $status;

        public function __construct($id, $foreign_id, $resource, $template, $status) {
            $this->id = $id;
            $this->foreign_id = $foreign_id;
            $this->resource = $resource;
            $this->template = $template;
            $this->status = $status;
        }
        public function process() {
            $resource = $this->template->process($this->resource);
            if($resource !== null)
                $this->setProcessed();
            return $resource;
        }
        public function setProcessed() {
            $rr = new RequestRepository();
            return $rr->setStatus('processed',$this);;
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
         * Get the value of resource
         */
        public function getResource()
        {
                return $this->resource;
        }

        /**
         * Set the value of resource
         */
        public function setResource($resource): self
        {
                $this->resource = $resource;

                return $this;
        }

        /**
         * Get the value of template
         */
        public function getTemplate()
        {
                return $this->template;
        }

        /**
         * Set the value of template
         */
        public function setTemplate($template): self
        {
                $this->template = $template;

                return $this;
        }

        /**
         * Get the value of status
         */
        public function getStatus()
        {
                return $this->status;
        }

        /**
         * Set the value of status
         */
        public function setStatus($status): self
        {
                $this->status = $status;

                return $this;
        }
    }


}