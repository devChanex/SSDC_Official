const modalSignature = document.getElementById('signature-modal');
const canvasSignature = document.getElementById('signature-pad');
const ctx = canvasSignature.getContext('2d');

let signatureCallback = null;
let drawing = false;

// Mouse Events
canvasSignature.addEventListener('mousedown', startPosition);
canvasSignature.addEventListener('mousemove', draw);
canvasSignature.addEventListener('mouseup', endPosition);
canvasSignature.addEventListener('mouseout', endPosition);

// Touch Events
canvasSignature.addEventListener('touchstart', startPosition, { passive: false });
canvasSignature.addEventListener('touchmove', draw, { passive: false });
canvasSignature.addEventListener('touchend', endPosition);

function getXY(e) {
    if (e.touches && e.touches.length > 0) {
        const rect = canvasSignature.getBoundingClientRect();
        return {
            x: e.touches[0].clientX - rect.left,
            y: e.touches[0].clientY - rect.top
        };
    } else {
        return {
            x: e.offsetX,
            y: e.offsetY
        };
    }
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
}

function endPosition(e) {
    drawing = false;
}


function clearPad() {
    ctx.clearRect(0, 0, canvasSignature.width, canvasSignature.height);
}

function openSignatureModal(callback) {
    signatureCallback = callback;
    clearPad();
    modalSignature.style.display = "flex";
}

function closeSignatureModal() {
    modalSignature.style.display = "none";
}

function confirmSignature() {
    const dataURL = canvasSignature.toDataURL('image/png');
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
