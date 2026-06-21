"use client";

import React, { useState, useEffect, useSyncExternalStore } from 'react';

/* ─────────────────────────────────────────────
   FOMO DATA — 500 entri random dari kota Indonesia
   ───────────────────────────────────────────── */
const FIRST_NAMES = [
  "Ahmad","Budi","Citra","Dewi","Eko","Fitri","Gilang","Hendra","Indah","Joko",
  "Kartika","Lia","Maman","Nina","Oki","Putri","Rudi","Sari","Tono","Udin",
  "Vina","Wawan","Yanti","Zainal","Adi","Bagus","Cindy","Dimas","Elsa","Farhan",
  "Gita","Hadi","Intan","Januar","Kiki","Lina","Mira","Nanda","Oka","Prita",
  "Rian","Sinta","Tommy","Umar","Vivi","Widya","Yoga","Zara","Agus","Bayu"
];

const LAST_NAMES = [
  "Santoso","Wijaya","Kusuma","Pratama","Hidayat","Nugroho","Saputra","Maulana",
  "Putra","Setiawan","Gunawan","Hartono","Wahyudi","Susanto","Hermawan","Rahman",
  "Lestari","Handayani","Anggraini","Puspita","Ramadhani","Kurniawan","Sudrajat",
  "Utami","Permata","Halim","Firdaus","Iskandar","Siregar","Nasution","Hutapea"
];

const CITIES = [
  "Jakarta","Surabaya","Bandung","Medan","Semarang","Makassar","Palembang",
  "Tangerang","Depok","Bekasi","Yogyakarta","Malang","Solo","Denpasar","Batam",
  "Pekanbaru","Bogor","Tasikmalaya","Cimahi","Pontianak","Banjarmasin","Balikpapan",
  "Samarinda","Manado","Ambon","Jayapura","Mataram","Kupang","Palu","Kendari",
  "Gorontalo","Ternate","Padang","Jambi","Bengkulu","Bandar Lampung","Pangkal Pinang",
  "Serang","Cilegon","Sukabumi","Cirebon","Tegal","Pekalongan","Salatiga","Magelang",
  "Probolinggo","Pasuruan","Mojokerto","Kediri","Blitar","Madiun","Batu","Banyuwangi"
];

const ACTIONS = [
  "baru saja mendaftar paket",
  "baru saja konsultasi via WhatsApp",
  "baru saja melakukan cek cakupan",
  "baru saja terhubung dengan tim sales",
  "baru saja memasang internet",
  "baru saja upgrade ke paket",
  "baru saja isi form registrasi",
  "baru saja request survey lokasi"
];

function generateFomoData(count: number) {
  const data: { name: string; city: string; action: string }[] = [];
  for (let i = 0; i < count; i++) {
    const first = FIRST_NAMES[Math.floor(Math.random() * FIRST_NAMES.length)];
    const last = LAST_NAMES[Math.floor(Math.random() * LAST_NAMES.length)];
    const city = CITIES[Math.floor(Math.random() * CITIES.length)];
    const action = ACTIONS[Math.floor(Math.random() * ACTIONS.length)];
    data.push({ name: `${first} ${last}`, city, action });
  }
  return data;
}

export default function FomoNotification() {
  const isClient = useSyncExternalStore(
    () => () => {},
    () => true,
    () => false
  );
  const [fomoEntries] = useState(() => generateFomoData(500));
  const [index, setIndex] = useState(0);
  const [visible, setVisible] = useState(false);
  const [minutesAgo, setMinutesAgo] = useState(1);

  useEffect(() => {
    let timer: NodeJS.Timeout;
    
    const startCycle = () => {
      // 1. Initial wait before first popup (5 seconds)
      timer = setTimeout(() => {
        setMinutesAgo(Math.floor(Math.random() * 59) + 1);
        setVisible(true);
        scheduleHide();
      }, 5000);
    };

    const scheduleHide = () => {
      // 2. Keep visible for 7 seconds
      timer = setTimeout(() => {
        setVisible(false);
        scheduleNextShow();
      }, 7000);
    };

    const scheduleNextShow = () => {
      // 3. Wait for exit animation (500ms) then advance index
      timer = setTimeout(() => {
        setIndex((prev) => (prev + 1) % fomoEntries.length);
        setMinutesAgo(Math.floor(Math.random() * 59) + 1);
        
        // 4. Stay hidden for 15-25 seconds before showing next
        const hiddenDuration = 15000 + Math.floor(Math.random() * 10000);
        timer = setTimeout(() => {
          setVisible(true);
          scheduleHide();
        }, hiddenDuration);
      }, 500);
    };

    startCycle();

    return () => {
      clearTimeout(timer);
    };
  }, [fomoEntries]);

  if (!isClient) return null;

  const entry = fomoEntries[index];

  return (
    <div
      className={`fixed z-40 bottom-28 left-4 md:bottom-6 md:left-6 transition-all duration-500 ease-out ${
        visible
          ? "translate-y-0 opacity-100 scale-100"
          : "translate-y-4 opacity-0 scale-95 pointer-events-none"
      }`}
    >
      <div className="flex items-center gap-2 rounded-full border border-slate-200 bg-white/95 backdrop-blur-md px-3 py-2 shadow-lg shadow-slate-900/10 max-w-[85vw] md:max-w-sm">
        <div className="flex-shrink-0 h-5 w-5 md:h-6 md:w-6 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white text-[8px] md:text-[10px] font-bold uppercase shadow-sm">
          {entry.name.charAt(0)}
        </div>
        <div className="flex-1 min-w-0">
          <p className="text-[9px] md:text-xs text-slate-600 truncate leading-none">
            <strong className="text-slate-800">{entry.name}</strong> ({entry.city}) {entry.action.replace("baru saja ", "")}
          </p>
        </div>
        <button
          onClick={() => setVisible(false)}
          className="flex-shrink-0 ml-1 p-2 text-slate-600 hover:text-slate-600 text-[10px] md:text-xs"
          aria-label="Tutup notifikasi"
        >
          ✕
        </button>
      </div>
    </div>
  );
}