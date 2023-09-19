<?php
// Path to the input image
$inputImagePath = "uploads/".$_GET['image_name'];

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

// Define the first square's properties
$squareSize = min($imageWidth, $imageHeight) * 0.3; // Square size as a fraction of image dimensions
$squareX1 = ($imageWidth - $squareSize) / 2; // X-coordinate of the first square's top-left corner
$squareY = ($imageHeight - $squareSize) / 2; // Y-coordinate of the square's top-left corner

// Define the second square's properties (20 pixels to the left of the first square)
$squareX2 = $squareX1 - 20;

// Enable alpha blending for the image
imagealphablending($inputImage, true);
imagesavealpha($inputImage, true);

// Fill the first square with the semi-transparent color
imagefilledrectangle($inputImage, $squareX1, $squareY, $squareX1 + $squareSize, $squareY + $squareSize, $squareColor);

// Fill the second square with the same semi-transparent color
imagefilledrectangle($inputImage, $squareX2, $squareY, $squareX2 + $squareSize, $squareY + $squareSize, $squareColor);

// Define text properties
$text = 'Sample Text';
$textColor = imagecolorallocate($inputImage, 0, 0, 0); // Black text color
$font = 'arial.ttf'; // Specify the path to a TrueType font file

// Calculate the position to center the text inside the first square
$textX = $squareX1 + ($squareSize - imagefontwidth(5) * strlen($text)) / 2;
$textY = $squareY + ($squareSize - imagefontheight(5)) / 2;

// Add text to the image
imagestring($inputImage, 5, $textX, $textY, $text, $textColor);

// Set the header to output the image as PNG with alpha transparency
header('Content-Type: image/png');

// Output the modified image as PNG to the browser
imagepng($inputImage);

// Clean up resources
imagedestroy($inputImage);
?>
