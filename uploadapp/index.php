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

<img src="https://events.redhat.com/accounts/register123/redhat/readhat2/Logo-RedHat-BlackText-Large.png" alt="Descrição da imagem" class="imagem-responsiva">

</head>
<body>
<img src="caminho-da-sua-imagem.jpg" alt="Descrição da imagem" class="img-fluid">

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
