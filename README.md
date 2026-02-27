# PHP_Laravel12_Capture_Image_Using_Web_Camera

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/WebcamJS-1.0.25-blue?style=for-the-badge">
  <img src="https://img.shields.io/badge/Image-Capture-success?style=for-the-badge">
</p>

---

##  Overview  
This project demonstrates how to **capture images from a webcam** using **WebcamJS** in Laravel and save them inside the `public/uploads` folder.

### ✔ No database needed  
### ✔ Pure frontend + backend file saving  
### ✔ Works on all major browsers  
### ✔ Simple & developer-friendly  

---

##  Features  

###  Webcam Features  
- Live webcam preview  
- Take snapshot instantly  
- Show preview after capture  
- High-quality JPEG/PNG capturing  

###  Storage Features  
- Saves images to:  
  ```
  public/uploads/
  ```
- Auto-creates folder if missing  
- Generates unique filenames  

###  Technical Features  
- Uses **WebcamJS**  
- Base64 → file conversion in Laravel  
- Simple controller & Blade integration  
- No migration or database required  

---

##  Folder Structure  

```
app/
├── Http/
│   └── Controllers/
│       └── WebcamController.php

resources/
└── views/
    └── webcam.blade.php

routes/
└── web.php

public/
└── uploads/
    └── *.png / *.jpg
```

---

#  Step 1 — Install Laravel  

```bash
composer create-project laravel/laravel webcam-app
cd webcam-app
```

---

#  Step 2 — Update .env (Optional)

```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxx
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

```

---

#  Step 3 — Add Routes  

 **routes/web.php**

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebcamController;

Route::get('webcam', [WebcamController::class, 'index']);
Route::post('webcam', [WebcamController::class, 'store'])->name('webcam.capture');
```

---

#  Step 4 — Create Controller  

Run:

```bash
php artisan make:controller WebcamController
```

 **app/Http/Controllers/WebcamController.php**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebcamController extends Controller
{
    public function index()
    {
        return view('webcam');
    }

    public function store(Request $request)
    {
        $img = $request->image;

        $folderPath = public_path('uploads/');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_base64 = base64_decode($image_parts[1]);

        $fileName = uniqid() . '.png';
        $file = $folderPath . $fileName;

        file_put_contents($file, $image_base64);

        dd("Image uploaded successfully: uploads/" . $fileName);
    }
}
```

---

#  Step 5 — Create Blade View (UI)  

 **resources/views/webcam.blade.php**

```html
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Webcam Capture Example</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />

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
    <h2 class="text-center mt-4">📸 Laravel Webcam Capture & Save</h2>

    <form method="POST" action="{{ route('webcam.capture') }}">
        @csrf

        <div class="row mt-4">

            <div class="col-md-6">
                <div id="my_camera"></div>
                <br>
                <input type="button" class="btn btn-primary" value="Take Snapshot" onClick="take_snapshot()">
                <input type="hidden" name="image" class="image-tag">
            </div>

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

Webcam.set({
    width: 490,
    height: 350,
    image_format: 'jpeg',
    jpeg_quality: 90
});

Webcam.attach('#my_camera');

function take_snapshot() {
    Webcam.snap(function(data_uri) {

        $(".image-tag").val(data_uri);

        document.getElementById('results').innerHTML =
            '<img src="'+data_uri+'"/>';
    });
}

</script>

</body>
</html>
```

---

#  Step 6 — Run Laravel  

```bash
php artisan serve
```

Open:

```
http://127.0.0.1:8000/webcam
```

---

#  Final Output  

✔ Camera opens  
✔ Snapshot captured  
✔ Preview shown  
✔ Image saved in:

```
public/uploads/unique_name.png
```
<img width="1288" height="635" alt="Screenshot 2025-12-11 163333" src="https://github.com/user-attachments/assets/a1b7f52d-5085-4575-ba4c-eb812308e832" />

<img width="862" height="119" alt="Screenshot 2025-12-11 163359" src="https://github.com/user-attachments/assets/a150833b-a912-47c1-ae72-8c6366d6a6d7" />



✔ Message shown:

```
Image uploaded successfully: uploads/xxxxxx.png
```

---



