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
    function speakLabel(label) {
        // Call Amazon Polly to synthesize and speak the label
        $.ajax({
            url: 'polly.php', // Create a separate PHP file to handle Polly synthesis
            type: 'GET',
            data: { label: label },
            success: function(response) {
                // Handle Polly response (e.g., play the audio)
                var audio = new Audio('data:audio/mpeg;base64,' + response);
                audio.id = 'audio_' + label;
                audio.controls = true;
                audio.style.display = 'block';

                // Append the audio element to the document
                document.body.appendChild(audio);

                // Play the audio
                audio.play();
            },
            error: function() {
                // Handle Polly synthesis error
                alert('Error synthesizing speech.');
            }
        });
    }
</script>

</head>
<body>
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

                    echo "Encontrado <b>". $translatedLabel . "</b> com grau de certeza de: " . $label['Confidence'] . "%<br>\n";
                    echo '<button onclick="speakLabel(\'' . $translatedLabel . '\')">Ouvir</button>';
                    echo '<audio id="audio_' . $translatedLabel . '" controls style="display:none;"></audio>';


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