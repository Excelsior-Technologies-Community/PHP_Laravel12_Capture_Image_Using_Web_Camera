<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laravel Webcam Capture & Image Editor</title>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- WebcamJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>

    <!-- Bootstrap -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css"
    >

    <style>

        body {
            background: #f5f7fa;
        }

        .main-container {
            max-width: 1150px;
            margin: 40px auto;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }

        #my_camera {
            width: 490px;
            max-width: 100%;
            margin: auto;
            overflow: hidden;
            border-radius: 10px;
        }

        #results {
            min-height: 350px;
            padding: 15px;

            border: 2px dashed #999;

            background: #f8f9fa;

            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;
        }

        #results img {
            max-width: 100%;
            max-height: 320px;
            border-radius: 8px;
        }

        /*
        |--------------------------------------------------------------------------
        | Editor
        |--------------------------------------------------------------------------
        */

        .editor-card {
            margin-top: 30px;
        }

        .editor-area {
            background: #222;
            padding: 20px;
            border-radius: 10px;

            min-height: 400px;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        #editorCanvas {
            max-width: 100%;
            max-height: 500px;

            background: white;

            border-radius: 6px;
        }

        .editor-controls {
            margin-top: 20px;
        }

        .control-group {
            margin-bottom: 15px;
        }

        .control-group label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        .brightness-value {
            font-weight: bold;
        }

        /*
        |--------------------------------------------------------------------------
        | Gallery
        |--------------------------------------------------------------------------
        */

        .gallery-card {
            margin-top: 35px;
        }

        .gallery-image {
            width: 100%;
            height: 220px;

            object-fit: cover;

            border-radius: 8px;
        }

        .image-card {
            border: 1px solid #ddd;

            border-radius: 10px;

            padding: 10px;

            background: #fff;

            height: 100%;
        }

        .image-name {
            font-size: 13px;

            word-break: break-all;

            margin-top: 10px;

            color: #555;
        }

        .empty-gallery {
            padding: 40px;

            text-align: center;

            color: #777;
        }

        .btn {
            border-radius: 6px;
        }

        .hidden {
            display: none !important;
        }

        .range-wrapper {
            padding: 0 10px;
        }

    </style>

</head>


<body>


