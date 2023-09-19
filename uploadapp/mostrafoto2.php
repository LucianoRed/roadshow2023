<?php
// Path to the input image


$inputImagePath = "uploads/".$_GET['image_name'];
$jsonData = file_get_contents("$inputImagePath.json");


// Step 2: Parse the JSON data into a PHP object
$responseData = json_decode($jsonData);


// Load the input image
$inputImage = imagecreatefromjpeg($inputImagePath);

if (!$inputImage) {
    die('Unable to load the input image.');
}
// Get the image dimensions
$imageWidth = imagesx($inputImage);
$imageHeight = imagesy($inputImage);

// Loop through each bounding box in the response
for($x=0;$x<sizeof($dataObject);$x++) {
	$Objeto = $dataObject[$x];
	$Instances = $Objeto->Instances;
	if(sizeof($Instances) > 0) {
		$Instance = $Instances[0];
		$boundingBox = $Instance->BoundingBox;
		$width = $boundingBox->Width;
		$height = $boundingBox->Height;
		$left = $boundingBox->Left;
		$top = $boundingBox->Top;


        // Calculate square coordinates and size
        $squareX = $left * $imageWidth;
        $squareY = $top * $imageHeight;
        $squareSize = min($width * $imageWidth, $height * $imageHeight);
        

        // Draw a square around the object
        $color = imagecolorallocate($inputImage, 255, 0, 0); // Red color (you can change the color as needed)
        imagerectangle(
            $inputImage,
            $squareX,
            $squareY,
            $squareX + $squareSize,
            $squareY + $squareSize,
            $color
        );
    }
}

// Set the header to output the image as PNG with alpha transparency
header('Content-Type: image/jpeg'); // Set the header for image output
imagejpeg($inputImage); // Output the mod

// Clean up resources
imagedestroy($inputImage);
?>
