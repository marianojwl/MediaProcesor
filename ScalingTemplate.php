<?php
namespace marianojwl\MediaProcessor {
    class ScalingTemplate extends Template {
        protected $width;
        protected $height;
        

        
        public function __construct($mp, int $id, string $description, string $sufix, $type, $settings) {
            parent::__construct($mp, $id, $description, $sufix, $type, $settings);
            $setts = json_decode( $settings , true);
            $this->width = $setts["width"];
            $this->height = $setts["height"];
            $this->setMimeType($setts["mime_type"]);
        }
        public function prepare(Request $request, bool $preview = false) {
            $settings = $this->settings;
            $r = $request->getResource();
            // Load the original image
            $originalImage = $this->imageCreateFromResource($r);
        
            $originalWidth = imagesx($originalImage);
            $originalHeight = imagesy($originalImage);
        
            // Define the new width and height while maintaining the aspect ratio
            $newWidth = $this->width;
            $newHeight = $this->height;
        
            $aspectRatio = $originalWidth / $originalHeight;
            if ($newWidth / $newHeight > $aspectRatio) {
                $newWidth = $newHeight * $aspectRatio;
            } else {
                $newHeight = $newWidth / $aspectRatio;
            }
        
            // Create a new GD image resource for the resized image
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
            // Use a better interpolation method for resizing
            imagecopyresampled($resizedImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
            if($preview)
                $outputPath = null;
            else
                $outputPath = $this->getOutputPath($r, explode("/", $this->getMimeType())[1]);
        
            $this->imageSave($resizedImage, $outputPath, $this->getMimeType());
            
            imagedestroy($originalImage);
            imagedestroy($resizedImage);

            $request->setStatus("processed");
            $request->setProcessedPath($outputPath);
            return $request;
        }



    }
}