<?php
namespace marianojwl\MediaProcessor {
    class MediaProcessor {
        protected $requestQueue;
        protected $uploadsDir;
        protected $processedDir;
        protected $queueLimit;

        protected $mediaSourcesTableName;
        protected $templatesTableName;
        protected $templateResourcesTableName;
        protected $requestsTableName;
        protected $conn;

        protected $requestRepository;
        protected $templateRepository;
        protected $mediaSourcesRepository;
        protected $templateResourcesRepository;


        public function __construct(int $queueLimit = 10, string $uploadsDir = "./uploads/", string $processedDir  = "./processed") {
            $this->queueLimit = $queueLimit;
            $this->uploadsDir = $uploadsDir;
            $this->processedDir = $processedDir;
            $this->requestQueue = new RequestQueue($this);
        }
        
        public function preview($i = 0) {
                $this->requestQueue->preview($i);
        }
        /**
         *  *********************************************
         *  *   addRequest                              *
         *  *   ==================                      *
         *  *                                           *
         *  *********************************************
         */
        public function addRequest(Request $request) {
            $this->requestQueue->add($request);
        }

        /**
         *  *********************************************
         *  *   requestQueueFillUp                      *
         *  *   ==================                      *
         *  *   Fill's apps requestQueue.               *
         *  *********************************************
         */
        public function requestQueueFillUp() {
            $this->requestQueue->fillUp();
        }


        /**
         *  *****************************************************
         *  *   requestQueueProcessAll                          *
         *  *   ======================                          *
         *  *   Processes all requestst in app's requestQueue.  *
         *  *****************************************************
         */
        public function requestQueueProcessAll() {
            $this->requestQueue->processAll();
        }


        /**
         *  *********************************************
         *  *   processNextInQueue                      *
         *  *   ==================                      *
         *  *   Gets next in queue and process's it.    *
         *  *********************************************
         */
        public function processNextInQueue() {
            $req = $this->requestQueue->next();
            return $req->process();
        }

        /**
         *  *************************
         *  *   GETTERS & SETTERS   *
         *  *************************
         */

        /**
         * Get the value of requestQueue
         */
        public function getRequestQueue() : RequestQueue
        {
                return $this->requestQueue;
        }

        /**
         * Set the value of requestQueue
         */
        private function setRequestQueue($requestQueue): self
        {
                $this->requestQueue = $requestQueue;

                return $this;
        } 
        /**
         * Get the value of queueLimit
         */
        public function getQueueLimit()
        {
                return $this->queueLimit;
        }

        /**
         * Set the value of queueLimit
         */
        public function setQueueLimit($queueLimit): self
        {
                $this->queueLimit = $queueLimit;

                return $this;
        }
        /**
         * Get the value of mediaSourcesTableName
         */
        public function getMediaSourcesTableName()
        {
                return $this->mediaSourcesTableName;
        }

        /**
         * Set the value of mediaSourcesTableName
         */
        public function setMediaSourcesTableName($mediaSourcesTableName): self
        {
                $this->mediaSourcesTableName = $mediaSourcesTableName;

                return $this;
        }
        public function initMediaSourcesRepositoryOnTable($mediaSourcesTableName) {
            $this->mediaSourcesTableName = $mediaSourcesTableName;
            $this->mediaSourcesRepository = new MediaSourcesRepository($this, $mediaSourcesTableName);
        }
        /**
         * Get the value of templatesTableName
         */
        public function getTemplatesTableName()
        {
                return $this->templatesTableName;
        }
        public function initTemplateRepositoryOnTable($templatesTableName) {
            $this->templatesTableName = $templatesTableName;
            $this->templateRepository = new TemplateRepository($this, $templatesTableName);
        }
        /**
         * Set the value of templatesTableName
         */
        public function setTemplatesTableName($templatesTableName): self
        {
                $this->templatesTableName = $templatesTableName;

                return $this;
        }
        /**
         * Get the value of templateResourcesTableName
         */
        public function getTemplateResourcesTableName()
        {
                return $this->templateResourcesTableName;
        }

        /**
         * Set the value of templateResourcesTableName
         */
        public function setTemplateResourcesTableName($templateResourcesTableName): self
        {
                $this->templateResourcesTableName = $templateResourcesTableName;
                return $this;
        }
        public function initTemplateResourcesRepositoryOnTable($templateResourcesTableName)
        {
            $this->templateResourcesTableName = $templateResourcesTableName;
            $this->templateResourcesRepository = new TemplateResourcesRepository($this, $templateResourcesTableName);
        }
        /**
         * Get the value of requestsTableName
         */
        public function getRequestsTableName()
        {
                return $this->requestsTableName;
        }

        /**
         * Set the value of requestsTableName
         */
        public function setRequestsTableName($requestsTableName): self
        {
                $this->requestsTableName = $requestsTableName;
                return $this;
        }
        public function initRequestRepositoryOnTable($requestsTableName) {
            $this->requestsTableName = $requestsTableName;
            $this->requestRepository = new RequestRepository($this, $requestsTableName);
        }
        /**
         * Get the value of conn
         */
        public function getConn()
        {
                return $this->conn;
        }

        /**
         * Set the value of conn
         */
        public function setConn($conn): self
        {
                $this->conn = $conn;

                return $this;
        }
        /**
         * Get the value of requestRepository
         */
        public function getRequestRepository()
        {
                return $this->requestRepository;
        }

        /**
         * Set the value of requestRepository
         */
        public function setRequestRepository($requestRepository): self
        {
                $this->requestRepository = $requestRepository;

                return $this;
        }
        /**
         * Get the value of templateResourcesRepository
         */
        public function getTemplateResourcesRepository()
        {
                return $this->templateResourcesRepository;
        }

        /**
         * Set the value of templateResourcesRepository
         */
        public function setTemplateResourcesRepository($templateResourcesRepository): self
        {
                $this->templateResourcesRepository = $templateResourcesRepository;

                return $this;
        }
        /**
         * Get the value of mediaSourcesRepository
         */
        public function getMediaSourcesRepository()
        {
                return $this->mediaSourcesRepository;
        }

        /**
         * Set the value of mediaSourcesRepository
         */
        public function setMediaSourcesRepository($mediaSourcesRepository): self
        {
                $this->mediaSourcesRepository = $mediaSourcesRepository;

                return $this;
        }
        /**
         * Get the value of templateRepository
         */
        public function getTemplateRepository()
        {
                return $this->templateRepository;
        }

        /**
         * Set the value of templateRepository
         */
        public function setTemplateRepository($templateRepository): self
        {
                $this->templateRepository = $templateRepository;

                return $this;
        }
        /**
         * Get the value of processedDir
         */
        public function getProcessedDir()
        {
                return $this->processedDir;
        }

        /**
         * Set the value of processedDir
         */
        public function setProcessedDir($processedDir): self
        {
                $this->processedDir = $processedDir;

                return $this;
        }
    }





}