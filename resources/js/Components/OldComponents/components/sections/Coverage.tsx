"use client";

import React, { useSyncExternalStore } from "react";
import dynamic from "next/dynamic";

// Dynamically import the client-side-only map component
const LeafletMap = dynamic(
  () => import("./LeafletMap"),
  {
    ssr: false,
    loading: () => (
      <div className="h-[400px] w-full rounded-2xl bg-slate-100 animate-pulse flex items-center justify-center">
        <span className="text-sm text-slate-600">Memuat peta...</span>
      </div>
    ),
  }
);


// Placeholder saat SSR
function MapPlaceholder() {
  return (
    <div className="h-[400px] w-full rounded-2xl bg-slate-100 animate-pulse flex items-center justify-center">
      <span className="text-sm text-slate-600">Memuat peta...</span>
    </div>
  );
}

export default function CoverageSection() {
  const isClient = useSyncExternalStore(
    () => () => {},
    () => true,
    () => false
  );

  return (
    <section className="relative py-24 overflow-hidden border-t border-b border-slate-100 bg-slate-50">
      <div className="mx-auto max-w-7xl px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          {/* Left - Interactive Map (Leaflet + OSM) */}
          <div className="relative order-2 lg:order-1">
            <div className="relative h-[400px] w-full rounded-2xl overflow-hidden shadow-xl border border-slate-200 z-0">
              {isClient ? <LeafletMap /> : <MapPlaceholder />}
            </div>

            {/* Floating Badges */}
            <div className="absolute top-4 left-4 bg-white/90 backdrop-blur-sm rounded-xl px-4 py-3 shadow-lg border border-slate-100 z-[999]">
              <span className="text-[10px] font-black uppercase tracking-widest text-slate-600">
                Area Terkover
              </span>
              <p className="text-xl font-black text-red-600">50+</p>
              <p className="text-[9px] text-slate-600">Kota & Kabupaten</p>
            </div>

            <div className="absolute bottom-4 right-4 bg-white/90 backdrop-blur-sm rounded-xl px-4 py-3 shadow-lg border border-slate-100 z-[999]">
              <span className="text-[10px] font-black uppercase tracking-widest text-slate-600">
                Pelanggan Aktif
              </span>
              <p className="text-xl font-black text-red-600">10K+</p>
              <p className="text-[9px] text-slate-600">Rumah & Bisnis</p>
            </div>

            {/* Decorative Element */}
            <div className="absolute -bottom-4 -left-4 h-24 w-24 rounded-2xl bg-red-600/10 -z-10 hidden lg:block"></div>
          </div>

          {/* Right - Content */}
          <div className="order-1 lg:order-2 text-left">
            <span className="inline-block text-xs font-bold uppercase tracking-widest text-red-600 bg-red-50 px-3 py-1 rounded">
              WE ARE EVERYWHERE
            </span>

            <h2 className="mt-4 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">
              Check Your{" "}
              <span className="text-red-600">Network Coverage</span>
            </h2>

            <p className="mt-5 max-w-full text-base leading-7 text-slate-600 text-justify">
              Kami terus memperluas jangkauan infrastruktur fiber optic ke berbagai wilayah di Indonesia. Cek ketersediaan layanan di lokasi Anda sekarang dan rasakan koneksi internet super cepat tanpa hambatan.
            </p>

            {/* Stat Cards */}
            <div className="mt-8 grid grid-cols-2 gap-4">
              <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <span className="text-2xl font-black text-red-600">99.9%</span>
                <p className="text-[11px] font-bold text-slate-600 mt-1">Uptime Jaminan</p>
              </div>
              <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <span className="text-2xl font-black text-red-600">24/7</span>
                <p className="text-[11px] font-bold text-slate-600 mt-1">Support Teknis</p>
              </div>
              <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <span className="text-2xl font-black text-red-600">{"<10ms"}</span>
                <p className="text-[11px] font-bold text-slate-600 mt-1">Latency Lokal</p>
              </div>
              <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <span className="text-2xl font-black text-red-600">Fiber</span>
                <p className="text-[11px] font-bold text-slate-600 mt-1">Teknologi Utama</p>
              </div>
            </div>

            {/* CTA */}
            <div className="mt-8">
              <a
                href="#"
                className="inline-flex items-center gap-2 rounded-md bg-red-600 px-8 py-4 text-sm font-bold text-white shadow-lg shadow-red-600/20 transition-all duration-300 hover:bg-slate-900"
              >
                <svg
                  className="h-4 w-4"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                  />
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
                Check Coverage Area
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}