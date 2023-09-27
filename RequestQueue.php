<?php
namespace marianojwl\MediaProcessor {
    class RequestQueue {
        protected $requests;
        protected $mp;

        public function __construct(MediaProcessor $mp) {
            $this->requests = [];
            $this->mp = $mp;
        }
        public function preview(int $i = 0) {
            $req = $this->requests[$i]->preview();

        }
        public function add(Request $r) {
            $this->requests[] = $r;
        }
        public function next() {
            return array_pop($this->requests);
        }

        public function fillUp(int $with=10) {
            $rr = $this->mp->getRequestRepository();
            $requests = $rr->getNextN($with);
            foreach($requests as $request)
                $this->add($request);
        }

        public function processAll() {
            foreach($this->requests as $request)
                $this->mp->getRequestRepository()->save( $request->process() );
                //$request->process();
        }
    }
}