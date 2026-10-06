import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const mapElement = document.querySelector('[data-live-map]');
const payload = window.delestAlertMap;

if (mapElement && payload?.zones?.length) {
    const mapShell = mapElement.closest('[data-live-map-shell]') || document;
    const defaultCenter = [7.3697, 12.3547];
    const map = L.map(mapElement, {
        scrollWheelZoom: false,
        zoomControl: true,
    }).setView(defaultCenter, 6);

    L.tileLayer(mapElement.dataset.tileUrl || 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    const markerLayer = L.layerGroup().addTo(map);
    const markerByZone = new Map();
    const colors = {
        outage: '#e11d48',
        planned: '#d97706',
        risk: '#7c3aed',
        stable: '#059669',
    };

    const addMetric = (container, label, value) => {
        const row = document.createElement('div');
        row.className = 'flex items-center justify-between gap-4 border-b border-slate-100 py-1.5 last:border-0';

        const name = document.createElement('span');
        name.className = 'text-slate-500';
        name.textContent = label;

        const count = document.createElement('strong');
        count.className = 'font-semibold text-slate-900';
        count.textContent = String(value);

        row.append(name, count);
        container.append(row);
    };

    const popupFor = (zone) => {
        const popup = document.createElement('article');
        popup.className = 'min-w-56';

        const heading = document.createElement('h3');
        heading.className = 'text-base font-bold text-slate-950';
        heading.textContent = zone.name;

        const location = document.createElement('p');
        location.className = 'mt-0.5 text-xs text-slate-500';
        location.textContent = [zone.district, zone.city, zone.region].filter(Boolean).join(' · ');

        const metrics = document.createElement('div');
        metrics.className = 'mt-3 text-xs';
        addMetric(metrics, payload.labels.activeOutages, zone.activeOutages);
        addMetric(metrics, payload.labels.plannedOutages, zone.plannedOutages);
        addMetric(metrics, payload.labels.openIncidents, zone.openIncidents);
        addMetric(metrics, payload.labels.recentReports, zone.recentReports);

        const prediction = document.createElement('div');
        prediction.className = 'mt-3 rounded-lg bg-slate-100 p-2.5 text-xs text-slate-700';

        if (zone.prediction) {
            prediction.textContent = `${payload.labels.prediction}: ${zone.prediction.probability}% · ${payload.labels.confidence}: ${zone.prediction.confidence}%`;
        } else {
            prediction.textContent = payload.labels.noPrediction;
        }

        const link = document.createElement('a');
        link.className = 'mt-3 inline-flex font-semibold text-sky-700 hover:text-sky-900';
        link.href = zone.outagesUrl;
        link.textContent = `${payload.labels.viewOutages} →`;

        popup.append(heading, location, metrics, prediction, link);

        return popup;
    };

    const showZones = (city = 'all') => {
        markerLayer.clearLayers();
        markerByZone.clear();
        const bounds = [];

        payload.zones
            .filter((zone) => city === 'all' || zone.city === city)
            .forEach((zone) => {
                const coordinates = [zone.latitude, zone.longitude];
                const color = colors[zone.status] || colors.stable;
                const marker = L.circleMarker(coordinates, {
                    radius: zone.activeOutages > 0 ? 12 : 9,
                    color: '#ffffff',
                    weight: 3,
                    fillColor: color,
                    fillOpacity: 0.95,
                })
                    .bindPopup(popupFor(zone), { maxWidth: 320 })
                    .bindTooltip(`${zone.name}, ${zone.city}`, { direction: 'top' })
                    .addTo(markerLayer);

                markerByZone.set(String(zone.id), marker);
                bounds.push(coordinates);
            });

        if (bounds.length === 1) {
            map.setView(bounds[0], 13);
        } else if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [36, 36], maxZoom: 12 });
        }

        mapShell.querySelectorAll('[data-map-zone-card]').forEach((card) => {
            card.classList.toggle('hidden', city !== 'all' && card.dataset.mapCity !== city);
        });
    };

    mapShell.querySelectorAll('[data-map-city-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            mapShell.querySelectorAll('[data-map-city-filter]').forEach((candidate) => {
                const active = candidate === button;
                candidate.classList.toggle('bg-slate-900', active);
                candidate.classList.toggle('text-white', active);
                candidate.classList.toggle('border-slate-300', !active);
                candidate.classList.toggle('bg-white', !active);
                candidate.classList.toggle('text-slate-700', !active);
                candidate.setAttribute('aria-pressed', String(active));
            });

            showZones(button.dataset.mapCityFilter);
        });
    });

    mapShell.querySelectorAll('[data-map-zone]').forEach((button) => {
        button.addEventListener('click', () => {
            const marker = markerByZone.get(button.dataset.mapZone);

            if (marker) {
                map.flyTo(marker.getLatLng(), 14, { duration: 0.8 });
                marker.openPopup();
            }
        });
    });

    mapShell.querySelector('[data-map-locate]')?.addEventListener('click', () => {
        map.locate({ setView: true, maxZoom: 13 });
    });

    map.on('locationfound', (event) => {
        L.circle(event.latlng, {
            radius: event.accuracy,
            color: '#0284c7',
            fillColor: '#38bdf8',
            fillOpacity: 0.15,
        }).addTo(map);
    });

    showZones();
}
