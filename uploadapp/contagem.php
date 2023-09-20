<?php
header("Access-Control-Allow-Origin: *");

$directory = 'uploads/'; // Replace with the path to your directory

// Use scandir to list files in the directory
$files = scandir($directory);

// Count the number of files (excluding . and ..)
$fileCount = (count($files) - 2)/2;

echo $fileCount;
?>