document.addEventListener('DOMContentLoaded', function () {
    const showMapBtn = document.getElementById('show-map-btn');
    const mapWrapper = document.getElementById('results-map-wrapper');
    const statusEl = mapWrapper ? mapWrapper.querySelector('.map-status') : null;
    let mapInitialized = false;
    let map;
    const submissionLocations = window.resultsMapLocations || [];

    if (!showMapBtn || !mapWrapper) {
        return;
    }

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
            if (statusEl) {
                statusEl.textContent = 'No se pudo cargar la librería de mapas. Revisa tu conexión.';
            }
        };
        document.body.appendChild(script);
    }

    function initMap() {
        if (!submissionLocations.length) {
            if (statusEl) {
                statusEl.textContent = 'No hay ubicaciones válidas para mostrar en el mapa.';
            }
            return;
        }

        if (mapInitialized) {
            map.invalidateSize();
            return;
        }

        map = L.map('results-map', { scrollWheelZoom: false });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        const markers = submissionLocations.map(function (item) {
            const marker = L.marker([item.lat, item.lng]).addTo(map);
            let popupHtml = '<strong>Envío:</strong> ' + item.label;
            if (item.url) {
                popupHtml += '<br><a href="' + item.url + '" target="_blank" rel="noreferrer">Ver en Google Maps</a>';
            }
            marker.bindPopup(popupHtml);
            return marker;
        });

        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.25));
        if (statusEl) {
            statusEl.textContent = 'Mapa cargado con ' + markers.length + ' ubicaciones.';
        }
        mapInitialized = true;
    }

    showMapBtn.addEventListener('click', function () {
        const isCurrentlyHidden = mapWrapper.style.display === 'none' || mapWrapper.style.display === '';
        mapWrapper.style.display = isCurrentlyHidden ? 'block' : 'none';
        showMapBtn.textContent = isCurrentlyHidden ? 'Ocultar mapa' : 'Mostrar mapa de ubicaciones';

        if (!isCurrentlyHidden) {
            return;
        }

        if (statusEl) {
            statusEl.textContent = 'Cargando mapa...';
        }

        if (window.L) {
            initMap();
        } else {
            loadLeafletAssets(initMap);
        }
    });
});
