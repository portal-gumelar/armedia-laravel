"use client";

import React, { useEffect } from "react";
import { MapContainer, TileLayer, Marker, Popup, useMap } from "react-leaflet";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

// Fix default marker icon issue in Leaflet + bundler
const defaultIcon = L.icon({
  iconUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png",
  shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
});

// Red custom marker
const redIcon = L.icon({
  iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png",
  shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41],
});

// Coverage locations (city coordinates in Indonesia)
const coverageLocations = [
  { name: "Jakarta", lat: -6.2088, lng: 106.8456, type: "Kantor Pusat" },
  { name: "Surabaya", lat: -7.2575, lng: 112.7521, type: "Cabang" },
  { name: "Bandung", lat: -6.9175, lng: 107.6191, type: "Cabang" },
  { name: "Semarang", lat: -6.9932, lng: 110.4203, type: "Cabang" },
  { name: "Yogyakarta", lat: -7.7956, lng: 110.3695, type: "Cabang" },
  { name: "Medan", lat: 3.5952, lng: 98.6722, type: "Coverage" },
  { name: "Makassar", lat: -5.1477, lng: 119.4327, type: "Coverage" },
  { name: "Denpasar", lat: -8.6705, lng: 115.2126, type: "Coverage" },
  { name: "Balikpapan", lat: -1.2675, lng: 116.8287, type: "Coverage" },
  { name: "Palembang", lat: -2.9761, lng: 104.7754, type: "Coverage" },
  { name: "Batam", lat: 1.0456, lng: 104.0305, type: "Coverage" },
  { name: "Pekanbaru", lat: 0.5333, lng: 101.4667, type: "Coverage" },
  { name: "Banjarmasin", lat: -3.3186, lng: 114.5944, type: "Coverage" },
  { name: "Manado", lat: 1.4748, lng: 124.8421, type: "Coverage" },
  { name: "Jayapura", lat: -2.5916, lng: 140.669, type: "Coverage" },
];

function FitBounds() {
  const map = useMap();
  useEffect(() => {
    const bounds = L.latLngBounds(
      coverageLocations.map((loc) => [loc.lat, loc.lng])
    );
    map.fitBounds(bounds, { padding: [60, 60], maxZoom: 10 });
  }, [map]);
  return null;
}

export default function LeafletMap() {
  const [map, setMap] = React.useState<L.Map | null>(null);

  React.useEffect(() => {
    return () => {
      if (map) {
        map.remove();
      }
    };
  }, [map]);

  return (
    <MapContainer
      center={[-2.5, 118]}
      ref={setMap}
      zoom={5}
      scrollWheelZoom={true}
      style={{ height: "100%", width: "100%" }}
      zoomControl={true}
    >
      <TileLayer
        attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
      />
      {coverageLocations.map((loc) => (
        <Marker
          key={loc.name}
          position={[loc.lat, loc.lng]}
          icon={loc.type === "Kantor Pusat" || loc.type === "Cabang" ? redIcon : defaultIcon}
        >
          <Popup>
            <div className="text-center min-w-[120px]">
              <p className="text-xs font-black uppercase tracking-wider text-slate-900">
                {loc.name}
              </p>
              <p className="text-[10px] text-slate-600 mt-1">
                {loc.type}
              </p>
            </div>
          </Popup>
        </Marker>
      ))}
      <FitBounds />
    </MapContainer>
  );
}
