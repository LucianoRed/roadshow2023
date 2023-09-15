<?php
session_start();
require_once 'aws/aws-autoloader.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $targetDir = "uploads/";
    $sessionId = session_id();
    $targetFile = $targetDir . $sessionId . "_" . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            echo "The file " . basename($_FILES["image"]["name"]) . " has been uploaded as " . $sessionId . "_" . basename($_FILES["image"]["name"]) . ".";

            // Now, send the image to Amazon Rekognition for analysis
            //require 'vendor/autoload.php'; // Include the AWS SDK for PHP

            // Specify your AWS credentials and region
            $awsKey = getenv("AWS_KEY");
            $awsSecret = getenv("AWS_SECRET");
            $awsRegion = 'us-east-1'; // Change to your desired region

            // Create an Amazon Rekognition client
            $rekognition = new Aws\Rekognition\RekognitionClient([
                'version' => 'latest',
                'region' => $awsRegion,
                'credentials' => [
                    'key' => $awsKey,
                    'secret' => $awsSecret,
                ],
            ]);

            // Analyze the uploaded image
            $image = file_get_contents($targetFile);

            try {
                $result = $rekognition->detectLabels([
                    'Image' => [
                        'Bytes' => $image,
                    ],
                ]);

                // Process the result (e.g., display labels)
                echo "<h2>Labels Detected:</h2>";
                $json_results = json_encode($result);
                var_dump($json_results);
                foreach ($result['Labels'] as $label) {
                    echo $label['Name'] . ": " . $label['Confidence'] . "%<br>";
                }
            } catch (Exception $e) {
                echo "Error analyzing the image: " . $e->getMessage();
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
