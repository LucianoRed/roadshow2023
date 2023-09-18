<?php
// require 'vendor/autoload.php'; // Include the AWS SDK for PHP
require_once 'aws/aws-autoloader.php';

use Aws\Polly\PollyClient;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["label"])) {
    $awsKey = getenv("AWS_KEY");
    $awsSecret = getenv("AWS_SECRET");
    $awsRegion = 'us-east-1'; // Change to your desired region

    $polly = new PollyClient([
        'version' => 'latest',
        'region' => $awsRegion,
        'credentials' => [
            'key' => $awsKey,
            'secret' => $awsSecret,
        ],
    ]);

    $label = $_GET["label"];
    $voiceId = 'Joanna'; // Change to the desired voice

    try {
        $result = $polly->synthesizeSpeech([
            'OutputFormat' => 'mp3',
            'Text' => $label,
            'VoiceId' => $voiceId,
        ]);
    
        // Return the audio as a blob
        header('Content-Type: audio/mpeg');
        header('Content-Disposition: inline; filename="speech.mp3"');
        echo $result['AudioStream']->getContents();
    } catch (Exception $e) {
        echo "Error synthesizing speech: " . $e->getMessage();
    }
}
?>
