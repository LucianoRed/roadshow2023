<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload App</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Image Upload</h1>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Choose an image:</label>
                <input type="file" name="image" id="image" class="form-control-file">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</body>
</html>
