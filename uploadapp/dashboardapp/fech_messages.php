<?php
$brokers = getenv("KAFKA_BROKERS");
$topic = getenv("KAFKA_TOPIC");
$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', $brokers); // Replace with your Kafka broker's address
$consumer = new RdKafka\Consumer($conf);
$consumer->addBrokers($brokers); // Replace with your Kafka broker's address

$topic = $consumer->newTopic($topic); // Replace with your Kafka topic name

$messages = [];

while (true) {
    $message = $topic->consume(0, 1000); // Poll for messages every 1 second

    if ($message->err) {
        // Handle errors here
    } else {
        $payload = $message->payload;
        if (strpos($payload, "baggage") !== false) {
            $messages[] = $payload;
        }
    }

    if (count($messages) > 0) {
        // Return messages as JSON
        header('Content-Type: application/json');
        echo json_encode($messages);
        break;
    }
}
?>
