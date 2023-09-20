<?php
// Path to the input image


$inputImagePath = "uploads/celular.jpg";

if(!file_exists($inputImagePath)) {
    $inputImagePath = "notfound.jpg";
}



// Load the input image
$inputImage = imagecreatefromjpeg($inputImagePath);

if (!$inputImage) {
    die('Unable to load the input image.');
}
// Get the image dimensions
$imageWidth = imagesx($inputImage);
$imageHeight = imagesy($inputImage);

// Create a semi-transparent white color
$squareColor = imagecolorallocatealpha($inputImage, 255, 255, 255, 64); // 64 is half of 127 (max alpha)

// Loop through each bounding box in the response


// Set the header to output the image as PNG with alpha transparency
header('Content-Type: image/jpeg'); // Set the header for image output
imagejpeg($inputImage); // Output the mod

// Clean up resources
imagedestroy($inputImage);
?>
