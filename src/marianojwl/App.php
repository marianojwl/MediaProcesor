<?php
namespace marianojwl {
    class App {
        protected $requestQueue;
        public function __construct() {
            $this->requestQueue = new RequestQueue();
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
    }
}