<div class="container main-container">


    <!-- ========================================================= -->
    <!-- PAGE HEADER -->
    <!-- ========================================================= -->

    <div class="text-center mb-4">

        <h2>
            📸 Laravel Webcam Capture & Image Editor
        </h2>

        <p class="text-muted">
            Capture, edit, save and manage your webcam images
        </p>

    </div>


    <!-- ========================================================= -->
    <!-- SUCCESS MESSAGE -->
    <!-- ========================================================= -->

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="close"
                data-dismiss="alert"
            >

                <span>&times;</span>

            </button>

        </div>

    @endif


    <!-- ========================================================= -->
    <!-- ERROR MESSAGE -->
    <!-- ========================================================= -->

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button
                type="button"
                class="close"
                data-dismiss="alert"
            >

                <span>&times;</span>

            </button>

        </div>

    @endif


    <!-- ========================================================= -->
    <!-- VALIDATION ERRORS -->
    <!-- ========================================================= -->

    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    <!-- ========================================================= -->
    <!-- WEBCAM CAPTURE -->
    <!-- ========================================================= -->

    <div class="card">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                📷 Capture New Image
            </h5>

        </div>


        <div class="card-body">


            <form
                method="POST"
                action="{{ route('webcam.capture') }}"
                id="captureForm"
            >

                @csrf


                <div class="row">


                    <!-- ================================================= -->
                    <!-- LIVE CAMERA -->
                    <!-- ================================================= -->

                    <div class="col-md-6 text-center">

                        <h6 class="mb-3">
                            Live Camera
                        </h6>


                        <div id="my_camera"></div>


                        <br>


                        <button
                            type="button"
                            class="btn btn-primary"
                            onclick="take_snapshot()"
                        >
                            📸 Take Snapshot
                        </button>


                        <input
                            type="hidden"
                            name="image"
                            class="image-tag"
                            id="imageInput"
                        >

                    </div>



                    <!-- ================================================= -->
                    <!-- CAPTURE PREVIEW -->
                    <!-- ================================================= -->

                    <div class="col-md-6">

                        <h6 class="mb-3 text-center">
                            Captured Preview
                        </h6>


                        <div id="results">

                            <span class="text-muted">

                                Your captured image will appear here...

                            </span>

                        </div>


                    </div>

                </div>



                <!-- ================================================= -->
                <!-- SAVE ORIGINAL BUTTON -->
                <!-- ================================================= -->

                <div class="text-center mt-4">

                    <button
                        type="submit"
                        class="btn btn-success btn-lg"
                    >
                        💾 Save Image
                    </button>

                </div>


            </form>


        </div>

    </div>



    <!-- ========================================================= -->
    <!-- IMAGE EDITOR -->
    <!-- ========================================================= -->

    <div
        class="card editor-card hidden"
        id="editorCard"
    >


        <div class="card-header bg-warning">

            <h5 class="mb-0">
                ✨ Image Editor
            </h5>

        </div>


        <div class="card-body">


            <div class="row">


                <!-- ================================================= -->
                <!-- CANVAS -->
                <!-- ================================================= -->

                <div class="col-md-8">

                    <div class="editor-area">

                        <canvas
                            id="editorCanvas"
                        ></canvas>

                    </div>

                </div>



                <!-- ================================================= -->
                <!-- EDITING CONTROLS -->
                <!-- ================================================= -->

                <div class="col-md-4">


                    <h5 class="mb-3">
                        Editing Tools
                    </h5>


                    <!-- ROTATE -->
                    <div class="control-group">

                        <label>
                            Rotate
                        </label>

                        <div class="btn-group w-100">

                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                onclick="rotateImage(-90)"
                            >
                                ↶ Left
                            </button>

                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                onclick="rotateImage(90)"
                            >
                                ↷ Right
                            </button>

                        </div>

                    </div>



                    <!-- BRIGHTNESS -->
                    <div class="control-group">

                        <label>
                            ☀️ Brightness:
                            <span
                                id="brightnessValue"
                                class="brightness-value"
                            >
                                100%
                            </span>
                        </label>


                        <div class="range-wrapper">

                            <input
                                type="range"
                                class="custom-range"
                                id="brightnessRange"
                                min="50"
                                max="150"
                                value="100"
                                oninput="changeBrightness(this.value)"
                            >

                        </div>

                    </div>



                    <!-- GRAYSCALE -->
                    <div class="control-group">

                        <button
                            type="button"
                            class="btn btn-outline-dark btn-block"
                            onclick="toggleGrayscale()"
                            id="grayscaleButton"
                        >
                            🎨 Apply Grayscale
                        </button>

                    </div>



                    <!-- CROP -->
                    <div class="control-group">

                        <button
                            type="button"
                            class="btn btn-outline-info btn-block"
                            onclick="cropImage()"
                        >
                            ✂️ Crop Center
                        </button>

                    </div>



                    <!-- RESET -->
                    <div class="control-group">

                        <button
                            type="button"
                            class="btn btn-outline-danger btn-block"
                            onclick="resetEditor()"
                        >
                            ↩️ Reset Changes
                        </button>

                    </div>



                    <!-- SAVE EDITED IMAGE -->
                    <div class="control-group mt-4">

                        <button
                            type="button"
                            class="btn btn-success btn-block btn-lg"
                            onclick="saveEditedImage()"
                        >
                            💾 Save Edited Image
                        </button>

                    </div>


                </div>

            </div>


        </div>

    </div>



    <!-- ========================================================= -->
    <!-- IMAGE GALLERY -->
    <!-- ========================================================= -->

    <div class="card gallery-card">


        <div class="card-header bg-dark text-white">

            <h5 class="mb-0">
                🖼️ Captured Image Gallery
            </h5>

        </div>


        <div class="card-body">


            @if($images->count() > 0)


                <div class="row">


                    @foreach($images as $image)


                        <div class="col-md-4 mb-4">


                            <div class="image-card">


                                <!-- IMAGE -->

                                <img
                                    src="{{ asset('uploads/' . $image->getFilename()) }}"
                                    alt="Captured Image"
                                    class="gallery-image"
                                >


                                <!-- FILE NAME -->

                                <div class="image-name">

                                    <strong>
                                        File:
                                    </strong>

                                    {{ $image->getFilename() }}

                                </div>


                                <!-- BUTTONS -->

                                <div class="mt-3 d-flex justify-content-between">


                                    <!-- DOWNLOAD -->

                                    <a
                                        href="{{ route('webcam.download', $image->getFilename()) }}"
                                        class="btn btn-sm btn-primary"
                                    >

                                        📥 Download

                                    </a>



                                    <!-- DELETE -->

                                    <form
                                        method="POST"
                                        action="{{ route('webcam.destroy', $image->getFilename()) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this image?');"
                                    >

                                        @csrf

                                        @method('DELETE')


                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                        >

                                            🗑️ Delete

                                        </button>


                                    </form>


                                </div>


                            </div>


                        </div>


                    @endforeach


                </div>


            @else


                <div class="empty-gallery">

                    <h5>
                        📭 No captured images yet
                    </h5>

                    <p class="mb-0">

                        Take a snapshot using the webcam above
                        to create your first image.

                    </p>

                </div>


            @endif


        </div>

    </div>


