<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detector de artefatos</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
    .imagem-responsiva {
        max-width: 100%;
        height: auto;
    }
    .image-container {
    max-width: 400px; /* Set the maximum width */
    margin: 0 auto; /* Center the image container */
}

/* Style for the image itself */
.image-container img {
    width: 100%; /* Make the image take up 100% of the container's width */
    height: auto; /* Maintain the aspect ratio */
    display: block; /* Remove extra space below the image */
}
</style>
</head>
<body>
<?php
require_once 'aws/aws-autoloader.php';
require_once "Kafka.class.php";
require_once "functions.php";

$conteudo = "";
$url_eda = getenv("ANSIBLE_EDA");
$url_eda_cop = getenv("ANSIBLE_EDA_COP");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $targetDir = "uploads/";
    $sessionId = session_id();
    $targetFile = $targetDir . $sessionId . "_" . basename($_FILES["image"]["name"]);
    $image_name_future =  $sessionId . "_" . basename($_FILES["image"]["name"]);

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
                    $conteudo .= "Encontrado <b>".$label['Name'] . "</b> com grau de certeza de: " . $label['Confidence'] . "%<br>\n";
                    if($label['Name'] == "Backpack") 
                    {
                        $message = "$image_name_future";
                        $url = "http://$url_eda/endpoint";
                        //copy( string $source, string $destination, resource $context )
                        copy( $targetFile, "uploads/mochila.jpg");
                        $response = sendHttpPostRequest($message, $url);
                    }
                    if($label['Name'] == "Pen") 
                    {
                        $message = "$image_name_future";
                        $url = "http://$url_eda/endpoint";
                        //copy( string $source, string $destination, resource $context )
                        copy( $targetFile, "uploads/caneta.jpg");
                        //$response = sendHttpPostRequest($message, $url);
                    }
                    if($label['Name'] == "Headphones") 
                    {
                        $message = "$image_name_future";
                        $url = "http://$url_eda/endpoint";
                        //copy( string $source, string $destination, resource $context )
                        copy( $targetFile, "uploads/fone.jpg");
                        //$response = sendHttpPostRequest($message, $url);
                    }
                    if($label['Name'] == "Phone") 
                    {
                        $message = "$image_name_future";
                        $url = "http://$url_eda/endpoint";
                        //copy( string $source, string $destination, resource $context )
                        copy( $targetFile, "uploads/celular.jpg");
                        //$response = sendHttpPostRequest($message, $url);
                    }
                    if($label['Name'] == "Knife") 
                    {
                        $message = "$image_name_future";
                        $url = "http://$url_eda_cop/endpoint";
                        //copy( string $source, string $destination, resource $context )
                        copy( $targetFile, "uploads/knife.jpg");
                        $response = sendHttpPostRequest($message, $url);
                    }
                }
                // ===========================
                // Jogando para Kafka
                // ===========================
                   // echo $json_results;
                    $Kafka = new Kafka;
                    $Kafka->kafka_server = getenv("KAFKA_BROKERS");
                    $Kafka->kafka_topic = getenv("KAFKA_TOPIC");
                    $conteudo .= $Kafka->ProduzMensagem($json_results);

                    
$filename = 'uploads/number.txt';

// Check if the file exists
if (file_exists($filename)) {
    // Read the current number from the file
    $currentNumber = (int) file_get_contents($filename);

    // Increment the number by 1
    $newNumber = $currentNumber + 1;

    // Update the file with the new number
    file_put_contents($filename, $newNumber);

   // echo "Number updated successfully. New number is $newNumber.";
} else {
   // echo "File not found: $filename";
    $fp = fopen("uploads/number.txt", "w+");
    fputs($fp, "0");
    fclose($fp);
}
            } catch (Exception $e) {
                echo "Error analyzing the image: " . $e->getMessage();
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    $fp = fopen("$targetFile.json", "w+");
    fputs($fp, $json_results);
    fclose($fp);
}
?>
<!-- <div>
    <img src="mostrafoto2.php?image_name=<?php echo $image_name_future;?>"  class="imagem-responsiva"><br>
</div> -->
<div class="image-container">
        <img src="mostrafoto2.php?image_name=<?php echo $image_name_future;?>" alt="Sua imagem">
    </div>
<div>


    <?php echo $conteudo;?>
</div>
 <a href="index.php"><button type="button" name="voltar" class="btn btn-primary">Testar outra foto</button></a>
</body>
</html>