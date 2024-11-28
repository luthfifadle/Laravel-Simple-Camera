<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <style>
        #camera {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        video, canvas {
            max-width: 100%;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center m-4"><h1>Laravel Simple Camera <x-heroicon-o-camera style="height:40px" /></h1></div>
            <div class="col-md-6">
                <div id="camera">
                    <video id="video" autoplay></video>
                    <button class="btn btn-success mt-3" id="capture" data-bs-toggle="modal" data-bs-target="#previewPhoto">Capture</button>

                    <div class="modal fade" id="previewPhoto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="previewPhotoLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="previewPhotoLabel">Preview</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <canvas id="canvas" style="display: none;"></canvas>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button class="btn btn-primary" id="upload" style="display: none;">Upload</button>
                            </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    @foreach ($photos as $key => $item)
                        <div class="col-sm-4 mb-3">
                            <div class="card text-white">
                                <img src="{{ asset('images/' . $item->image) }}" class="img-thumbnail" alt="Image" style="width: 200px; height: 200px;">
                                <div class="card-img-overlay">
                                    <div class="card-title">
                                        <button class="btn btn-sm btn-danger">
                                            <x-heroicon-s-trash style="color: white; height:20px" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureButton = document.getElementById('capture');
        const uploadButton = document.getElementById('upload');
        const context = canvas.getContext('2d');

        // Access camera
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Error accessing camera:", err);
            });

        // Capture image
        captureButton.addEventListener('click', () => {
            canvas.style.display = 'block';
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            uploadButton.style.display = 'block';
        });

        // Upload image
        uploadButton.addEventListener('click', async () => {
            const imageData = canvas.toDataURL('image/png');
            const url = '{{ route("upload-image") }}';

            try {
                const response = $.ajax({
                    type: "POST",
                    url: url,
                    contentType: "application/json",
                    cache: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // Token CSRF
                    },
                    data: JSON.stringify({ image: imageData }),
                    success: function (result) {
                        Swal.fire({
                            icon: "success",
                            title: result.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            } catch (error) {
                alert(error);
            }
        });
    </script>
</body>
</html>
