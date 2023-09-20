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

<!-- <img src="https://events.redhat.com/accounts/register123/redhat/readhat2/Logo-RedHat-BlackText-Large.png" class="imagem-responsiva"> -->

</head>
<body>
<!-- <img src="https://events.redhat.com/accounts/register123/redhat/readhat2/Logo-RedHat-BlackText-Large.png" alt="Descrição da imagem" class="img-fluid"> -->
<img src="opentalk.jpg" class="imagem-responsiva">


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
