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

function getXY(e) {
    if (e.touches && e.touches.length > 0) {
        const rect = canvas.getBoundingClientRect();
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
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function openSignatureModal(callback) {
    signatureCallback = callback;
    clearPad();
    modal.style.display = "flex";
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

    // Show image preview inside the div
    box.innerHTML = `<img src="${sigData}" alt="${role} signature" style="width: 100%; height: 100%; object-fit: contain;">`;

}
