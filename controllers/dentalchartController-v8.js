let currentTooth = null;
let currentClientId = null;
let currentRemarks = null;
let penColor = 'red';
let isSavingToothRemark = false;

document.querySelectorAll('input[name="penColor"]').forEach(input => {
    input.addEventListener('change', () => penColor = input.value);
});

document.body.addEventListener('click', function (e) {
    // Make sure it’s a .tooth img
    if (e.target && e.target.matches('.tooth img')) {
        const toothElement = e.target.closest('.tooth');
        currentTooth = toothElement.dataset.tooth;
        currentClientId = toothElement.dataset.clientid;
        currentRemarks = toothElement.dataset.remarks;
        document.getElementById("remarkSelect").value = currentRemarks;

        document.getElementById('toothImage').src = e.target.src;
        clearSVGRegions();

        const modal = new bootstrap.Modal(document.getElementById('drawingModal'));
        modal.show();
    }
});


function clearSVGRegions() {
    ["top", "bottom", "left", "right", "center"].forEach(id => {
        document.getElementById(id).setAttribute("fill", "transparent");
    });
}

["top", "bottom", "left", "right", "center"].forEach(id => {
    document.getElementById(id).addEventListener("click", () => {
        document.getElementById(id).setAttribute("fill", penColor);
    });
});

function saveRegion() {
    if (isSavingToothRemark) {
        return;
    }

    const svgWrapper = document.getElementById("svg-wrapper");
    const clientId = document.getElementById("clientid").value;
    const remarks = document.getElementById("remarkSelect").value;
    const toothNumber = currentTooth;
    const tooth = document.querySelector('#dental-chart-region .tooth[data-tooth="' + toothNumber + '"]');
    const saveButton = document.querySelector("#drawingModal .modal-footer .btn-primary");

    if (!toothNumber || !tooth) {
        toastError("Select a tooth before saving.");
        return;
    }

    const toothImage = tooth.querySelector("img");
    const remarkDisplay = tooth.querySelector(".remark-display");
    const previousImage = toothImage.src;
    const previousRemarks = tooth.dataset.remarks || "-";
    const previousDisplayedRemarks = remarkDisplay.textContent;
    isSavingToothRemark = true;
    saveButton.disabled = true;

    const clone = svgWrapper.cloneNode(true);
    clone.style.position = "absolute";
    clone.style.top = "-10000px";
    clone.style.left = "-10000px";
    clone.style.zIndex = "-1";
    document.body.appendChild(clone);

    const clonedImg = clone.querySelector("img");
    if (clonedImg) {
        clonedImg.crossOrigin = "anonymous";
    }
    bootstrap.Modal.getInstance(document.getElementById('drawingModal')).hide();

    setTimeout(() => {
        html2canvas(clone, {
            scale: 2,
            useCORS: true,
            allowTaint: false,
            backgroundColor: null
        }).then(canvas => {
            const imageData = canvas.toDataURL("image/png");
            const formData = new FormData();
            formData.append("tooth", toothNumber);
            formData.append("image", imageData);
            formData.append("clientid", clientId);
            formData.append("remarks", remarks);

            if (clone.parentNode) {
                clone.parentNode.removeChild(clone);
            }

            toothImage.src = imageData;
            tooth.dataset.remarks = remarks;
            remarkDisplay.textContent = remarks;

            return fetch("dentalcharts/save_remarks.php", {
                method: "POST",
                body: formData
            }).then(response => {
                if (!response.ok) {
                    throw new Error("The server returned HTTP " + response.status);
                }
                return response.json();
            }).then(data => {
                if (data.status !== "success") {
                    throw new Error(data.message || "The server rejected the save.");
                }
                toastSuccess("Tooth remark saved.");
            });
        }).catch(error => {
            console.error("Tooth remark could not be saved:", error);
            toothImage.src = previousImage;
            tooth.dataset.remarks = previousRemarks;
            remarkDisplay.textContent = previousDisplayedRemarks;
            toastError("The tooth remark could not be saved. Please try again.");
        }).finally(() => {
            if (clone.parentNode) {
                clone.parentNode.removeChild(clone);
            }
            isSavingToothRemark = false;
            saveButton.disabled = false;
        });
    }, 300);
}



function resetDrawingModal() {
    // Reset pen color to red
    document.querySelector('input[name="penColor"][value="red"]').checked = true;

    // Reset remarks dropdown
    document.getElementById('remarkSelect').value = "-";

    // Reset tooth image to default
    const toothImage = document.getElementById('toothImage');
    toothImage.src = "dentalcharts/tooth_1.png";

    // Clear SVG (if any marks were added via JS drawing)
    const svg = document.getElementById('svgOverlay');
    Array.from(svg.children).forEach(shape => {
        shape.setAttribute('fill', 'transparent');
    });
}



