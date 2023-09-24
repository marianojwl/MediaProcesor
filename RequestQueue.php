<?php
namespace marianojwl\MediaProcessor {
    class RequestQueue {
        protected $requests;

        public function __construct() {
            $this->requests = [];
        }

        public function add(Request $r) {
            $this->requests[] = $r;
        }
        public function next() {
            return array_pop($this->requests);
        }

        public function fillUp(int $with=10) {
            $rr = new RequestRepository();
            $requests = $rr->getNextN($with);
            foreach($requests as $request)
                $this->add($request);
        }

        public function processAll() {
            foreach($this->requests as $request)
                $request->process();
        }
    }
}