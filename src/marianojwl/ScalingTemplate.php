<?php
namespace marianojwl {
    class ScalingTemplate extends Template {
        protected $width;
        protected $height;

        //public function __construct(int $width, int $height) {
        public function __construct(int $id, string $description, string $sufix, $type, $settings) {
            parent::__construct($id, $description, $sufix, $type, $settings);
            $setts = json_decode( $settings , true);
            $this->width = $setts["width"];
            $this->height = $setts["height"];
        }

        public function process(Resource $r, $settings = "{}")  {
            $path_to_file = $r->getPath();

            // Load the original image
            $originalImage = imagecreatefromjpeg($path_to_file);

            // Define the new width and height
            $newWidth = $this->width;
            $newHeight = $this->height;

            // Create a new GD image resource for the resized image
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Resize the image
            imagecopyresized($resizedImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($originalImage), imagesy($originalImage));

            // Define the path where you want to save the resized image
            $outputPath = $this->getOutputPath($r);

            // Save the resized image to the specified path
            imagejpeg($resizedImage, $outputPath);

            // Clean up resources
            imagedestroy($originalImage);
            imagedestroy($resizedImage);

            $resource = new Image(null,1,$outputPath,"image/jpeg",false,$this->id);
            
            $resource = $this->storeResource($resource);

            return $resource;
        }


    }
}