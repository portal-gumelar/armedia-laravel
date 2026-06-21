"use client";

import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { Smartphone, Tv, Video, ChevronDown } from 'lucide-react';

export default function BandwidthCalculator({ onSelectPackage }: { onSelectPackage?: (pkgId: string) => void }) {
  const [phones, setPhones] = useState<number>(0);
  const [hasTv, setHasTv] = useState<boolean>(false);
  const [tvSize, setTvSize] = useState<string>("32");
  const [hasLive, setHasLive] = useState<boolean>(false);

  const calculateBandwidth = () => {
    let min = phones * 3;
    let max = phones * 5;

    if (hasTv) {
      if (tvSize === "32") { min += 5; max += 10; }
      else if (tvSize === "43") { min += 10; max += 15; }
      else if (tvSize === "50") { min += 15; max += 25; }
      else { min += 25; max += 50; } // 55+
    }

    if (hasLive) {
      min += 10; max += 20;
    }

    let recommended = "20 Mbps";
    let recommendedId = "PAKET_1";
    if (max === 0) { recommended = "-"; recommendedId = ""; }
    else if (max <= 20) { recommended = "20 Mbps"; recommendedId = "PAKET_1"; }
    else if (max <= 30) { recommended = "30 Mbps"; recommendedId = "PAKET_2"; }
    else if (max <= 50) { recommended = "50 Mbps"; recommendedId = "PAKET_3"; }
    else if (max <= 75) { recommended = "75 Mbps"; recommendedId = "PAKET_4"; }
    else { recommended = "100 Mbps"; recommendedId = "PAKET_5"; }

    return { min, max, recommended, recommendedId };
  };

  const result = calculateBandwidth();

  return (
    <section id="bandwidth-calculator" className="mx-auto max-w-7xl px-4 sm:px-6 py-12 lg:py-24 lg:px-8 border-t border-slate-100 scroll-mt-20">
      <motion.div 
        initial={{ opacity: 0, y: 30 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true, margin: "-100px" }}
        transition={{ duration: 0.6 }}
        className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12"
      >
        <div className="lg:col-span-5 flex flex-col justify-center">
          <span className="text-xs font-bold uppercase tracking-widest text-red-600">EDUKASI BANDWIDTH</span>
          <h2 className="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
            Kalkulator Kebutuhan <span className="text-red-600">Internet</span>
          </h2>
          <p className="mt-6 text-sm leading-relaxed text-slate-600 font-medium text-justify">
            Banyak pelanggan mengira internet lambat selalu karena Mbps kecil. Padahal, jumlah perangkat aktif dan jenis aktivitas (seperti Live Streaming atau Smart TV) sangat mempengaruhi penggunaan bandwidth. Hitung kebutuhan ideal Anda sekarang!
          </p>
          
          <div className="mt-8 space-y-4">
            <div className="bg-red-50 rounded-xl p-5 border border-red-100">
              <h3 className="text-xs font-black uppercase text-red-600 tracking-wider mb-2">Informasi Penggunaan:</h3>
              <ul className="text-xs text-slate-700 space-y-2 font-medium">
                <li>• <span className="font-bold text-slate-900">HP Normal (Scroll/Chat):</span> 3 - 5 Mbps / perangkat</li>
                <li>• <span className="font-bold text-slate-900">HP Live (TikTok/Shopee):</span> 10 - 20 Mbps / perangkat</li>
                <li>• <span className="font-bold text-slate-900">Smart TV Android:</span> 5 - 50 Mbps (tergantung ukuran layar)</li>
              </ul>
            </div>
          </div>
        </div>

        <div className="lg:col-span-7 bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden flex flex-col">
          <div className="p-6 md:p-8 flex-1 space-y-4">
            <h3 className="text-sm font-black uppercase tracking-widest text-slate-900 mb-4 border-b border-slate-100 pb-2">Kalkulator Perangkat</h3>
            
            {/* HP */}
            <div className="flex items-center justify-between p-4 border border-slate-100 rounded-xl bg-slate-50">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 shadow-sm">
                  <Smartphone size={16} />
                </div>
                <div>
                  <div className="text-sm font-bold text-slate-800">Jumlah HP / Gadget</div>
                  <div className="text-[10px] text-slate-600 font-medium">Aktif bersamaan</div>
                </div>
              </div>
              <div className="flex items-center gap-2 bg-white border border-slate-200 rounded-xl p-1 shadow-sm shrink-0">
                <motion.button whileTap={{scale: 0.85}} onClick={() => setPhones(p => Math.max(0, p-1))} className="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-200 font-bold">-</motion.button>
                <motion.span key={phones} initial={{ scale: 0.5, opacity: 0 }} animate={{ scale: 1, opacity: 1 }} className="w-6 text-center font-black text-slate-800 text-sm">{phones}</motion.span>
                <motion.button whileTap={{scale: 0.85}} onClick={() => setPhones(p => p+1)} className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors font-bold">+</motion.button>
              </div>
            </div>

            {/* TV Toggle */}
            <div className="border border-slate-100 rounded-xl bg-slate-50 p-4 transition-all">
              <label className="flex items-center justify-between cursor-pointer">
                <div className="flex items-center gap-3">
                  <div className={`w-8 h-8 rounded-full flex items-center justify-center transition-colors ${hasTv ? 'bg-red-100 text-red-600' : 'bg-slate-200 text-slate-600'}`}>
                    <Tv size={16} />
                  </div>
                  <div>
                    <div className="text-sm font-bold text-slate-800">Smart TV / Android Box</div>
                    <div className="text-[10px] text-slate-600 font-medium">Netflix, YouTube di TV</div>
                  </div>
                </div>
                <div className="relative">
                  <input type="checkbox" className="sr-only" checked={hasTv} onChange={(e) => setHasTv(e.target.checked)} />
                  <div className={`block w-10 h-6 rounded-full transition-colors ${hasTv ? 'bg-red-500' : 'bg-slate-300'}`}></div>
                  <div className={`absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform ${hasTv ? 'transform translate-x-4' : ''}`}></div>
                </div>
              </label>

              {hasTv && (
                <motion.div initial={{ opacity: 0, height: 0 }} animate={{ opacity: 1, height: 'auto' }} className="mt-4 pt-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 overflow-hidden">
                  <div className="text-xs font-bold text-slate-600 whitespace-nowrap">Resolusi / Ukuran:</div>
                  <div className="relative w-full">
                    <select value={tvSize} onChange={(e) => setTvSize(e.target.value)} className="w-full p-2.5 text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 appearance-none shadow-sm pr-8 cursor-pointer">
                      <option value="32">32 Inch (Standar)</option>
                      <option value="43">43 Inch (Medium)</option>
                      <option value="50">50 Inch (Besar)</option>
                      <option value="55">55+ Inch (Sangat Besar)</option>
                    </select>
                    <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-600">
                      <ChevronDown size={14} strokeWidth={3} />
                    </div>
                  </div>
                </motion.div>
              )}
            </div>

            {/* Live Toggle */}
            <div className="border border-slate-100 rounded-xl bg-slate-50 p-4 transition-all">
              <label className="flex items-center justify-between cursor-pointer">
                <div className="flex items-center gap-3">
                  <div className={`w-8 h-8 rounded-full flex items-center justify-center transition-colors ${hasLive ? 'bg-red-100 text-red-600' : 'bg-slate-200 text-slate-600'}`}>
                    <Video size={16} />
                  </div>
                  <div>
                    <div className="text-sm font-bold text-slate-800">Live Jualan / Streaming</div>
                    <div className="text-[10px] text-slate-600 font-medium">TikTok Live, Shopee Live</div>
                  </div>
                </div>
                <div className="relative">
                  <input type="checkbox" className="sr-only" checked={hasLive} onChange={(e) => setHasLive(e.target.checked)} />
                  <div className={`block w-10 h-6 rounded-full transition-colors ${hasLive ? 'bg-red-500' : 'bg-slate-300'}`}></div>
                  <div className={`absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform ${hasLive ? 'transform translate-x-4' : ''}`}></div>
                </div>
              </label>
            </div>
          </div>
          
          <div className="bg-slate-900 p-6 md:p-10 flex flex-col items-center text-center border-t border-slate-800">
            <p className="text-[10px] sm:text-xs font-black uppercase tracking-widest text-slate-600 mb-1">
              Total Kebutuhan: <motion.span key={`${result.min}-${result.max}`} initial={{ y: -10, opacity: 0 }} animate={{ y: 0, opacity: 1 }} className="inline-block text-white mx-1">{result.min} - {result.max}</motion.span> Mbps
            </p>
            <p className="text-xs sm:text-sm font-medium text-slate-600 mb-5">
              Rekomendasi Paket Ideal Untuk Anda:
            </p>
            
            <motion.div 
              key={result.recommended}
              initial={{ scale: 0.8, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              transition={{ type: "spring", stiffness: 300, damping: 15 }}
              className="inline-block bg-gradient-to-r from-red-600 to-orange-600 px-8 py-3 sm:px-12 sm:py-4 rounded-2xl text-white font-black text-3xl sm:text-5xl shadow-xl shadow-red-600/20 mb-6"
            >
              {result.recommended}
            </motion.div>

            {result.recommendedId && onSelectPackage && (
              <motion.button 
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
                onClick={() => onSelectPackage(result.recommendedId)}
                className="font-black bg-white text-slate-900 px-8 py-3.5 sm:py-4 rounded-xl hover:bg-slate-200 transition-all uppercase tracking-widest shadow-lg shadow-white/10 text-xs sm:text-sm w-full max-w-[300px]"
              >
                Pilih Paket Sekarang
              </motion.button>
            )}
          </div>
        </div>
      </motion.div>
    </section>
  );
}