</div>



<!-- ============================================================= -->
<!-- BOOTSTRAP JS -->
<!-- ============================================================= -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.min.js"></script>



<script>


/*
|--------------------------------------------------------------------------
| GLOBAL EDITOR VARIABLES
|--------------------------------------------------------------------------
*/

let originalImage = null;

let currentRotation = 0;

let currentBrightness = 100;

let grayscaleEnabled = false;

let currentImageData = null;

const canvas = document.getElementById('editorCanvas');

const ctx = canvas.getContext('2d');



/*
|--------------------------------------------------------------------------
| WEBCAM SETTINGS
|--------------------------------------------------------------------------
*/

Webcam.set({

    width: 490,

    height: 350,

    image_format: 'jpeg',

    jpeg_quality: 90

});


/*
|--------------------------------------------------------------------------
| ATTACH CAMERA
|--------------------------------------------------------------------------
*/

Webcam.attach('#my_camera');



/*
|--------------------------------------------------------------------------
| TAKE SNAPSHOT
|--------------------------------------------------------------------------
*/

function take_snapshot()
{

    Webcam.snap(function(data_uri)
    {

        /*
        |--------------------------------------------------------------------------
        | Store Base64 image
        |--------------------------------------------------------------------------
        */

        $(".image-tag").val(data_uri);


        /*
        |--------------------------------------------------------------------------
        | Show preview
        |--------------------------------------------------------------------------
        */

        document.getElementById('results').innerHTML =
            '<img src="' + data_uri + '" alt="Captured Image">';


        /*
        |--------------------------------------------------------------------------
        | Load image into editor
        |--------------------------------------------------------------------------
        */

        loadImageIntoEditor(data_uri);


    });

}



/*
|--------------------------------------------------------------------------
| LOAD IMAGE INTO EDITOR
|--------------------------------------------------------------------------
*/

