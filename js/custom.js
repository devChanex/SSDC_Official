function showToast(toastType, message) {

    let toastElement = $("#" + toastType);
    toastElement.find(".toast-body").html(message);
    toastElement.toast({ delay: 3000 }).toast("show");
}