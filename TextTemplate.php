<?php
namespace marianojwl\MediaProcessor {
    class TextTemplate extends Template {
        protected $width;
        protected $height;
        protected $overlay_resource_id;
        protected $newX1;
        protected $newX2;
        protected $newY1;
        protected $newY2;
        protected $font;
        

        
        public function __construct(MediaProcessor $mp, int $id, string $description, string $sufix, $type, $settings) {
            parent::__construct($mp, $id, $description, $sufix, $type, $settings);
            $setts = json_decode( $settings , true);
            $this->width = $setts["width"];
            $this->height = $setts["height"];
            $this->font = $setts["font"]??'PassionOne-Regular.ttf';
            $this->newX1 = 0;
            $this->newX2 = $this->width;
            $this->newY1 = 0;
            $this->newY2 = $this->height;
            $this->setMimeType($setts["mime_type"]);
            //$this->overlay_resource_id = $setts["overlay_resource_id"];
        }
        public function prepare(Request $request, bool $preview = false)  {
          // template settings
          $templateSettings = json_decode($this->settings, true);
          // request settings
          $requestSettings = json_decode($request->getSettings(), true);
          // get the resource
          $r = $request->getResource();
          $path = explode("/", $r->getPath());
          $path[count($path)-1] = str_replace("+","%20",urlencode($path[count($path)-1]));
          $r->setPath( implode("/", $path) );
          // template resources repo
          $trsr = $this->mp->getTemplateResourcesRepository();

          $finalImage = imagecreatetruecolor($this->width, $this->height);
          $textDims = [];
          // Text Coordinates
          $texts = $requestSettings["txt"];
          foreach ($texts as $text) {
              $color = $text["color"];
              $x = 0;
              $y = $text["y"]??50;
              $font = __DIR__."/fonts/".$this->font;
              $fontSize = $text["s"]??20;
              $angle = 0;
              $padding = $text["linePadding"]??0;
              $textColor = imagecolorallocate($finalImage, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
              $coords = imagettftext($finalImage, $fontSize, $angle, $x, $y, $textColor, $font, $text["text"]);
              //$coords[5] = $coords[7] = $coords[5] - $y;
              $textDims[] = [$coords[2], $coords[5] - $y];
          }

          // Background image
          $bgImg_resource = $trsr->getById($templateSettings["bgImgId"]);
          $bgImage = $this->imageCreateFromResource($bgImg_resource);
          imagecopyresampled($finalImage, $bgImage, 0, 0, 0, 0, $this->width, $this->height, imagesx($bgImage), imagesy($bgImage));
          imagedestroy($bgImage);

          // Filled rectangles
          $filledRecs = $templateSettings["filledRecs"];
          foreach ($filledRecs as $rec) {
              $color = $rec["color"];
              $x1 = $rec["x1"];
              $y1 = $rec["y1"];
              $x2 = $rec["x2"];
              $y2 = $rec["y2"];
              $bgColor = imagecolorallocate($finalImage, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
              //$bgColor = imagecolorallocatealpha($finalImage, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)),60);
              imagefilledrectangle($finalImage, $x1, $y1, $x2, $y2, $bgColor);
              
          }

          // Images
          $imgs = $requestSettings["imgs"];
          foreach ($imgs as $img) {
              $overlayImage = $this->imageCreateFromResource($r);
              // keep aspect ratio and display centered in both axis
              $overlayWidth = imagesx($overlayImage);
              $overlayHeight = imagesy($overlayImage);
              $overlayRatio = $overlayWidth / $overlayHeight;
              $containerWidth = $img["w"];
              $containerHeight = $img["h"];
              $containerRatio = $containerWidth / $containerHeight;
              if($overlayRatio  != $containerRatio && ($img["keepRatio"]??false) === true) {
                // blured image below
                $bluredImage = $this->imageCreateFromResource($r);
                $msW = imagesx($bluredImage);
                $msH = imagesy($bluredImage);
                $inner = 0.05; // inner crop 0 to 1
                $miniW = 15 * $msH / $msW;
                $miniH = $miniW * $msH / $msW;
                $finalBluredImage = imagecreatetruecolor($miniW, $miniH);
                $bW = imagesx($bluredImage);
                $bH = imagesy($bluredImage);
                imagecopyresampled($finalBluredImage, $bluredImage, 0, 0, $bW*$inner, $bH*$inner, $miniW, $miniH, $bW*(1-$inner), $bH*(1-$inner));
                imagedestroy($bluredImage); // <- destroy =================

                for ($j = 0; $j < 4; $j++) {
                    imagefilter($finalBluredImage, IMG_FILTER_GAUSSIAN_BLUR);
                }
                
                imagecopyresampled($finalImage, $finalBluredImage, $img["x"], $img["y"], 0, 0, $containerWidth, $containerHeight, $miniW, $miniH);
                imagedestroy($finalBluredImage); // <- destroy ========
              }
              if($overlayRatio > $containerRatio) {
                  $finalWidth = $containerWidth;
                  $finalHeight = $containerWidth / $overlayRatio;
                  $finalX = $img["x"];
                  $finalY = $img["y"] + ($containerHeight - $finalHeight) / 2;
              } else {
                  $finalHeight = $containerHeight;
                  $finalWidth = $containerHeight * $overlayRatio;
                  $finalY = $img["y"];
                  $finalX = $img["x"] + ($containerWidth - $finalWidth) / 2;
              }
              imagecopyresampled($finalImage, $overlayImage, $finalX , $finalY, 0, 0, $finalWidth, $finalHeight, imagesx($overlayImage), imagesy($overlayImage));
              imagedestroy($overlayImage);
          }

          // Texts
          $texts = $requestSettings["txt"];
          $text_i = 0;
          foreach ($texts as $text) {
              $color = $text["color"];
              $x = $text["x"]??0;
              $y = $text["y"]??50;
              $align = $text["align"]??"left";
              if($align == "center") {
                $x = ($this->width - $textDims[$text_i][0]) / 2;
              }
              if($align == "right") {
                $x = ($this->width - $textDims[$text_i][0]) - $x;
              }

              $font = __DIR__."/fonts/".$this->font;
              $fontSize = $text["s"]??20;
              $angle = 0;
              $padding = $text["linePadding"]??0;
              if(!empty($text["bgcolor"])) {
                // if length is 6, then it's a hex color
                if(strlen($text["bgcolor"]) == 6) {
                  $textColor = imagecolorallocate($finalImage, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
                  $coords = imagettftext($finalImage, $fontSize, $angle, $x, $y, $textColor, $font, $text["text"]);
                  $bgColor = imagecolorallocate($finalImage, hexdec(substr($text["bgcolor"], 0, 2)), hexdec(substr($text["bgcolor"], 2, 2)), hexdec(substr($text["bgcolor"], 4, 2)));
                  imagefilledrectangle($finalImage, $coords[0]-$padding, $coords[1]+$padding, $coords[4]+$padding, $coords[5]-$padding, $bgColor);
                }
              }
              $textColor = imagecolorallocate($finalImage, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
              $coords = imagettftext($finalImage, $fontSize, $angle, $x, $y, $textColor, $font, $text["text"]);
              $text_i++;
          }
          
          
          if($preview)
              $outputPath = null;
          else
              $outputPath = $this->getOutputPath($r, explode("/", $this->getMimeType())[1]);
      
          $this->imageSave($finalImage, $outputPath, $this->getMimeType());
      
          // Clean up resources
          imagedestroy($finalImage);
        
          // Update request status and processed path
          $request->setStatus("processed");
          $request->setProcessedPath($outputPath);
          return $request;
        }
        public function prepareOld(Request $request, bool $preview = false) {
            $settings = json_decode($this->settings, true);
            $reqSettings = json_decode( $request->getSettings(), true );
            //var_dump($request->getSettings());
            
            $r = $request->getResource();
            $trsr = $this->mp->getTemplateResourcesRepository();
            $bgImg_resource = $trsr->getById($settings["bgImgId"]);
            $bgImage = $this->imageCreateFromResource($bgImg_resource);
        
            // Load the original image replace spaces with %20
            //$r->setPath( str_replace(" ", "%20", $r->getPath()) );
            // urlencode last part of path
            $path = explode("/", $r->getPath());
            $path[count($path)-1] = str_replace("+","%20",urlencode($path[count($path)-1]));
            $r->setPath( implode("/", $path) );
            
            $originalImage = $this->imageCreateFromResource($r);
            /*
            $msW = imagesx($originalImage);
            $msH = imagesy($originalImage);

            $inner = 0.05; // inner crop 0 to 1
            $miniW = 15 * $msH / $msW;
            $miniH = $miniW * $msH / $msW;
            $finalBluredImage = imagecreatetruecolor($miniW, $miniH);
            $bluredImage = $this->imageCreateFromResource($r);
            $bW = imagesx($bluredImage);
            $bH = imagesy($bluredImage);
            imagecopyresampled($finalBluredImage, $bluredImage, 0, 0, $bW*$inner, $bH*$inner, $miniW, $miniH, $bW*(1-$inner), $bH*(1-$inner));

            imagedestroy($bluredImage); // <- destroy =================

            for ($j = 0; $j < 10; $j++)
                imagefilter($finalBluredImage, IMG_FILTER_GAUSSIAN_BLUR);
            */
            // Load the overlay image with transparency support
            //$overlayImage = $this->imageCreateFromResource($overlay_resource);
        /*
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
            //$overlayWidth = imagesx($overlayImage);
            //$overlayHeight = imagesy($overlayImage);
            */
            // Create a new GD image resource for the final image
            $finalImage = imagecreatetruecolor($this->width, $this->height);
            imagecopyresampled($finalImage, $bgImage, 0, 0, 0, 0, $this->width, $this->height, imagesx($bgImage), imagesy($bgImage));
            imagedestroy($bgImage);

            // FILLED RECTANGLES
            // { "width":1280, "height":720, "mime_type":"image/png", "h1X":40,"h1Y":100,"h1S":60, "h1C":"9f0000", "h1B":"ffffff",   "h2X":40,"h2Y":170,"h2S":38, "h2C":"6e6e6e", "h2B":"ffffff", "line1X":400,"line1Y":250, "lineC":"000000", "lineB":"ffffff", "lineS":30,"lineSeparation":40,"posterX":40,"posterY":180,"posterW":370,"posterH":500, "bgImgId":3, filledRecs:[{ x1:40, y1:40, x2:1240, y2:100, color:"ffffff"}]}
            $filledRecs = $settings["filledRecs"];
            foreach ($filledRecs as $rec) {
                $color = $rec["color"];
                $x1 = $rec["x1"];
                $y1 = $rec["y1"];
                $x2 = $rec["x2"];
                $y2 = $rec["y2"];
                $bgColor = imagecolorallocate($finalImage, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
                imagefilledrectangle($finalImage, $x1, $y1, $x2, $y2, $bgColor);
            }
            /*
            //imagecopyresampled($finalImage, $finalBluredImage, $this->newX1, $this->newY1, 0, 0, $this->newX2-$this->newX1, $this->newY2-$this->newY1, $miniW, $miniH);
            imagecopyresampled($finalImage, $originalImage, $this->newX1, $this->newY1, 0, 0, $this->newX2-$this->newX1, $this->newY2-$this->newY1, $miniW, $miniH);
            
            //imagedestroy($finalBluredImage); // <- destroy ========

            for ($j = 0; $j < 14; $j++)
                imagefilter($finalImage, IMG_FILTER_GAUSSIAN_BLUR);

            // Resize the original image to fit the specified dimensions
            imagecopyresampled($finalImage, $originalImage, $newCenterX-($newWidthS/2), $newCenterY-($newHeightS/2), 0, 0, $newWidthS, $newHeightS, $originalWidth, $originalHeight);
            
            imagedestroy($originalImage); // <- destroy ========
            */

            // IMGS #################################
            // SAMPLE: {"txt":[{"text":"TODOS LOS DÍAS","color":"000000"},{"text":"17:20","color":"000000"},{"text":"SÁB DOM LUN MAR","color":"000000"},{"text":"14:50","color":"000000"},{"text":"NULL | 116 MIN | AVENTURAS","color":"333333"},{"text":"T 2D CA WONKA - SALA TURBO","color":"333333"},{"text":"TODOS LOS DÍAS","color":"000000","x":0,"y":50},{"text":"17:20","color":"000000","x":0,"y":100},{"text":"SÁB DOM LUN MAR","color":"000000","x":0,"y":50},{"text":"14:50","color":"000000","x":0,"y":100},{"text":"NULL | 116 MIN | AVENTURAS","color":"333333"},{"text":"T 2D CA WONKA - SALA TURBO","color":"333333"},{"text":"TODOS LOS DÍAS","color":"000000","x":0,"y":50},{"text":"17:20","color":"000000","x":0,"y":100},{"text":"SÁB DOM LUN MAR","color":"000000","x":0,"y":50},{"text":"14:50","color":"000000","x":0,"y":100},{"text":"NULL | 116 MIN | AVENTURAS","color":"333333"},{"text":"T 2D CA WONKA - SALA TURBO","color":"333333"},{"text":"TODOS LOS DÍAS","color":"000000","x":0,"y":50},{"text":"17:20","color":"000000","x":0,"y":100},{"text":"SÁB DOM LUN MAR","color":"000000","x":0,"y":50},{"text":"14:50","color":"000000","x":0,"y":100},{"text":"NULL | 116 MIN | AVENTURAS","color":"333333"},{"text":"T 2D CA WONKA - SALA TURBO","color":"333333"}],"imgs":[{"src":"https://www.brodi.com.ar/clientes/cinemadb/recursos/processed/LM_LATAM_WONKA_EM_VERT
            $imgs = $reqSettings["imgs"];
            //var_dump($imgs);
            //foreach ($imgs as $img) {
                $overlayImage = $originalImage;
                $overlayWidth = imagesx($overlayImage);
                $overlayHeight = imagesy($overlayImage);
                imagecopyresampled($finalImage, $overlayImage, $imgs[0]["x"] , $imgs[0]["y"], 0, 0, $imgs[0]["w"], $imgs[0]["h"], $overlayWidth, $overlayHeight);
                imagedestroy($overlayImage);
            //}
            // TEXTS #################################
            // SAMPLE: {"txt":[{"text":"TODOS LOS DÍAS","color":"000000"},{"text":"17:20","color":"000000"},{"text":"SÁB DOM LUN MAR","color":"000000"},{"text":"14:50","color":"000000"},{"text":"NULL | 116 MIN | AVENTURAS","color":"333333"},{"text":"T 2D CA WONKA - SALA TURBO","color":"333333"},{"text":"TODOS LOS DÍAS","color":"000000","x":0,"y":50},{"text":"17:20","color":"000000","x":0,"y":100},{"text":"SÁB DOM LUN MAR","color":"000000","x":0,"y":50},{"text":"14:50","color":"000000","x":0,"y":100},{"text":"NULL | 116 MIN | AVENTURAS","color":"333333"},{"text":"T 2D CA WONKA - SALA TURBO","color":"333333"},{"text":"TODOS LOS DÍAS","color":"000000","x":0,"y":50},{"text":"17:20","color":"000000","x":0,"y":100},{"text":"SÁB DOM LUN MAR","color":"000000","x":0,"y":50},{"text":"14:50","color":"000000","x":0,"y":100},{"text":"NULL | 116 MIN | AVENTURAS","color":"333333"},{"text":"T 2D CA WONKA - SALA TURBO","color":"333333"},{"text":"TODOS LOS DÍAS","color":"000000","x":0,"y":50},{"text":"17:20","color":"000000","x":0,"y":100},{"text":"SÁB DOM LUN MAR","color":"000000","x":0,"y":50},{"text":"14:50","color":"000000","x":0,"y":100},{"text":"NULL | 116 MIN | AVENTURAS","color":"333333"},{"text":"T 2D CA WONKA - SALA TURBO","color":"333333"}],"imgs":[{"src":"https://www.brodi.com.ar/clientes/cinemadb/recursos/processed/LM_LATAM_WONKA_EM_VERT_INTL_TSR_1080x1920_INTL (2)_GACE.png"},{"src":"https://www.brodi.com.ar/clientes/cinemadb/recursos/processed/LM_LATAM_WONKA_EM_VERT_INTL_TSR_1080x1920_INTL (2)_GACE.png"},{"src":"https://www.brodi.com.ar/clientes/cinemadb/recursos/processed/LM_LATAM_WONKA_EM_VERT_INTL_TSR_1080x1920_INTL (2)_GACE.png"},{"src":"https://www.brodi.com.ar/clientes/cinemadb/recursos/processed/LM_LATAM_WONKA_EM_VERT_INTL_TSR_1080x1920_INTL (2)_GACE.png"}]}
            $texts = $reqSettings["txt"];
            //var_dump($texts);

            foreach ($texts as $text) {
                $color = $text["color"];
                $x = $text["x"]??0;
                $y = $text["y"]??50;
                $font = __DIR__."/fonts/PassionOne-Regular.ttf";
                //echo $font;
                $fontSize = $text["s"]??20;
                $angle = 0;
                $padding = $text["linePadding"]??0;
                if(!empty($text["bgcolor"])) {
                  // if length is 6, then it's a hex color
                  if(strlen($text["bgcolor"]) == 6) {
                    $textColor = imagecolorallocate($finalImage, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
                    $coords = imagettftext($finalImage, $fontSize, $angle, $x, $y, $textColor, $font, $text["text"]);
                    $bgColor = imagecolorallocate($finalImage, hexdec(substr($text["bgcolor"], 0, 2)), hexdec(substr($text["bgcolor"], 2, 2)), hexdec(substr($text["bgcolor"], 4, 2)));
                    imagefilledrectangle($finalImage, $coords[0]-$padding, $coords[1]+$padding, $coords[4]+$padding, $coords[5]-$padding, $bgColor);
                  }
                }

                $textColor = imagecolorallocate($finalImage, hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
                $coords = imagettftext($finalImage, $fontSize, $angle, $x, $y, $textColor, $font, $text["text"]);
            }


            if($preview)
                $outputPath = null;
            else
                $outputPath = $this->getOutputPath($r, explode("/", $this->getMimeType())[1]);
        
            $this->imageSave($finalImage, $outputPath, $this->getMimeType());
        
            // Clean up resources
            
            //imagedestroy($overlayImage);
            imagedestroy($finalImage);
            
            
        
            // Update request status and processed path
            $request->setStatus("processed");
            $request->setProcessedPath($outputPath);
            return $request;
            //$this->mp->getRequestRepository()->save($request);
        }
        
    }
}