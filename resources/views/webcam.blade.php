<!DOCTYPE html>
<html>
<head>
    <title>Laravel Webcam Capture Example</title>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- WebcamJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />

    <style>
        #results {
            padding: 20px;
            border: 1px solid #333;
            background: #eee;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mt-4">📸 Laravel Webcam Capture and Save</h2>

    <form method="POST" action="{{ route('webcam.capture') }}">
        @csrf

        <div class="row mt-4">

            <!-- Webcam preview -->
            <div class="col-md-6">
                <div id="my_camera"></div>
                <br>
                <input type="button" class="btn btn-primary" value="Take Snapshot" onClick="take_snapshot()">
                <input type="hidden" name="image" class="image-tag">
            </div>

            <!-- Captured image -->
            <div class="col-md-6">
                <div id="results">Your captured image will appear here...</div>
            </div>

            <div class="col-md-12 text-center mt-3">
                <button class="btn btn-success">Submit</button>
            </div>
        </div>
    </form>
</div>

<script>

// Webcam settings
Webcam.set({
    width: 490,
    height: 350,
    image_format: 'jpeg',
    jpeg_quality: 90
});

// Attach camera to div
Webcam.attach('#my_camera');

// Capture and display image
function take_snapshot() {
    Webcam.snap(function(data_uri) {

        // Save base64 to hidden input
        $(".image-tag").val(data_uri);

        // Show preview
        document.getElementById('results').innerHTML =
            '<img src="' + data_uri + '"/>';
    });
}

</script>

</body>
</html>
