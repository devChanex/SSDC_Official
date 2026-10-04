let currentTooth = null;
let currentToothElement = null;
let penColor = 'red';
let isSavingToothRemark = false;
let isMigratingLegacyDentalChart = false;
const toothRegions = ['top', 'bottom', 'left', 'right', 'center'];

document.querySelectorAll('input[name="penColor"]').forEach(input => {
    input.addEventListener('change', () => penColor = input.value);
});

document.body.addEventListener('click', function (event) {
    if (!event.target || !event.target.matches('.tooth img')) {
        return;
    }

    currentToothElement = event.target.closest('.tooth');
    if (currentToothElement.dataset.migrating === 'true') {
        toastError('This tooth is being converted to the new chart format. Please try again shortly.');
        return;
    }
    currentTooth = currentToothElement.dataset.tooth;
    let regionColors;

    try {
        regionColors = JSON.parse(currentToothElement.dataset.regionColors || '{}');
        if (currentToothElement.dataset.legacy === 'true') {
            regionColors = readLegacyRegionColors(event.target);
        }
    } catch (error) {
        console.error('The saved tooth regions could not be read:', error);
        toastError('The saved tooth markings could not be loaded. Please try again.');
        return;
    }

    document.getElementById('remarkSelect').value = currentToothElement.dataset.remarks || '-';
    document.getElementById('toothImage').src = 'dentalcharts/tooth_1.png';
    setSVGRegions(regionColors);

    const modal = new bootstrap.Modal(document.getElementById('drawingModal'));
    modal.show();
});

function readLegacyRegionColors(image) {
    const canvas = document.createElement('canvas');
    canvas.width = image.naturalWidth;
    canvas.height = image.naturalHeight;
    const context = canvas.getContext('2d');

    if (!context || !canvas.width || !canvas.height) {
        throw new Error('The legacy chart image is not ready.');
    }

    context.drawImage(image, 0, 0);
    const samplePoints = {
        top: [150, 30],
        bottom: [150, 270],
        left: [30, 150],
        right: [270, 150],
        center: [150, 150]
    };
    const regionColors = {};

    Object.keys(samplePoints).forEach(region => {
        const point = samplePoints[region];
        const x = Math.min(canvas.width - 1, Math.floor(point[0] * canvas.width / 300));
        const y = Math.min(canvas.height - 1, Math.floor(point[1] * canvas.height / 300));
        const pixel = context.getImageData(x, y, 1, 1).data;

        if (pixel[0] > 200 && pixel[1] < 80 && pixel[2] < 80) {
            regionColors[region] = 'red';
        } else if (pixel[2] > 200 && pixel[0] < 80 && pixel[1] < 80) {
            regionColors[region] = 'blue';
        } else {
            regionColors[region] = 'transparent';
        }
    });

    return regionColors;
}

function saveToothRegions(tooth, clientId, remarks, regions) {
    const formData = new FormData();
    formData.append('tooth', tooth);
    formData.append('clientid', clientId);
    formData.append('remarks', remarks);
    formData.append('regions', JSON.stringify(regions));

    return fetch('dentalcharts/save_remarks.php', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (!response.ok) {
            throw new Error('The server returned HTTP ' + response.status);
        }
        return response.json();
    }).then(data => {
        if (data.status !== 'success') {
            throw new Error(data.message || 'The server rejected the save.');
        }
    });
}

function updateToothDisplay(toothElement, remarks, regions) {
    toothElement.querySelector('img').src = 'dentalcharts/tooth_1.png';
    toothRegions.forEach(region => {
        toothElement.querySelector('[data-region="' + region + '"]').setAttribute('fill', regions[region]);
    });
    toothElement.dataset.regionColors = JSON.stringify(regions);
    toothElement.dataset.legacy = 'false';
    toothElement.dataset.remarks = remarks;
    toothElement.querySelector('.remark-display').textContent = remarks;
}