function loadImageIntoEditor(data_uri)
{

    originalImage = new Image();


    originalImage.onload = function()
    {

        /*
        |--------------------------------------------------------------------------
        | Reset editor state
        |--------------------------------------------------------------------------
        */

        currentRotation = 0;

        currentBrightness = 100;

        grayscaleEnabled = false;


        document.getElementById('brightnessRange').value = 100;

        document.getElementById('brightnessValue').innerText = '100%';

        document.getElementById('grayscaleButton').innerText =
            '🎨 Apply Grayscale';


        /*
        |--------------------------------------------------------------------------
        | Show editor
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('editorCard')
            .classList
            .remove('hidden');


        /*
        |--------------------------------------------------------------------------
        | Draw image
        |--------------------------------------------------------------------------
        */

        drawEditor();


        /*
        |--------------------------------------------------------------------------
        | Scroll to editor
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('editorCard')
            .scrollIntoView({
                behavior: 'smooth'
            });

    };


    originalImage.src = data_uri;

}



/*
|--------------------------------------------------------------------------
| DRAW EDITOR CANVAS
|--------------------------------------------------------------------------
*/

function drawEditor()
{

    if (!originalImage) {
        return;
    }


    let width = originalImage.width;

    let height = originalImage.height;


    /*
    |--------------------------------------------------------------------------
    | Rotation
    |--------------------------------------------------------------------------
    */

    if (Math.abs(currentRotation) === 90 ||
        Math.abs(currentRotation) === 270)
    {

        canvas.width = height;

        canvas.height = width;

    }
    else
    {

        canvas.width = width;

        canvas.height = height;

    }


    /*
    |--------------------------------------------------------------------------
    | Clear canvas
    |--------------------------------------------------------------------------
    */

    ctx.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    );


    /*
    |--------------------------------------------------------------------------
    | Move to center
    |--------------------------------------------------------------------------
    */

    ctx.save();

    ctx.translate(
        canvas.width / 2,
        canvas.height / 2
    );


    /*
    |--------------------------------------------------------------------------
    | Rotate
    |--------------------------------------------------------------------------
    */

    ctx.rotate(
        currentRotation * Math.PI / 180
    );


    /*
    |--------------------------------------------------------------------------
    | Brightness + Grayscale
    |--------------------------------------------------------------------------
    */

    let filters = [];

    filters.push(
        'brightness(' + currentBrightness + '%)'
    );


    if (grayscaleEnabled)
    {

        filters.push(
            'grayscale(100%)'
        );

    }


    ctx.filter = filters.join(' ');


    /*
    |--------------------------------------------------------------------------
    | Draw Image
    |--------------------------------------------------------------------------
    */

    ctx.drawImage(
        originalImage,
        -width / 2,
        -height / 2
    );


    /*
    |--------------------------------------------------------------------------
    | Restore Context
    |--------------------------------------------------------------------------
    */

    ctx.restore();


    ctx.filter = 'none';

}



/*
|--------------------------------------------------------------------------
| ROTATE IMAGE
|--------------------------------------------------------------------------
*/

function rotateImage(degrees)
{

    if (!originalImage) {
        return;
    }


    currentRotation += degrees;


    /*
    |--------------------------------------------------------------------------
    | Keep rotation between 0 and 360
    |--------------------------------------------------------------------------
    */

    if (currentRotation >= 360)
    {
        currentRotation -= 360;
    }


    if (currentRotation <= -360)
    {
        currentRotation += 360;
    }


    drawEditor();

}



/*
|--------------------------------------------------------------------------
| CHANGE BRIGHTNESS
|--------------------------------------------------------------------------
*/

function changeBrightness(value)
{

    currentBrightness = parseInt(value);


    document.getElementById('brightnessValue').innerText =
        currentBrightness + '%';


    drawEditor();

}



/*
|--------------------------------------------------------------------------
| TOGGLE GRAYSCALE
|--------------------------------------------------------------------------
*/

function toggleGrayscale()
{

    grayscaleEnabled = !grayscaleEnabled;


    const button =
        document.getElementById('grayscaleButton');


    if (grayscaleEnabled)
    {

        button.innerText =
            '🌈 Remove Grayscale';

    }
    else
    {

        button.innerText =
            '🎨 Apply Grayscale';

    }


    drawEditor();

}



/*
|--------------------------------------------------------------------------
| CROP CENTER
|--------------------------------------------------------------------------
*/

function cropImage()
{

    if (!originalImage) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Get current canvas size
    |--------------------------------------------------------------------------
    */

    const cropWidth =
        Math.floor(canvas.width * 0.75);


    const cropHeight =
        Math.floor(canvas.height * 0.75);


    const startX =
        Math.floor(
            (canvas.width - cropWidth) / 2
        );


    const startY =
        Math.floor(
            (canvas.height - cropHeight) / 2
        );


    /*
    |--------------------------------------------------------------------------
    | Get current image
    |--------------------------------------------------------------------------
    */

    const croppedData =
        ctx.getImageData(
            startX,
            startY,
            cropWidth,
            cropHeight
        );


    /*
    |--------------------------------------------------------------------------
    | Resize canvas
    |--------------------------------------------------------------------------
    */

    canvas.width = cropWidth;

    canvas.height = cropHeight;


    /*
    |--------------------------------------------------------------------------
    | Put cropped image
    |--------------------------------------------------------------------------
    */

    ctx.putImageData(
        croppedData,
        0,
        0
    );


    /*
    |--------------------------------------------------------------------------
    | Convert cropped canvas to image
    |--------------------------------------------------------------------------
    */

    currentImageData =
        canvas.toDataURL('image/png');


}



/*
|--------------------------------------------------------------------------
| RESET EDITOR
|--------------------------------------------------------------------------
*/

function resetEditor()
{

    if (!originalImage) {
        return;
    }


    currentRotation = 0;

    currentBrightness = 100;

    grayscaleEnabled = false;


    document.getElementById('brightnessRange').value = 100;

    document.getElementById('brightnessValue').innerText =
        '100%';


    document.getElementById('grayscaleButton').innerText =
        '🎨 Apply Grayscale';


    drawEditor();

}



/*
|--------------------------------------------------------------------------
| SAVE EDITED IMAGE
|--------------------------------------------------------------------------
*/

function saveEditedImage()
{

    if (!originalImage) {

        alert('Please capture an image first.');

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Get edited image from Canvas
    |--------------------------------------------------------------------------
    */

    const editedImage =
        canvas.toDataURL('image/png');


    /*
    |--------------------------------------------------------------------------
    | Put edited image into hidden form field
    |--------------------------------------------------------------------------
    */

    document.getElementById('imageInput').value =
        editedImage;


    /*
    |--------------------------------------------------------------------------
    | Update preview
    |--------------------------------------------------------------------------
    */

    document.getElementById('results').innerHTML =
        '<img src="' +
        editedImage +
        '" alt="Edited Image">';


    /*
    |--------------------------------------------------------------------------
    | Scroll back to save button
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('captureForm')
        .scrollIntoView({
            behavior: 'smooth'
        });


    /*
    |--------------------------------------------------------------------------
    | Confirmation
    |--------------------------------------------------------------------------
    */

    alert(
        'Your edited image is ready. Click "Save Image" to save it.'
    );

}

</script>


</body>

</html>