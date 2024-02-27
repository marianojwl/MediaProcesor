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
            return count($requests);
        }

        public function processAll() {
            foreach($this->requests as $request)
                $this->mp->getRequestRepository()->save( $request->process() );
                //$request->process();
        }
        public function processAllWithResult() {
            $results = [];
            $success = true;
            $error = "";
            try {
              foreach($this->requests as $request)
                  $results[] = $this->mp->getRequestRepository()->save( $request->process() );
            } catch (\Exception $e) {
              $success = false;
              $error = $e->getMessage();
            }
          return ["success"=>$success, "error"=>$error, "data"=>["results"=>$results]];
                
        }
    }
}