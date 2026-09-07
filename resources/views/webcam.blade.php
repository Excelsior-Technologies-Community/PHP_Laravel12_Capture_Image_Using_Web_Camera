<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Laravel Webcam Capture & Image Editor
    </title>


    <!-- jQuery -->

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js">
    </script>


    <!-- WebcamJS -->

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js">
    </script>


    <!-- Bootstrap -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">


    <style>
        body {
            background: #f5f7fa;
        }


        .main-container {
            max-width: 1200px;
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


        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        .stat-card {
            padding: 20px;

            background: white;

            border-radius: 12px;

            box-shadow: 0 4px 15px rgba(0, 0, 0, .06);

            height: 100%;
        }


        .stat-number {
            font-size: 30px;

            font-weight: 700;
        }


        .stat-title {
            color: #777;

            font-size: 14px;
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        .pagination-wrapper {
            display: flex;

            justify-content: center;

            margin-top: 20px;
        }


        .pagination {
            margin-bottom: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Selection
        |--------------------------------------------------------------------------
        */

        .select-image {
            width: 18px;
            height: 18px;
        }


        .selected-card {
            border: 2px solid #007bff;
        }


        .storage-text {
            font-size: 13px;

            color: #777;
        }
    </style>

</head>


<body>


    <div class="container main-container">


        <!-- ========================================================= -->
        <!-- HEADER -->
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
        <!-- SUCCESS -->
        <!-- ========================================================= -->

        @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="close"
                data-dismiss="alert">

                <span>
                    &times;
                </span>

            </button>

        </div>

        @endif


        <!-- ========================================================= -->
        <!-- ERROR -->
        <!-- ========================================================= -->

        @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button
                type="button"
                class="close"
                data-dismiss="alert">

                <span>
                    &times;
                </span>

            </button>

        </div>

        @endif


        <!-- ========================================================= -->
        <!-- VALIDATION -->
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
        <!-- STATISTICS -->
        <!-- ========================================================= -->

        <div class="row mb-4">


            <!-- TOTAL -->

            <div class="col-md-3 mb-3">

                <div class="stat-card">

                    <div class="stat-number">

                        {{ $totalImages }}

                    </div>

                    <div class="stat-title">

                        📸 Total Images

                    </div>

                </div>

            </div>


            <!-- JPG -->

            <div class="col-md-3 mb-3">

                <div class="stat-card">

                    <div class="stat-number">

                        {{ $jpgImages }}

                    </div>

                    <div class="stat-title">

                        🖼️ JPG / JPEG

                    </div>

                </div>

            </div>


            <!-- PNG -->

            <div class="col-md-3 mb-3">

                <div class="stat-card">

                    <div class="stat-number">

                        {{ $pngImages }}

                    </div>

                    <div class="stat-title">

                        🎨 PNG Images

                    </div>

                </div>

            </div>


            <!-- STORAGE -->

            <div class="col-md-3 mb-3">

                <div class="stat-card">

                    <div class="stat-number">

                        {{ $formattedStorage }}

                    </div>

                    <div class="stat-title">

                        💾 Total Storage

                    </div>

                </div>

            </div>

        </div>


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
                    id="captureForm">

                    @csrf


                    <div class="row">


                        <!-- CAMERA -->

                        <div class="col-md-6 text-center">

                            <h6 class="mb-3">

                                Live Camera

                            </h6>


                            <div id="my_camera"></div>


                            <br>


                            <button
                                type="button"
                                class="btn btn-primary"
                                onclick="take_snapshot()">

                                📸 Take Snapshot

                            </button>


                            <input
                                type="hidden"
                                name="image"
                                class="image-tag"
                                id="imageInput">

                        </div>


                        <!-- PREVIEW -->

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


                    <!-- SAVE -->

                    <div class="text-center mt-4">

                        <button
                            type="submit"
                            class="btn btn-success btn-lg">

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
            id="editorCard">


            <div class="card-header bg-warning">

                <h5 class="mb-0">

                    ✨ Image Editor

                </h5>

            </div>


            <div class="card-body">


                <div class="row">


                    <!-- CANVAS -->

                    <div class="col-md-8">

                        <div class="editor-area">

                            <canvas id="editorCanvas"></canvas>

                        </div>

                    </div>


                    <!-- CONTROLS -->

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
                                    onclick="rotateImage(-90)">

                                    ↶ Left

                                </button>


                                <button
                                    type="button"
                                    class="btn btn-outline-primary"
                                    onclick="rotateImage(90)">

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
                                    class="brightness-value">
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
                                    oninput="changeBrightness(this.value)">

                            </div>

                        </div>


                        <!-- GRAYSCALE -->

                        <div class="control-group">

                            <button
                                type="button"
                                class="btn btn-outline-dark btn-block"
                                onclick="toggleGrayscale()"
                                id="grayscaleButton">

                                🎨 Apply Grayscale

                            </button>

                        </div>


                        <!-- CROP -->

                        <div class="control-group">

                            <button
                                type="button"
                                class="btn btn-outline-info btn-block"
                                onclick="cropImage()">

                                ✂️ Crop Center

                            </button>

                        </div>


                        <!-- RESET -->

                        <div class="control-group">

                            <button
                                type="button"
                                class="btn btn-outline-danger btn-block"
                                onclick="resetEditor()">

                                ↩️ Reset Changes

                            </button>

                        </div>


                        <!-- SAVE EDITED -->

                        <div class="control-group mt-4">

                            <button
                                type="button"
                                class="btn btn-success btn-block btn-lg"
                                onclick="saveEditedImage()">

                                💾 Save Edited Image

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- GALLERY -->
        <!-- ========================================================= -->

        <div class="card gallery-card">


            <div class="card-header bg-dark text-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">

                        🖼️ Captured Image Gallery

                    </h5>

                    @if($totalImages > 0)

                    <span>

                        {{ $totalImages }} images

                    </span>

                    @endif

                </div>

            </div>


            <div class="card-body">


                <!-- ================================================= -->
                <!-- SEARCH + SORT -->
                <!-- ================================================= -->

                <form
                    method="GET"
                    action="{{ route('webcam.index') }}"
                    class="mb-4">

                    <div class="row">


                        <!-- SEARCH -->

                        <div class="col-md-6 mb-2">

                            <div class="input-group">

                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="🔎 Search image..."
                                    value="{{ $search }}">

                                <div class="input-group-append">

                                    <button
                                        class="btn btn-primary"
                                        type="submit">

                                        Search

                                    </button>

                                </div>

                            </div>

                        </div>


                        <!-- SORT -->

                        <div class="col-md-4 mb-2">

                            <select
                                name="sort"
                                class="form-control"
                                onchange="this.form.submit()">

                                <option
                                    value="newest"
                                    {{ $sort === 'newest' ? 'selected' : '' }}>

                                    Newest First

                                </option>


                                <option
                                    value="oldest"
                                    {{ $sort === 'oldest' ? 'selected' : '' }}>

                                    Oldest First

                                </option>


                                <option
                                    value="name_asc"
                                    {{ $sort === 'name_asc' ? 'selected' : '' }}>

                                    Name A-Z

                                </option>


                                <option
                                    value="name_desc"
                                    {{ $sort === 'name_desc' ? 'selected' : '' }}>

                                    Name Z-A

                                </option>


                                <option
                                    value="size_asc"
                                    {{ $sort === 'size_asc' ? 'selected' : '' }}>

                                    Smallest First

                                </option>


                                <option
                                    value="size_desc"
                                    {{ $sort === 'size_desc' ? 'selected' : '' }}>

                                    Largest First

                                </option>

                            </select>

                        </div>


                        <!-- RESET -->

                        <div class="col-md-2 mb-2">

                            <a
                                href="{{ route('webcam.index') }}"
                                class="btn btn-secondary btn-block">

                                Reset

                            </a>

                        </div>

                    </div>

                </form>


                <!-- ================================================= -->
                <!-- BULK ACTIONS -->
                <!-- ================================================= -->

                @if($pagedImages->count() > 0)

                <form
                    method="POST"
                    action="{{ route('webcam.bulkDestroy') }}"
                    id="bulkDeleteForm">

                    @csrf

                    @method('DELETE')


                    <div class="d-flex justify-content-between align-items-center mb-3">


                        <div>

                            <label class="mb-0">

                                <input
                                    type="checkbox"
                                    id="selectAll"
                                    class="select-image mr-2">

                                Select All

                            </label>

                        </div>


                        <div>

                            <button
                                type="submit"
                                class="btn btn-danger btn-sm"
                                onclick="
                                    return confirm(
                                        'Delete all selected images?'
                                    );
                                ">

                                🗑️ Delete Selected

                            </button>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- IMAGE GRID -->
                    <!-- ================================================= -->

                    <div class="row">


                        @foreach($pagedImages as $image)


                        @php

                        $filename =
                        $image->getFilename();

                        $extension =
                        strtoupper(
                        $image->getExtension()
                        );

                        $fileSize =
                        number_format(
                        $image->getSize() / 1024,
                        2
                        );

                        $modified =
                        date(
                        'd M Y h:i A',
                        $image->getMTime()
                        );

                        @endphp


                        <div class="col-md-4 mb-4">


                            <div class="image-card">


                                <!-- SELECT -->

                                <div class="mb-2">

                                    <input
                                        type="checkbox"
                                        name="images[]"
                                        value="{{ $filename }}"
                                        class="select-image image-checkbox">

                                    <span class="ml-2">

                                        Select

                                    </span>

                                </div>


                                <!-- IMAGE -->

                                <img
                                    src="{{ asset('uploads/' . $filename) }}"
                                    alt="Captured Image"
                                    class="gallery-image">


                                <!-- NAME -->

                                <div class="image-name">

                                    <strong>
                                        File:
                                    </strong>

                                    {{ $filename }}

                                </div>


                                <!-- INFO -->

                                <div class="storage-text mt-2">

                                    <div>

                                        📁 Type:
                                        {{ $extension }}

                                    </div>


                                    <div>

                                        💾 Size:
                                        {{ $fileSize }} KB

                                    </div>


                                    <div>

                                        🕒 Created:
                                        {{ $modified }}

                                    </div>

                                </div>


                                <!-- BUTTONS -->

                                <div class="mt-3">


                                    <a
                                        href="{{ route(
                                                'webcam.download',
                                                $filename
                                            ) }}"
                                        class="btn btn-sm btn-primary">

                                        📥 Download

                                    </a>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                                'webcam.destroy',
                                                $filename
                                            ) }}"
                                        class="d-inline"
                                        onsubmit="
                                                return confirm(
                                                    'Are you sure you want to delete this image?'
                                                );
                                            ">

                                        @csrf

                                        @method('DELETE')


                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger">

                                            🗑️ Delete

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>


                        @endforeach


                    </div>


                </form>


                <!-- ================================================= -->
                <!-- PAGINATION -->
                <!-- ================================================= -->

                @if($pagination['last_page'] > 1)

                <div class="pagination-wrapper">


                    <ul class="pagination">


                        @for(
                        $page = 1;
                        $page <= $pagination['last_page'];
                            $page++
                            )


                            <li
                            class="page-item
                                    {{ $page == $pagination['current_page']
                                        ? 'active'
                                        : ''
                                    }}">

                            <a
                                class="page-link"
                                href="{{ route(
                                            'webcam.index',
                                            [
                                                'page' => $page,
                                                'search' => $search,
                                                'sort' => $sort
                                            ]
                                        ) }}">

                                {{ $page }}

                            </a>

                            </li>


                            @endfor


                    </ul>

                </div>

                @endif


                @else


                <div class="empty-gallery">

                    <h5>

                        📭 No captured images found

                    </h5>


                    @if($search)

                    <p>

                        No image matches:

                        <strong>
                            {{ $search }}
                        </strong>

                    </p>


                    <a
                        href="{{ route('webcam.index') }}"
                        class="btn btn-secondary">

                        Clear Search

                    </a>

                    @else

                    <p class="mb-0">

                        Take a snapshot using the webcam
                        above to create your first image.

                    </p>

                    @endif

                </div>

                @endif


            </div>

        </div>


        <!-- ========================================================= -->
        <!-- DELETE ALL -->
        <!-- ========================================================= -->

        @if($totalImages > 0)

        <div class="text-center mt-4 mb-5">

            <form
                method="POST"
                action="{{ route('webcam.destroyAll') }}"
                onsubmit="
                    return confirm(
                        'WARNING: This will permanently delete ALL images. Continue?'
                    );
                ">

                @csrf

                @method('DELETE')


                <button
                    type="submit"
                    class="btn btn-outline-danger">

                    🗑️ Delete All Images

                </button>

            </form>

        </div>

        @endif


    </div>


    <!-- ============================================================= -->
    <!-- BOOTSTRAP JS -->
    <!-- ============================================================= -->

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js">
    </script>


    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.min.js">
    </script>


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


        const canvas =
            document.getElementById('editorCanvas');

        const ctx =
            canvas.getContext('2d');


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

        function take_snapshot() {

            Webcam.snap(function(data_uri) {

                $(".image-tag").val(data_uri);


                document.getElementById('results').innerHTML =
                    '<img src="' +
                    data_uri +
                    '" alt="Captured Image">';


                loadImageIntoEditor(data_uri);

            });

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD IMAGE INTO EDITOR
        |--------------------------------------------------------------------------
        */

        function loadImageIntoEditor(data_uri) {

            originalImage = new Image();


            originalImage.onload = function() {

                currentRotation = 0;

                currentBrightness = 100;

                grayscaleEnabled = false;


                document.getElementById(
                    'brightnessRange'
                ).value = 100;


                document.getElementById(
                    'brightnessValue'
                ).innerText = '100%';


                document.getElementById(
                        'grayscaleButton'
                    ).innerText =
                    '🎨 Apply Grayscale';


                document
                    .getElementById('editorCard')
                    .classList
                    .remove('hidden');


                drawEditor();


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
        | DRAW EDITOR
        |--------------------------------------------------------------------------
        */

        function drawEditor() {

            if (!originalImage) {

                return;

            }


            let width =
                originalImage.width;


            let height =
                originalImage.height;


            if (
                Math.abs(currentRotation) === 90 ||
                Math.abs(currentRotation) === 270
            ) {

                canvas.width = height;

                canvas.height = width;

            } else {

                canvas.width = width;

                canvas.height = height;

            }


            ctx.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );


            ctx.save();


            ctx.translate(
                canvas.width / 2,
                canvas.height / 2
            );


            ctx.rotate(
                currentRotation * Math.PI / 180
            );


            let filters = [];


            filters.push(
                'brightness(' +
                currentBrightness +
                '%)'
            );


            if (grayscaleEnabled) {

                filters.push(
                    'grayscale(100%)'
                );

            }


            ctx.filter =
                filters.join(' ');


            ctx.drawImage(
                originalImage,
                -width / 2,
                -height / 2
            );


            ctx.restore();


            ctx.filter = 'none';

        }


        /*
        |--------------------------------------------------------------------------
        | ROTATE
        |--------------------------------------------------------------------------
        */

        function rotateImage(degrees) {

            if (!originalImage) {

                return;

            }


            currentRotation += degrees;


            if (currentRotation >= 360) {

                currentRotation -= 360;

            }


            if (currentRotation <= -360) {

                currentRotation += 360;

            }


            drawEditor();

        }


        /*
        |--------------------------------------------------------------------------
        | BRIGHTNESS
        |--------------------------------------------------------------------------
        */

        function changeBrightness(value) {

            currentBrightness =
                parseInt(value);


            document.getElementById(
                    'brightnessValue'
                ).innerText =
                currentBrightness + '%';


            drawEditor();

        }


        /*
        |--------------------------------------------------------------------------
        | GRAYSCALE
        |--------------------------------------------------------------------------
        */

        function toggleGrayscale() {

            grayscaleEnabled = !grayscaleEnabled;


            const button =
                document.getElementById(
                    'grayscaleButton'
                );


            if (grayscaleEnabled) {

                button.innerText =
                    '🌈 Remove Grayscale';

            } else {

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

        function cropImage() {

            if (!originalImage) {

                return;

            }


            const cropWidth =
                Math.floor(
                    canvas.width * 0.75
                );


            const cropHeight =
                Math.floor(
                    canvas.height * 0.75
                );


            const startX =
                Math.floor(
                    (canvas.width - cropWidth) / 2
                );


            const startY =
                Math.floor(
                    (canvas.height - cropHeight) / 2
                );


            const croppedData =
                ctx.getImageData(
                    startX,
                    startY,
                    cropWidth,
                    cropHeight
                );


            canvas.width =
                cropWidth;


            canvas.height =
                cropHeight;


            ctx.putImageData(
                croppedData,
                0,
                0
            );


            currentImageData =
                canvas.toDataURL(
                    'image/png'
                );

        }


        /*
        |--------------------------------------------------------------------------
        | RESET EDITOR
        |--------------------------------------------------------------------------
        */

        function resetEditor() {

            if (!originalImage) {

                return;

            }


            currentRotation = 0;

            currentBrightness = 100;

            grayscaleEnabled = false;


            document.getElementById(
                'brightnessRange'
            ).value = 100;


            document.getElementById(
                'brightnessValue'
            ).innerText = '100%';


            document.getElementById(
                    'grayscaleButton'
                ).innerText =
                '🎨 Apply Grayscale';


            drawEditor();

        }


        /*
        |--------------------------------------------------------------------------
        | SAVE EDITED IMAGE
        |--------------------------------------------------------------------------
        */

        function saveEditedImage() {

            if (!originalImage) {

                alert(
                    'Please capture an image first.'
                );

                return;

            }


            const editedImage =
                canvas.toDataURL(
                    'image/png'
                );


            document.getElementById(
                    'imageInput'
                ).value =
                editedImage;


            document.getElementById(
                    'results'
                ).innerHTML =
                '<img src="' +
                editedImage +
                '" alt="Edited Image">';


            document
                .getElementById('captureForm')
                .scrollIntoView({
                    behavior: 'smooth'
                });


            alert(
                'Your edited image is ready. Click "Save Image" to save it.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SELECT ALL
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const selectAll =
                    document.getElementById(
                        'selectAll'
                    );


                if (!selectAll) {

                    return;

                }


                selectAll.addEventListener(
                    'change',
                    function() {

                        const checkboxes =
                            document.querySelectorAll(
                                '.image-checkbox'
                            );


                        checkboxes.forEach(
                            function(checkbox) {

                                checkbox.checked =
                                    selectAll.checked;

                            }
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Update Select All
                |--------------------------------------------------------------------------
                */

                const checkboxes =
                    document.querySelectorAll(
                        '.image-checkbox'
                    );


                checkboxes.forEach(
                    function(checkbox) {

                        checkbox.addEventListener(
                            'change',
                            function() {

                                const checked =
                                    document.querySelectorAll(
                                        '.image-checkbox:checked'
                                    ).length;


                                selectAll.checked =
                                    checked === checkboxes.length;

                            }
                        );

                    }
                );

            }
        );
    </script>


</body>

</html>