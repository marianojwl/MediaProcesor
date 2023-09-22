<?php
namespace marianojwl {
    class MediaProcessor {
        
        public function process(Resource $r, Template $t) {
            return $t->process($r);
        }
    }
}