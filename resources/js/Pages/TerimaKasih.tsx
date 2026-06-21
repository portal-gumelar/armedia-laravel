import React, { useEffect, useState } from "react";
import { Head, router } from "@inertiajs/react";

export default function TerimaKasih() {
  const [countdown, setCountdown] = useState(10);
  const [paket, setPaket] = useState("PAKET_1");
  const [nama, setNama] = useState("Pelanggan");

  useEffect(() => {
    // Parse URL params
    const searchParams = new URLSearchParams(window.location.search);
    setPaket(searchParams.get("paket") || "PAKET_1");
    setNama(searchParams.get("nama") || "Pelanggan");
  }, []);

  const paketNames: Record<string, string> = {
    PAKET_1: "PAKET_1 — 20 Mbps (Rp 115.000/bln)",
    PAKET_2: "PAKET_2 — 30 Mbps (Rp 148.000/bln)",
    PAKET_3: "PAKET_3 — 50 Mbps (Rp 182.000/bln)",
    PAKET_4: "PAKET_4 — 75 Mbps (Rp 260.000/bln)",
    PAKET_5: "PAKET_5 — 100 Mbps (Rp 330.000/bln)",
  };

  useEffect(() => {
    if (countdown <= 0) {
      router.visit("/");
      return;
    }
    const timer = setTimeout(() => setCountdown((c) => c - 1), 1000);
    return () => clearTimeout(timer);
  }, [countdown]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 via-white to-slate-50 px-4">
      <Head title="Terima Kasih - ARMEDIA" />
      <div className="w-full max-w-lg text-center">
        {/* Checkmark Icon */}
        <div className="mx-auto h-20 w-20 rounded-full bg-emerald-100 flex items-center justify-center mb-8 animate-bounce">
          <svg
            className="h-10 w-10 text-emerald-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2.5}
              d="M5 13l4 4L19 7"
            />
          </svg>
        </div>

        <h1 className="text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
          Terima Kasih, {nama}!
        </h1>
        <p className="mt-4 text-base text-slate-600 leading-relaxed text-center">
          Pendaftaran paket{" "}
          <span className="font-bold text-red-600">
            {paketNames[paket] || paket}
          </span>{" "}
          telah kami terima.
        </p>

        <div className="mt-8 rounded-xl border border-slate-200 bg-white p-6 shadow-sm text-left space-y-3">
          <h3 className="text-sm font-black text-slate-900 uppercase tracking-wider">
            📋 Apa Selanjutnya?
          </h3>
          <ul className="space-y-2 text-xs text-slate-600 leading-relaxed">
            <li className="flex items-start gap-2">
              <span className="text-emerald-500 font-bold mt-0.5">1.</span>
              Tim kami akan menghubungi Anda via WhatsApp dalam 1×24 jam kerja.
            </li>
            <li className="flex items-start gap-2">
              <span className="text-emerald-500 font-bold mt-0.5">2.</span>
              Survey lokasi akan dijadwalkan sesuai waktu yang Anda pilih.
            </li>
            <li className="flex items-start gap-2">
              <span className="text-emerald-500 font-bold mt-0.5">3.</span>
              Pemasangan dilakukan segera setelah survey disetujui.
            </li>
          </ul>
        </div>

        <p className="mt-6 text-xs text-slate-600">
          Anda akan diarahkan kembali ke halaman utama dalam {countdown} detik...
        </p>

        <button
          onClick={() => router.visit("/")}
          className="mt-4 inline-flex items-center gap-2 rounded-md bg-red-600 px-6 py-4 text-xs font-bold uppercase tracking-wider text-white shadow-sm hover:bg-slate-900 transition-all duration-300 cursor-pointer"
        >
          ← Kembali ke Halaman Utama
        </button>
      </div>
    </div>
  );
}
