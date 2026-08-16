const submissionLocations = window.resultsMapLocations ?? [];

function loadLeafletAssets(callback) {
    if (window.L) {
        callback();
        return;
    }

    const css = document.createElement('link');
    css.rel = 'stylesheet';
    css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(css);

    const script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    script.onload = callback;
    script.onerror = function () {
        const statusEl = document.querySelector('#results-map-wrapper .map-status');
        if (statusEl) {
            statusEl.textContent = 'No se pudo cargar la librería de mapas. Revisa tu conexión.';
        }
    };
    document.body.appendChild(script);
}

function initResultsMap() {
    if (!submissionLocations.length) {
        const statusEl = document.querySelector('#results-map-wrapper .map-status');
        if (statusEl) {
            statusEl.textContent = 'No hay ubicaciones válidas para mostrar en el mapa.';
        }
        return;
    }

    const mapEl = document.getElementById('results-map');
    if (!mapEl) {
        return;
    }

    const map = L.map(mapEl, { scrollWheelZoom: false });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
    }).addTo(map);

    const markers = submissionLocations.map((item) => {
        const marker = L.marker([item.lat, item.lng]).addTo(map);
        const ipText = item.ip_address ? String(item.ip_address) : 'IP no disponible';
        let popupHtml = '<strong>Envío:</strong> ' + item.label + '<br><strong>IP:</strong> ' + ipText;
        if (item.url) {
            popupHtml += '<br><a href="' + item.url + '" target="_blank" rel="noreferrer">Ver en Google Maps</a>';
        }
        marker.bindPopup(popupHtml);
        marker.bindTooltip('<strong>IP:</strong> ' + ipText, { direction: 'top', opacity: 0.95 });
        return marker;
    });

    const group = L.featureGroup(markers);
    map.fitBounds(group.getBounds().pad(0.25));

    const statusEl = document.querySelector('#results-map-wrapper .map-status');
    if (statusEl) {
        statusEl.textContent = 'Mapa cargado con ' + markers.length + ' ubicaciones.';
    }
}

function setupResultsMap() {
    const showMapBtn = document.getElementById('show-map-btn');
    const mapWrapper = document.getElementById('results-map-wrapper');
    if (!showMapBtn || !mapWrapper) {
        return;
    }

    const statusEl = mapWrapper.querySelector('.map-status');
    let mapInitialized = false;

    showMapBtn.addEventListener('click', () => {
        const isHidden = mapWrapper.style.display === 'none' || mapWrapper.style.display === '';
        mapWrapper.style.display = isHidden ? 'block' : 'none';
        showMapBtn.textContent = isHidden ? 'Ocultar mapa' : 'Mostrar mapa de ubicaciones';

        if (!isHidden) {
            return;
        }

        if (statusEl) {
            statusEl.textContent = 'Cargando mapa...';
        }

        const renderMap = () => {
            initResultsMap();
            mapInitialized = true;
        };

        if (window.L) {
            renderMap();
        } else {
            loadLeafletAssets(renderMap);
        }
    });
}

document.addEventListener('DOMContentLoaded', setupResultsMap);
