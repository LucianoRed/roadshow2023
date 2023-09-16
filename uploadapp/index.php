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
        <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Choose an image:</label>
                <input type="file" name="image" id="image" class="form-control-file">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>

    <!-- Modal for Upload Progress -->
    <div id="uploadModal" class="modal" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p>Uploading...</p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="uploadProgressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#uploadForm').submit(function() {
                // Display the modal when the form is submitted
                $('#uploadModal').modal('show');

                // Show progress bar as the file uploads
                let progressBar = $('#uploadProgressBar');
                let formData = new FormData(this);

                $.ajax({
                    xhr: function() {
                        let xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                let percentComplete = (evt.loaded / evt.total) * 100;
                                progressBar.width(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Hide the modal and reset progress bar
                        $('#uploadModal').modal('hide');
                        progressBar.width('0%');

                        // Handle the response from the server
                        alert(response);
                    },
                    error: function() {
                        // Handle the upload error
                        alert('Upload failed. Please try again.');
                    }
                });

                return false;
            });
        });
    </script>
</body>
</html>
