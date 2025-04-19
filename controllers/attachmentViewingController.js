
// $("#bodyResult").load("esoaprint.php?soaid=" + soaid);


loadattachment();
function loadattachment() {
    var soaid = document.getElementById("soaid").value;
    var fd = new FormData();
    fd.append("soaid", soaid)
    $.ajax({
        url: "services/loadAttachmentService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            document.getElementById("bodyResult").innerHTML = result;
        }
    });

}

function deletePhoto() {
    var id = document.getElementById("attachmentid").value;
    var fd = new FormData();
    fd.append("id", id)
    $.ajax({
        url: "services/deleteAttachmentService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            $('#photoModal').modal('hide');
            toastSuccess(result);
            loadattachment();
        }
    });

}


function openPhotoModal(img, id) {
    const modalImg = document.getElementById('modalImage');
    document.getElementById("attachmentid").value = id;
    modalImg.src = img.src;
    $('#photoModal').modal('show');
}


function capturePhoto() {

    var soaid = document.getElementById("soaid").value;
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    const video = document.getElementById('video');
    // const photoPreview = document.getElementById('photoPreview');
    // const capturedInput = document.getElementById('capturedPhoto');

    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = canvas.toDataURL('image/png', 1);
    // photoPreview.src = imageData;
    // capturedInput.value = imageData;


    var fd = new FormData();
    fd.append("soaid", soaid);
    fd.append("attachment", imageData);
    $.ajax({
        url: "services/addAttachmentService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            toastSuccess(result);
            loadattachment();

        }
    });

    closeCameraModal();
}