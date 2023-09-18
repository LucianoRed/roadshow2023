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
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function speakLabel(label) {
    // Call Amazon Polly to synthesize and speak the label
    $.ajax({
        url: 'polly.php', // Create a separate PHP file to handle Polly synthesis
        type: 'GET',
        data: { label: label },
        responseType: 'blob', // Set the response type to blob
        success: function(response) {
            // Create a Blob URL from the audio blob
            var blob = new Blob([response], { type: 'audio/mpeg' });
            var url = window.URL.createObjectURL(blob);

            // Create an <audio> element and set its source to the Blob URL
            var audio = new Audio(url);

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
<!-- <img src="https://events.redhat.com/accounts/register123/redhat/readhat2/Logo-RedHat-BlackText-Large.png" class="imagem-responsiva"> -->

</head>
<body>
<!-- <img src="https://events.redhat.com/accounts/register123/redhat/readhat2/Logo-RedHat-BlackText-Large.png" alt="Descrição da imagem" class="img-fluid"> -->
<img src="https://events.redhat.com/accounts/register123/redhat/readhat2/Logo-RedHat-BlackText-Large.png" class="imagem-responsiva">


    <div class="container mt-5">
        <h1>Detector de artefatos</h1>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Escolha ou tire uma foto:</label>
                <input type="file" name="image" id="image" class="form-control-file">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Checar Foto</button>
        </form>
    </div>
</body>
</html>
