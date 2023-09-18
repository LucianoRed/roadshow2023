<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detector de artefatos</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const audioPlayer = document.getElementById("audioPlayer");
        const playButtons = document.querySelectorAll(".playButton");

        playButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const audioFile = button.getAttribute("data-audio");
                const audioSource = "https://roadshow-2023-git2-upload.apps.teste.sandbox2448.opentlc.com/polly.php?label=" + encodeURIComponent(audioFile);

                audioPlayer.src = audioSource;
                audioPlayer.load(); // Load the new audio source
                audioPlayer.play(); // Play the audio
            });
        });
    </script>

</head>
<body>
<audio id="audioPlayer" controls>
        <source src="" type="audio/mpeg">
    </audio>
<?php
session_start();
require_once 'aws/aws-autoloader.php';
require_once "Kafka.class.php";

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
          //  echo "The file " . basename($_FILES["image"]["name"]) . " has been uploaded as " . $sessionId . "_" . basename($_FILES["image"]["name"]) . ".";

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
                // echo "<h2>Labels Detected:</h2>";
                $json_results = json_encode($result['Labels']);
              //  var_dump($json_results);
                foreach ($result['Labels'] as $label) {
                    $translatedLabel = translateText($label['Name'], 'pt');
                    echo "<div>\n";
                    echo "Encontrado <b>". $translatedLabel . "</b> com grau de certeza de: " . $label['Confidence'] . "%";
                    echo "<button class=\"playButton\" data-audio=\"$translatedLabel\">Ouvir $translatedLabel</button><br>\n";
                    echo "</div>\n";

                }
                // ===========================
                // Jogando para Kafka
                // ===========================
                   // echo $json_results;
                    $Kafka = new Kafka;
                    $Kafka->kafka_server = getenv("KAFKA_BROKERS");
                    $Kafka->kafka_topic = getenv("KAFKA_TOPIC");
                    $Kafka->ProduzMensagem($json_results);
            } catch (Exception $e) {
                echo "Error analyzing the image: " . $e->getMessage();
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
function translateText($text, $targetLanguage)
{

    $awsKey = getenv("AWS_KEY");
    $awsSecret = getenv("AWS_SECRET");
    $translate = new Aws\Translate\TranslateClient([
        'version' => 'latest',
        'region' => 'us-east-1', // Change to your desired region
        'credentials' => [
            'key' => $awsKey,
            'secret' => $awsSecret,
        ],
    ]);

    $result = $translate->translateText([
        'SourceLanguageCode' => 'auto', // Automatically detect source language
        'TargetLanguageCode' => $targetLanguage,
        'Text' => $text,
    ]);

    return $result['TranslatedText'];
}


?>
 <a href="index.php"><button type="button" name="voltar" class="btn btn-primary">Testar outra foto</button></a>
</body>
</html>