const modal = document.getElementById('cameraModal');
const video = document.getElementById('video');
const photoPreview = document.getElementById('photoPreview');
const capturedInput = document.getElementById('capturedPhoto');
const canvas = document.getElementById('canvas');
let stream;

let currentFacingMode = "user"; // "user" for front, "environment" for back

function openCameraModal() {
    modal.style.display = 'flex';
    navigator.mediaDevices.getUserMedia({
        video: { facingMode: "user" },
        audio: false
    }).then(s => {
        stream = s;
        video.srcObject = stream;
        video.play();
    }).catch(err => {
        alert('Camera access denied or not supported on this browser.');
        console.error(err);
    });
}


function closeCameraModal() {
    modal.style.display = 'none';
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
}

function capturePhoto() {
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    const video = document.getElementById('video');
    const photoPreview = document.getElementById('photoPreview');
    const capturedInput = document.getElementById('capturedPhoto');

    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = canvas.toDataURL('image/png', 0.7);
    photoPreview.src = imageData;
    capturedInput.value = imageData;

    closeCameraModal();
}

function switchCamera() {
    // Stop current stream
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }

    // Toggle camera mode
    currentFacingMode = currentFacingMode === "user" ? "environment" : "user";

    // Restart with new camera
    startCamera(currentFacingMode);
}

function startCamera(facingMode) {
    navigator.mediaDevices.getUserMedia({
        video: { facingMode: facingMode },
        audio: false
    }).then(s => {
        stream = s;
        video.srcObject = stream;
        video.play();
    }).catch(err => {
        alert('Camera access denied or not supported on this browser.');
        console.error(err);
    });
}