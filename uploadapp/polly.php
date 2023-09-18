<?php
// require 'vendor/autoload.php'; // Include the AWS SDK for PHP
require_once 'aws/aws-autoloader.php';

use Aws\Polly\PollyClient;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["label"])) {
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

    $label = $_POST["label"];
    $voiceId = 'Joanna'; // Change to the desired voice

    try {
        $result = $polly->synthesizeSpeech([
            'OutputFormat' => 'mp3',
            'Text' => $label,
            'VoiceId' => $voiceId,
        ]);

        // Play the synthesized audio (you may want to save it or serve it differently)
        header('Content-Type: audio/mpeg');
        echo $result['AudioStream']->getContents();
    } catch (Exception $e) {
        echo "Error synthesizing speech: " . $e->getMessage();
    }
}
?>
