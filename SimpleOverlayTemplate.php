<?php
namespace marianojwl\MediaProcessor {
    class SimpleOverlayTemplate extends Template {
        protected $width;
        protected $height;
        protected $overlay_resource_id;
        
        public function __construct($mp, int $id, string $description, string $sufix, $type, $settings) {
            parent::__construct($mp, $id, $description, $sufix, $type, $settings);
            $setts = json_decode( $settings , true);
            $this->width = $setts["width"];
            $this->height = $setts["height"];
            $this->setMimeType($setts["mime_type"]);
            $this->overlay_resource_id = $setts["overlay_resource_id"];
        }

        public function prepare(Request $request, bool $preview = false) {
            $settings = $this->settings;
            $r = $request->getResource();
            $trsr = $this->mp->getTemplateResourcesRepository();
            $overlay_resource = $trsr->getById($this->overlay_resource_id);
               
            // Load the original image
            $originalImage = $this->imageCreateFromResource($r);
        
            // Load the overlay image with transparency support
            $overlayImage = $this->imageCreateFromResource($overlay_resource);
        
            // Get the dimensions of the original image
            $originalWidth = imagesx($originalImage);
            $originalHeight = imagesy($originalImage);
        
            // Get the dimensions of the overlay image
            $overlayWidth = imagesx($overlayImage);
            $overlayHeight = imagesy($overlayImage);
        
            // Create a new GD image resource for the final image
            $finalImage = imagecreatetruecolor( $this->width, $this->height );

            imagecopyresampled($finalImage, $originalImage, 0, 0, 0, 0, $this->width, $this->height, $originalWidth, $originalHeight);
            
            imagecopyresampled($finalImage, $overlayImage, 0, 0, 0, 0, $this->width, $this->height, $overlayWidth, $overlayHeight);

            if($preview)
                $outputPath = null;
            else
                $outputPath = $this->getOutputPath($r, explode("/", $this->getMimeType())[1]);

            $this->imageSave($finalImage, $outputPath, $this->getMimeType());
        
            // Clean up resources
            imagedestroy($originalImage);
            imagedestroy($overlayImage);
            imagedestroy($finalImage);
        
            //$resource = new Image(null, 1, $outputPath, $this->getMimeType(), false, $this->id);
            $request->setStatus("processed");
            $request->setProcessedPath($outputPath);
            $this->mp->getRequestRepository()->save($request);
            //$resource = $this->storeResource($resource);
        
            //return $resource;
        }
    }
}