<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Webcam Capture & Image Editor</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root { --bg-primary: #f5f7fa; --bg-secondary: #fff; --text-primary: #212529; --text-secondary: #6c757d; --border-color: #dee2e6; --card-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .dark-mode { --bg-primary: #1a1a2e; --bg-secondary: #16213e; --text-primary: #e0e0e0; --text-secondary: #b0b0b0; --border-color: #533483; --card-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        body { background: var(--bg-primary); color: var(--text-primary); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; transition: all 0.3s; }
        .main-container { max-width: 1200px; margin: 20px auto; padding: 0 15px; }
        .card { border: none; border-radius: 12px; box-shadow: var(--card-shadow); background: var(--bg-secondary); transition: all 0.3s; margin-bottom: 20px; }
        .card-header { border-radius: 12px 12px 0 0 !important; }
        #my_camera { width: 100%; max-width: 490px; margin: auto; overflow: hidden; border-radius: 10px; }
        #results { min-height: 350px; padding: 15px; border: 2px dashed #999; background: #f8f9fa; border-radius: 10px; display: flex; align-items: center; justify-content: center; text-align: center; }
        #results img { max-width: 100%; max-height: 320px; border-radius: 8px; }
        .editor-card { margin-top: 30px; }
        .editor-area { background: #222; padding: 20px; border-radius: 10px; min-height: 400px; display: flex; align-items: center; justify-content: center; }
        #editorCanvas { max-width: 100%; max-height: 500px; background: white; border-radius: 6px; }
        .editor-controls { margin-top: 20px; }
        .control-group { margin-bottom: 15px; }
        .control-group label { font-weight: 600; display: block; margin-bottom: 5px; }
        .brightness-value { font-weight: bold; }
        .gallery-card { margin-top: 35px; }
        .gallery-image { width: 100%; height: 220px; object-fit: cover; border-radius: 8px; cursor: pointer; }
        .image-card { border: 1px solid var(--border-color); border-radius: 10px; padding: 10px; background: var(--bg-secondary); height: 100%; transition: transform 0.2s; }
        .image-card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
        .image-name { font-size: 13px; word-break: break-all; margin-top: 10px; color: var(--text-secondary); }
        .tag { font-size: 11px; padding: 2px 8px; border-radius: 12px; background: #e9ecef; color: #495057; }
        .tag.active { background: #007bff; color: white; }
        .sidebar { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); height: fit-content; }
        .bulk-actions { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .empty-state { text-align: center; padding: 60px 20px; color: #777; }
        .stats-card { border-radius: 10px; padding: 15px; text-align: center; }
        .action-btn { padding: 4px 8px; font-size: 12px; margin: 2px; }
        .dark-mode-toggle { cursor: pointer; padding: 8px 15px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-secondary); color: var(--text-primary); }
        .camera-controls { display: flex; gap: 10px; margin-bottom: 15px; justify-content: center; flex-wrap: wrap; }
        .timer-display { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 100px; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.8); display: none; z-index: 1000; }
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
        .loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 9998; }
        .spinner { width: 50px; height: 50px; border: 5px solid #fff; border-top-color: #007bff; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .fullscreen-mode { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--bg-primary); z-index: 9997; padding: 20px; overflow-y: auto; }
        .fullscreen-mode .card { margin-bottom: 20px; }
        .mobile-optimized { padding: 10px; }
        .drag-drop-zone { border: 2px dashed var(--border-color); border-radius: 12px; padding: 30px; text-align: center; margin-bottom: 20px; transition: all 0.3s; }
        .drag-drop-zone.dragover { border-color: #007bff; background: rgba(0,123,255,0.1); }
        .shortcut-key { background: #e9ecef; padding: 2px 8px; border-radius: 4px; font-family: monospace; font-size: 12px; }
        .image-count-badge { background: #007bff; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 5px; }
        @media (max-width: 768px) {
            .main-container { margin: 10px auto; }
            .card { margin-bottom: 15px; }
            .gallery-image { height: 180px; }
            .btn-group { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    <div class="timer-display" id="timerDisplay">3</div>

    <div class="container main-container" id="mainContainer">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>📸 Laravel Webcam Capture & Image Editor</h2>
                <p class="text-muted">Capture, edit, save and manage your webcam images</p>
            </div>
            <div>
                <button class="dark-mode-toggle" onclick="toggleDarkMode()">🌙 Dark Mode</button>
                <button class="dark-mode-toggle" onclick="toggleFullscreen()">⛶ Fullscreen</button>
                <span class="image-count-badge" id="imageCount">0</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" id="successAlert">{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" id="errorAlert">{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <div class="card" id="captureCard">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📷 Capture New Image</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('webcam.capture') }}" id="captureForm" enctype="multipart/form-data">
                    @csrf
                    <div class="camera-controls">
                        <select id="cameraSelect" class="form-control" style="width: auto;">
                            <option value="">Default Camera</option>
                        </select>
                        <button type="button" class="btn btn-primary" onclick="startCamera()">Start Camera</button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchCamera()">🔄 Switch Camera</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleMirror()">🪞 Mirror</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-warning" onclick="startTimer(3)">⏱️ 3s</button>
                            <button type="button" class="btn btn-outline-warning" onclick="startTimer(5)">5s</button>
                            <button type="button" class="btn btn-outline-warning" onclick="startTimer(10)">10s</button>
                        </div>
                        <button type="button" class="btn btn-success" onclick="take_snapshot()">📸 Take Snapshot</button>
                    </div>
                    <div class="drag-drop-zone" id="dropZone">
                        <p>Drag & Drop images here or click to upload</p>
                        <input type="file" id="fileInput" accept="image/*" multiple style="display: none;">
                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">Choose Files</button>
                    </div>
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <h6 class="mb-3">Live Camera</h6>
                            <div id="my_camera"></div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3 text-center">Captured Preview</h6>
                            <div id="results"><span class="text-muted">Your captured image will appear here...</span></div>
                        </div>
                    </div>
                    <input type="hidden" name="image" class="image-tag" id="imageInput">
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg">💾 Save Image</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card editor-card hidden" id="editorCard">
            <div class="card-header bg-warning">
                <h5 class="mb-0">✨ Image Editor</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="editor-area">
                            <canvas id="editorCanvas"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h5 class="mb-3">Editing Tools</h5>
                        <div class="control-group">
                            <label>Flip</label>
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-outline-primary" onclick="flipImage('horizontal')">↔ Horizontal</button>
                                <button type="button" class="btn btn-outline-primary" onclick="flipImage('vertical')">↕ Vertical</button>
                            </div>
                        </div>
                        <div class="control-group">
                            <label>Rotate</label>
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-outline-primary" onclick="rotateImage(-90)">↶ Left</button>
                                <button type="button" class="btn btn-outline-primary" onclick="rotateImage(90)">↷ Right</button>
                            </div>
                        </div>
                        <div class="control-group">
                            <label>☀️ Brightness: <span id="brightnessValue" class="brightness-value">100%</span></label>
                            <input type="range" class="custom-range" id="brightnessRange" min="50" max="150" value="100" oninput="changeBrightness(this.value)">
                        </div>
                        <div class="control-group">
                            <label>Contrast: <span id="contrastValue" class="brightness-value">100%</span></label>
                            <input type="range" class="custom-range" id="contrastRange" min="50" max="150" value="100" oninput="changeContrast(this.value)">
                        </div>
                        <div class="control-group">
                            <label>Saturation: <span id="saturationValue" class="brightness-value">100%</span></label>
                            <input type="range" class="custom-range" id="saturationRange" min="0" max="200" value="100" oninput="changeSaturation(this.value)">
                        </div>
                        <div class="control-group">
                            <label>Blur: <span id="blurValue" class="brightness-value">0px</span></label>
                            <input type="range" class="custom-range" id="blurRange" min="0" max="20" value="0" oninput="changeBlur(this.value)">
                        </div>
                        <div class="control-group">
                            <label>Resize</label>
                            <div class="input-group">
                                <input type="number" id="resizeWidth" class="form-control" placeholder="Width">
                                <input type="number" id="resizeHeight" class="form-control" placeholder="Height">
                                <button type="button" class="btn btn-outline-info" onclick="resizeImage()">Resize</button>
                            </div>
                        </div>
                        <div class="control-group">
                            <label>Filters</label>
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-sm btn-outline-dark" onclick="applyFilter('grayscale')">B&W</button>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="applyFilter('sepia')">Sepia</button>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="applyFilter('invert')">Invert</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="applyFilter('vintage')">Vintage</button>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="applyFilter('cool')">Cool</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="applyFilter('warm')">Warm</button>
                            </div>
                        </div>
                        <div class="control-group">
                            <label>Crop Aspect Ratio</label>
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-outline-info" onclick="setCropRatio(1)">1:1</button>
                                <button type="button" class="btn btn-outline-info" onclick="setCropRatio(16/9)">16:9</button>
                                <button type="button" class="btn btn-outline-info" onclick="setCropRatio(4/3)">4:3</button>
                            </div>
                            <button type="button" class="btn btn-outline-info btn-block mt-2" onclick="cropImage()">✂️ Crop Center</button>
                        </div>
                        <div class="control-group">
                            <label>Watermark</label>
                            <input type="text" id="watermarkText" class="form-control mb-2" placeholder="Enter watermark text">
                            <button type="button" class="btn btn-outline-dark btn-block" onclick="addWatermark()">Add Watermark</button>
                        </div>
                        <div class="control-group">
                            <label>Text Overlay</label>
                            <input type="text" id="overlayText" class="form-control mb-2" placeholder="Enter text">
                            <input type="color" id="textColor" class="form-control mb-2" value="#ffffff">
                            <button type="button" class="btn btn-outline-dark btn-block" onclick="addTextOverlay()">Add Text</button>
                        </div>
                        <div class="control-group">
                            <label>Undo/Redo</label>
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-outline-secondary" onclick="undo()">↩️ Undo</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="redo()">↪️ Redo</button>
                            </div>
                        </div>
                        <div class="control-group">
                            <button type="button" class="btn btn-outline-danger btn-block" onclick="resetEditor()">↩️ Reset Changes</button>
                        </div>
                        <div class="control-group mt-4">
                            <button type="button" class="btn btn-success btn-block btn-lg" onclick="saveEditedImage()">💾 Save Edited Image</button>
                        </div>
                        <div class="control-group">
                            <button type="button" class="btn btn-secondary btn-block" onclick="printImage()">🖨️ Print</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card gallery-card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">🖼️ Captured Image Gallery <span class="image-count-badge">{{ $images->count() }}</span></h5>
            </div>
            <div class="card-body">
                @if($images->count() > 0)
                    <div class="row">
                        @foreach($images as $image)
                            <div class="col-md-4 mb-4">
                                <div class="image-card">
                                    <img src="{{ asset('uploads/' . $image->getFilename()) }}" alt="Captured Image" class="gallery-image" onclick="window.location='{{ route('gallery.lightbox', $image->getFilename()) }}'">
                                    <div class="image-name">
                                        <strong>File:</strong> {{ $image->getFilename() }}<br>
                                        <strong>Size:</strong> {{ $image->getSize() }} bytes<br>
                                        <strong>Date:</strong> {{ $image->getMTime() }}
                                    </div>
                                    <div class="mt-3 d-flex justify-content-between">
                                        <a href="{{ route('webcam.download', $image->getFilename()) }}" class="btn btn-sm btn-primary">📥 Download</a>
                                        <form method="POST" action="{{ route('webcam.destroy', $image->getFilename()) }}" onsubmit="return confirm('Are you sure?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">🗑️ Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-gallery">
                        <h5>📭 No captured images yet</h5>
                        <p class="mb-0">Take a snapshot using the webcam above to create your first image.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script>
        let originalImage = null, currentRotation = 0, currentBrightness = 100, currentContrast = 100, currentSaturation = 100, currentBlur = 0, grayscaleEnabled = false, flipHorizontal = false, flipVertical = false, currentImageData = null, historyStack = [], historyIndex = -1, cropRatio = null, isMirrored = false;
        const canvas = document.getElementById('editorCanvas'), ctx = canvas.getContext('2d');
        Webcam.set({ width: 490, height: 350, image_format: 'jpeg', jpeg_quality: 90 });
        Webcam.attach('#my_camera');
        updateImageCount();

        function toggleDarkMode() { document.body.classList.toggle('dark-mode'); localStorage.setItem('darkMode', document.body.classList.contains('dark-mode')); }
        if (localStorage.getItem('darkMode') === 'true') document.body.classList.add('dark-mode');
        function toggleFullscreen() { document.getElementById('mainContainer').classList.toggle('fullscreen-mode'); }
        function showToast(message, type = 'success') { const toast = document.createElement('div'); toast.className = 'alert alert-' + type + ' alert-dismissible fade show'; toast.innerHTML = message + '<button type="button" class="close" data-dismiss="alert">&times;</button>'; document.getElementById('toastContainer').appendChild(toast); setTimeout(() => toast.remove(), 3000); }
        function showLoading() { document.getElementById('loadingOverlay').style.display = 'flex'; }
        function hideLoading() { document.getElementById('loadingOverlay').style.display = 'none'; }
        function updateImageCount() { const count = {{ $images->count() }}; document.getElementById('imageCount').textContent = count + ' images'; }

        function startCamera() { Webcam.attach('#my_camera'); showToast('Camera started'); }
        async function switchCamera() { try { const devices = await navigator.mediaDevices.enumerateDevices(); const videoDevices = devices.filter(d => d.kind === 'videoinput'); if (videoDevices.length > 1) { Webcam.set({ videoDeviceId: videoDevices[1].deviceId }); Webcam.attach('#my_camera'); showToast('Camera switched'); } else { showToast('Only one camera found', 'error'); } } catch (e) { showToast('Could not switch camera', 'error'); } }
        function toggleMirror() { isMirrored = !isMirrored; if (originalImage) drawEditor(); showToast(isMirrored ? 'Mirror mode ON' : 'Mirror mode OFF'); }
        function startTimer(seconds) { let remaining = seconds; const display = document.getElementById('timerDisplay'); display.style.display = 'block'; display.textContent = remaining; const interval = setInterval(() => { remaining--; if (remaining > 0) { display.textContent = remaining; } else { clearInterval(interval); display.style.display = 'none'; take_snapshot(); } }, 1000); }

        function take_snapshot() { Webcam.snap(function(data_uri) { $('.image-tag').val(data_uri); document.getElementById('results').innerHTML = '<img src="' + data_uri + '" alt="Captured Image">'; loadImageIntoEditor(data_uri); }); }

        function loadImageIntoEditor(data_uri) {
            originalImage = new Image();
            originalImage.onload = function() {
                currentRotation = 0; currentBrightness = 100; currentContrast = 100; currentSaturation = 100; currentBlur = 0; grayscaleEnabled = false; flipHorizontal = false; flipVertical = false; cropRatio = null;
                document.getElementById('brightnessRange').value = 100; document.getElementById('brightnessValue').innerText = '100%';
                document.getElementById('contrastRange').value = 100; document.getElementById('contrastValue').innerText = '100%';
                document.getElementById('saturationRange').value = 100; document.getElementById('saturationValue').innerText = '100%';
                document.getElementById('blurRange').value = 0; document.getElementById('blurValue').innerText = '0px';
                document.getElementById('grayscaleButton').innerText = '🎨 Apply Grayscale';
                document.getElementById('editorCard').classList.remove('hidden');
                historyStack = []; historyIndex = -1; saveState(); drawEditor();
                document.getElementById('editorCard').scrollIntoView({ behavior: 'smooth' });
            };
            originalImage.src = data_uri;
        }

        function saveState() { historyIndex++; historyStack = historyStack.slice(0, historyIndex); historyStack.push(canvas.toDataURL()); }
        function undo() { if (historyIndex > 0) { historyIndex--; restoreState(); } }
        function redo() { if (historyIndex < historyStack.length - 1) { historyIndex++; restoreState(); } }
        function restoreState() { const img = new Image(); img.onload = function() { canvas.width = img.width; canvas.height = img.height; ctx.drawImage(img, 0, 0); currentImageData = canvas.toDataURL(); }; img.src = historyStack[historyIndex]; }

        function drawEditor() {
            if (!originalImage) return;
            let width = originalImage.width, height = originalImage.height;
            if (Math.abs(currentRotation) === 90 || Math.abs(currentRotation) === 270) { canvas.width = height; canvas.height = width; } else { canvas.width = width; canvas.height = height; }
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.save();
            ctx.translate(canvas.width / 2, canvas.height / 2);
            ctx.rotate(currentRotation * Math.PI / 180);
            let filters = ['brightness(' + currentBrightness + '%)', 'contrast(' + currentContrast + '%)', 'saturate(' + currentSaturation + '%)'];
            if (currentBlur > 0) filters.push('blur(' + currentBlur + 'px)');
            if (grayscaleEnabled) filters.push('grayscale(100%)');
            if (currentFilter) filters.push(currentFilter);
            ctx.filter = filters.join(' ');
            let sx = 0, sy = 0, sw = width, sh = height, dx = -width/2, dy = -height/2, dw = width, dh = height;
            if (flipHorizontal) { dx = width/2; ctx.scale(-1, 1); }
            if (flipVertical) { dy = height/2; ctx.scale(1, -1); }
            ctx.drawImage(originalImage, dx, dy, dw, dh);
            ctx.restore();
            ctx.filter = 'none';
            currentImageData = canvas.toDataURL();
        }

        let currentFilter = null;
        function applyFilter(filter) {
            if (currentFilter === filter) { currentFilter = null; showToast('Filter removed'); }
            else { currentFilter = filter; showToast(filter.charAt(0).toUpperCase() + filter.slice(1) + ' filter applied'); }
            drawEditor();
        }

        function rotateImage(degrees) { if (!originalImage) return; currentRotation += degrees; if (currentRotation >= 360) currentRotation -= 360; if (currentRotation <= -360) currentRotation += 360; drawEditor(); saveState(); }
        function flipImage(direction) { if (!originalImage) return; if (direction === 'horizontal') flipHorizontal = !flipHorizontal; if (direction === 'vertical') flipVertical = !flipVertical; drawEditor(); saveState(); }
        function changeBrightness(value) { currentBrightness = parseInt(value); document.getElementById('brightnessValue').innerText = currentBrightness + '%'; drawEditor(); }
        function changeContrast(value) { currentContrast = parseInt(value); document.getElementById('contrastValue').innerText = currentContrast + '%'; drawEditor(); }
        function changeSaturation(value) { currentSaturation = parseInt(value); document.getElementById('saturationValue').innerText = currentSaturation + '%'; drawEditor(); }
        function changeBlur(value) { currentBlur = parseInt(value); document.getElementById('blurValue').innerText = currentBlur + 'px'; drawEditor(); }
        function toggleGrayscale() { grayscaleEnabled = !grayscaleEnabled; const button = document.getElementById('grayscaleButton'); button.innerText = grayscaleEnabled ? '🌈 Remove Grayscale' : '🎨 Apply Grayscale'; drawEditor(); }
        function setCropRatio(ratio) { cropRatio = ratio; showToast('Crop ratio set: ' + (ratio === 1 ? '1:1' : ratio === 16/9 ? '16:9' : '4:3')); }
        function cropImage() {
            if (!originalImage) return;
            let cropWidth, cropHeight;
            if (cropRatio) { cropWidth = canvas.width; cropHeight = canvas.width / cropRatio; if (cropHeight > canvas.height) { cropHeight = canvas.height; cropWidth = cropHeight * cropRatio; } } else { cropWidth = Math.floor(canvas.width * 0.75); cropHeight = Math.floor(canvas.height * 0.75); }
            const startX = Math.floor((canvas.width - cropWidth) / 2), startY = Math.floor((canvas.height - cropHeight) / 2);
            const croppedData = ctx.getImageData(startX, startY, cropWidth, cropHeight);
            canvas.width = cropWidth; canvas.height = cropHeight;
            ctx.putImageData(croppedData, 0, 0);
            currentImageData = canvas.toDataURL('image/png');
            saveState();
        }
        function resizeImage() { const newWidth = parseInt(document.getElementById('resizeWidth').value); const newHeight = parseInt(document.getElementById('resizeHeight').value); if (!newWidth || !newHeight) { showToast('Please enter width and height', 'error'); return; } const tempCanvas = document.createElement('canvas'); tempCanvas.width = newWidth; tempCanvas.height = newHeight; const tempCtx = tempCanvas.getContext('2d'); tempCtx.drawImage(canvas, 0, 0, newWidth, newHeight); canvas.width = newWidth; canvas.height = newHeight; ctx.drawImage(tempCanvas, 0, 0); saveState(); showToast('Image resized'); }
        function addWatermark() { const text = document.getElementById('watermarkText').value; if (!text) { showToast('Please enter watermark text', 'error'); return; } ctx.font = '30px Arial'; ctx.fillStyle = 'rgba(255,255,255,0.5)'; ctx.fillText(text, 20, canvas.height - 20); saveState(); showToast('Watermark added'); }
        function addTextOverlay() { const text = document.getElementById('overlayText').value; if (!text) { showToast('Please enter text', 'error'); return; } const x = canvas.width / 2; const y = canvas.height / 2; ctx.font = '40px Arial'; ctx.fillStyle = document.getElementById('textColor').value; ctx.textAlign = 'center'; ctx.fillText(text, x, y); ctx.textAlign = 'start'; saveState(); showToast('Text overlay added'); }
        function resetEditor() { if (!originalImage) return; currentRotation = 0; currentBrightness = 100; currentContrast = 100; currentSaturation = 100; currentBlur = 0; grayscaleEnabled = false; flipHorizontal = false; flipVertical = false; currentFilter = null; cropRatio = null; document.getElementById('brightnessRange').value = 100; document.getElementById('brightnessValue').innerText = '100%'; document.getElementById('contrastRange').value = 100; document.getElementById('contrastValue').innerText = '100%'; document.getElementById('saturationRange').value = 100; document.getElementById('saturationValue').innerText = '100%'; document.getElementById('blurRange').value = 0; document.getElementById('blurValue').innerText = '0px'; document.getElementById('grayscaleButton').innerText = '🎨 Apply Grayscale'; historyStack = []; historyIndex = -1; saveState(); drawEditor(); }
        function saveEditedImage() { if (!originalImage) { alert('Please capture an image first.'); return; } const editedImage = canvas.toDataURL('image/png'); document.getElementById('imageInput').value = editedImage; document.getElementById('results').innerHTML = '<img src="' + editedImage + '" alt="Edited Image">'; document.getElementById('captureForm').scrollIntoView({ behavior: 'smooth' }); showToast('Edited image ready! Click Save Image to store it.'); }
        function printImage() { if (!originalImage) { showToast('Please capture an image first', 'error'); return; } const dataUrl = canvas.toDataURL(); const win = window.open(); win.document.write('<img src="' + dataUrl + '" onload="window.print();window.close();">'); }

        document.getElementById('dropZone').addEventListener('dragover', (e) => { e.preventDefault(); e.currentTarget.classList.add('dragover'); });
        document.getElementById('dropZone').addEventListener('dragleave', (e) => { e.currentTarget.classList.remove('dragover'); });
        document.getElementById('dropZone').addEventListener('drop', (e) => { e.preventDefault(); e.currentTarget.classList.remove('dragover'); const files = e.dataTransfer.files; if (files.length > 0) handleFiles(files); });
        document.getElementById('fileInput').addEventListener('change', (e) => { if (e.target.files.length > 0) handleFiles(e.target.files); });
        function handleFiles(files) { Array.from(files).forEach(file => { if (!file.type.startsWith('image/')) return; const reader = new FileReader(); reader.onload = function(e) { document.getElementById('results').innerHTML = '<img src="' + e.target.result + '" alt="Uploaded Image">'; document.getElementById('imageInput').value = e.target.result; loadImageIntoEditor(e.target.result); }; reader.readAsDataURL(file); }); }

        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'z') { e.preventDefault(); undo(); }
            if (e.ctrlKey && e.key === 'y') { e.preventDefault(); redo(); }
            if (e.ctrlKey && e.key === 's') { e.preventDefault(); saveEditedImage(); }
            if (e.key === 'Escape') { document.getElementById('mainContainer').classList.remove('fullscreen-mode'); }
        });
    </script>
</body>
</html>
