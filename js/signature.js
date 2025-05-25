const modal = document.getElementById('signature-modal');
const canvas = document.getElementById('signature-pad');
const ctx = canvas.getContext('2d');

let signatureCallback = null;
let drawing = false;

// Mouse Events
canvas.addEventListener('mousedown', startPosition);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', endPosition);
canvas.addEventListener('mouseout', endPosition);

// Touch Events
canvas.addEventListener('touchstart', startPosition, { passive: false });
canvas.addEventListener('touchmove', draw, { passive: false });
canvas.addEventListener('touchend', endPosition);
window.addEventListener('resize', resizeCanvas);


function resizeCanvas() {
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width;
    canvas.height = rect.height;
}
function getXY(e) {
    const rect = canvas.getBoundingClientRect();
    let clientX, clientY;

    if (e.touches && e.touches.length > 0) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
    } else if (e.changedTouches && e.changedTouches.length > 0) {
        clientX = e.changedTouches[0].clientX;
        clientY = e.changedTouches[0].clientY;
    } else {
        clientX = e.clientX;
        clientY = e.clientY;
    }

    return {
        x: clientX - rect.left,
        y: clientY - rect.top
    };
}

function startPosition(e) {
    e.preventDefault();
    drawing = true;
    const pos = getXY(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}

function draw(e) {
    if (!drawing) return;
    e.preventDefault();
    const pos = getXY(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}

function endPosition(e) {
    if (drawing) {
        ctx.closePath();
    }
    drawing = false;
}


function clearPad() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function openSignatureModal(callback) {
    signatureCallback = callback;
    clearPad();
    modal.style.display = "flex";
    setTimeout(() => {
        resizeCanvas();
        clearPad();
    }, 10);
}

function closeSignatureModal() {
    modal.style.display = "none";
}

function confirmSignature() {
    const dataURL = canvas.toDataURL('image/png');
    if (typeof signatureCallback === "function") {
        signatureCallback(dataURL);
    }
    closeSignatureModal();
}
function setSignature(role, sigData) {
    const box = document.getElementById(`${role}-signature-box`);
    const input = document.getElementById(`${role}-signature-input`);

    // Set hidden input
    input.value = sigData;
    input.dispatchEvent(new Event('change'));
    // Show image preview inside the div
    box.innerHTML = `<img src="${sigData}" alt="${role} signature" style="width: 100%; height: 100%; object-fit: contain;">`;

}
