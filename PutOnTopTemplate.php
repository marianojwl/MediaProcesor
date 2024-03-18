<?php
namespace marianojwl\MediaProcessor {
    class PutOnTopTemplate extends Template {
        protected $width;
        protected $height;
        protected $underlay_resource_id;
        protected $newX1;
        protected $newX2;
        protected $newY1;
        protected $newY2;
        protected $frameWidth;
        protected $frameColor;
        

        
        public function __construct(MediaProcessor $mp, int $id, string $description, string $sufix, $type, $settings) {
            parent::__construct($mp, $id, $description, $sufix, $type, $settings);
            $setts = json_decode( $settings , true);
            $this->width = $setts["width"];
            $this->height = $setts["height"];
            $this->newX1 = $setts["newX1"];
            $this->newX2 = $setts["newX2"];
            $this->newY1 = $setts["newY1"];
            $this->newY2 = $setts["newY2"];
            $this->frameWidth = $setts["frameWidth"]??null;
            $this->frameColor = $setts["frameColor"]??"ffffff";
            $this->setMimeType($setts["mime_type"]);
            $this->underlay_resource_id = $setts["underlay_resource_id"];
        }
        public function prepare(Request $request, bool $preview = false) {
            $settings = $this->settings;
            $r = $request->getResource();
            $trsr = $this->mp->getTemplateResourcesRepository();
            $overlay_resource = $trsr->getById($this->underlay_resource_id);
        
            // Load the original image
            $originalImage = $this->imageCreateFromResource($r);
            $msW = imagesx($originalImage);
            $msH = imagesy($originalImage);

            $inner = 0.05; // inner crop 0 to 1
            $miniW = 15 * $msH / $msW;
            $miniH = $miniW * $msH / $msW;
            $finalBluredImage = imagecreatetruecolor($miniW, $miniH);
            /*
            $bluredImage = $this->imageCreateFromResource($r);
            $bW = imagesx($bluredImage);
            $bH = imagesy($bluredImage);
            imagecopyresampled($finalBluredImage, $bluredImage, 0, 0, $bW*$inner, $bH*$inner, $miniW, $miniH, $bW*(1-$inner), $bH*(1-$inner));

            imagedestroy($bluredImage); // <- destroy =================
            */

            // for ($j = 0; $j < 10; $j++)   imagefilter($finalBluredImage, IMG_FILTER_GAUSSIAN_BLUR);
        
            // Load the overlay image with transparency support
            $overlayImage = $this->imageCreateFromResource($overlay_resource);
        
            // Get the dimensions of the original image
            $originalWidth = imagesx($originalImage);
            $originalHeight = imagesy($originalImage);
            // Define the new width and height while maintaining the aspect ratio
            $newWidth = $this->width;
            $newHeight = $this->height;
        
            $aspectRatio = $originalWidth / $originalHeight;
            $newWidthS = ($this->newX2 - $this->newX1);
            $newHeightS = ($this->newY2 - $this->newY1);


            if ($newWidthS / $newHeightS > $aspectRatio) {
                $newWidthS = $newHeightS * $aspectRatio;
            } else {
                $newHeightS = $newWidthS / $aspectRatio;
            }
            $newCenterX = $this->newX1 + ($this->newX2-$this->newX1)/2;
            $newCenterY = $this->newY1 + ($this->newY2-$this->newY1)/2;
            
            // Get the dimensions of the overlay image
            $overlayWidth = imagesx($overlayImage);
            $overlayHeight = imagesy($overlayImage);
        
            // Create a new GD image resource for the final image
            $finalImage = imagecreatetruecolor($this->width, $this->height);
            imagecopyresampled($finalImage, $finalBluredImage, $this->newX1, $this->newY1, 0, 0, $this->newX2-$this->newX1, $this->newY2-$this->newY1, $miniW, $miniH);
            
            imagedestroy($finalBluredImage); // <- destroy ========

            //for ($j = 0; $j < 14; $j++)  imagefilter($finalImage, IMG_FILTER_GAUSSIAN_BLUR);

            
            // Underlay the transparent image under the resized original image
            imagecopyresampled($finalImage, $overlayImage, 0, 0, 0, 0, $this->width, $this->height, $overlayWidth, $overlayHeight);
        
            if($this->frameWidth) {
              $frameWidth = $this->frameWidth; // in percentage
            // create white filled rectangle before laying the original image, making it wider and taller as to show as a frame
              $webcolor = $this->frameColor;
              $white = imagecolorallocate($finalImage, hexdec(substr($webcolor, 0, 2)), hexdec(substr($webcolor, 2, 2)), hexdec(substr($webcolor, 4, 2)));
              //$white = imagecolorallocate($finalImage, 255, 255, 255);
              imagefilledrectangle($finalImage, $newCenterX-($newWidthS/2)-floor($frameWidth*$this->width), $newCenterY-($newHeightS/2)-floor($frameWidth*$this->width), $newCenterX+($newWidthS/2)+floor($frameWidth*$this->width), $newCenterY+($newHeightS/2)+floor($frameWidth*$this->width), $white);

            }
            

            // Resize the original image to fit the specified dimensions
            imagecopyresampled($finalImage, $originalImage, $newCenterX-($newWidthS/2), $newCenterY-($newHeightS/2), 0, 0, $newWidthS, $newHeightS, $originalWidth, $originalHeight);
            
            imagedestroy($originalImage); // <- destroy ========

            
            if($preview)
                $outputPath = null;
            else
                $outputPath = $this->getOutputPath($r, explode("/", $this->getMimeType())[1]);
        
            $this->imageSave($finalImage, $outputPath, $this->getMimeType());
        
            // Clean up resources
            
            imagedestroy($overlayImage);
            imagedestroy($finalImage);
            
            
        
            // Update request status and processed path
            $request->setStatus("processed");
            $request->setProcessedPath($outputPath);
            return $request;
            //$this->mp->getRequestRepository()->save($request);
        }
        
        public function processOld(Request $request, $settings = "{}") {
            $r = $request->getResource();
            $trsr = $this->mp->getTemplateResourcesRepository();
            $overlay_resource = $trsr->getById($this->underlay_resource_id);
            //$rsr->closeConnection();
               
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
        
            // Copy the original image to the final image
            //imagecopy($finalImage, $originalImage, 0, 0, 0, 0, $originalWidth, $originalHeight);
            imagecopyresized($finalImage, $originalImage, $this->newX1, $this->newY1, 0, 0, ($this->newX2-$this->newX1), ($this->newY2-$this->newY1), $originalWidth, $originalHeight);
            
            // Calculate the position to center the overlay image on the original image
            //$x = ($originalWidth - $overlayWidth) / 2;
            //$y = ($originalHeight - $overlayHeight) / 2;
        
            // Overlay the transparent image on top of the original image
            //imagecopy($finalImage, $overlayImage, $x, $y, 0, 0, $overlayWidth, $overlayHeight);
            imagecopyresized($finalImage, $overlayImage, 0, 0, 0, 0, $this->width, $this->height, $overlayWidth, $overlayHeight);

            // Define the path where you want to save the resized image
            $outputPath = $this->getOutputPath($r, explode("/",$this->getMimeType())[1]);

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