async function waitForImage(image) {
    if (typeof image.decode === 'function') {
        await image.decode();
        return;
    }

    if (image.complete) {
        if (image.naturalWidth > 0) {
            return;
        }
        throw new Error('Legacy chart image failed to load.');
    }

    await new Promise((resolve, reject) => {
        image.addEventListener('load', resolve, { once: true });
        image.addEventListener('error', () => reject(new Error('Legacy chart image failed to load.')), { once: true });
    });
}

async function migrateLegacyDentalCharts() {
    if (isMigratingLegacyDentalChart) {
        return;
    }

    const legacyTeeth = Array.from(
        document.querySelectorAll('#dental-chart-region .tooth[data-legacy="true"]')
    );
    if (legacyTeeth.length === 0) {
        return;
    }

    isMigratingLegacyDentalChart = true;
    legacyTeeth.forEach(tooth => tooth.dataset.migrating = 'true');
    let failedMigrations = 0;

    for (const tooth of legacyTeeth) {
        try {
            const image = tooth.querySelector('img');
            await waitForImage(image);
            const regions = readLegacyRegionColors(image);
            const remarks = tooth.dataset.remarks || '-';

            await saveToothRegions(
                tooth.dataset.tooth,
                tooth.dataset.clientid,
                remarks,
                regions
            );
            updateToothDisplay(tooth, remarks, regions);
        } catch (error) {
            failedMigrations++;
            console.error('Legacy chart migration failed for tooth ' + tooth.dataset.tooth + ':', error);
        } finally {
            tooth.dataset.migrating = 'false';
        }
    }

    isMigratingLegacyDentalChart = false;
    if (failedMigrations > 0) {
        toastError(
            failedMigrations + ' legacy tooth chart(s) could not be migrated. They remain available and will be retried when the chart is loaded again.'
        );
    }
}

function setSVGRegions(regionColors) {
    toothRegions.forEach(region => {
        const shape = document.querySelector('#svgOverlay [data-region="' + region + '"]');
        const color = regionColors && ['red', 'blue'].includes(regionColors[region])
            ? regionColors[region]
            : 'transparent';
        shape.setAttribute('fill', color);
    });
}

toothRegions.forEach(region => {
    document.querySelector('#' + region).addEventListener('click', () => {
        document.querySelector('#' + region).setAttribute('fill', penColor);
    });
});

function saveRegion() {
    if (isSavingToothRemark) {
        return;
    }

    const clientId = document.getElementById('clientid').value;
    const remarks = document.getElementById('remarkSelect').value;
    const saveButton = document.querySelector('#drawingModal .modal-footer .btn-primary');

    if (!currentTooth || !currentToothElement) {
        toastError('Select a tooth before saving.');
        return;
    }

    const regions = {};
    toothRegions.forEach(region => {
        regions[region] = document.querySelector('#svgOverlay [data-region="' + region + '"]').getAttribute('fill');
    });

    const toothElement = currentToothElement;
    isSavingToothRemark = true;
    saveButton.disabled = true;

    saveToothRegions(currentTooth, clientId, remarks, regions).then(() => {
        updateToothDisplay(toothElement, remarks, regions);
        bootstrap.Modal.getInstance(document.getElementById('drawingModal')).hide();
        toastSuccess('Tooth remark saved.');
    }).catch(error => {
        console.error('Tooth remark could not be saved:', error);
        toastError('The tooth remark could not be saved. Please try again.');
    }).finally(() => {
        isSavingToothRemark = false;
        saveButton.disabled = false;
    });
}

function resetDrawingModal() {
    document.querySelector('input[name="penColor"][value="red"]').checked = true;
    penColor = 'red';
    document.getElementById('remarkSelect').value = '-';
    document.getElementById('toothImage').src = 'dentalcharts/tooth_1.png';
    clearSVGRegions();
}

function clearSVGRegions() {
    toothRegions.forEach(region => {
        document.querySelector('#svgOverlay [data-region="' + region + '"]').setAttribute('fill', 'transparent');
    });
}
