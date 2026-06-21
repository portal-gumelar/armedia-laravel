"use client";

import React, { useState, useSyncExternalStore } from "react";
import { motion, AnimatePresence } from "framer-motion";

const WA_NUMBER = "6289646415444"; // Ganti dengan nomor WhatsApp ARMDEIA asli
const WA_MESSAGE = encodeURIComponent(
  "Halo ARMEDIA! Saya tertarik dengan layanan internet Fiber Optic. Bisa dibantu info lebih lanjut?"
);

export default function WhatsAppButton() {
  const isClient = useSyncExternalStore(
    () => () => {},
    () => true,
    () => false
  );
  const [isHovered, setIsHovered] = useState(false);

  if (!isClient) return null;

  return (
    <div className="fixed z-50 bottom-6 right-6 flex items-center gap-3">
      {/* Label Tooltip */}
      <AnimatePresence>
        {isHovered && (
          <motion.span
            initial={{ opacity: 0, x: 20, scale: 0.8 }}
            animate={{ opacity: 1, x: 0, scale: 1 }}
            exit={{ opacity: 0, x: 20, scale: 0.8 }}
            transition={{ duration: 0.2, ease: "easeOut" }}
            className="hidden sm:inline-block rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-slate-800 shadow-xl border border-slate-100"
          >
            💬 Chat WhatsApp
          </motion.span>
        )}
      </AnimatePresence>

      <a
        href={`https://wa.me/${WA_NUMBER}?text=${WA_MESSAGE}`}
        target="_blank"
        rel="noopener noreferrer"
        onMouseEnter={() => setIsHovered(true)}
        onMouseLeave={() => setIsHovered(false)}
        className="relative flex items-center justify-center group"
        aria-label="Chat WhatsApp"
      >
        {/* Pinging background for attention */}
        <span className="absolute inline-flex h-full w-full rounded-full bg-[#25D366] opacity-40 animate-ping" style={{ animationDuration: '2s' }}></span>
        
        {/* Main Button */}
        <motion.div
          whileHover={{ scale: 1.15, rotate: [0, -10, 10, -10, 10, 0] }}
          whileTap={{ scale: 0.95 }}
          animate={{ y: [0, -8, 0] }}
          transition={{ 
            y: { duration: 2, repeat: Infinity, ease: "easeInOut" },
            rotate: { duration: 0.5 }
          }}
          className="relative h-14 w-14 rounded-full bg-[#25D366] shadow-lg shadow-[#25D366]/40 flex items-center justify-center cursor-pointer overflow-hidden z-10 border border-white/20"
        >
          {/* Shine effect on hover */}
          <motion.div 
            className="absolute inset-0 bg-white/30 -skew-x-12"
            initial={{ x: "-150%" }}
            whileHover={{ x: "150%" }}
            transition={{ duration: 0.7, ease: "easeInOut" }}
          />

          <svg
            className="h-7 w-7 text-white drop-shadow-md z-10"
            fill="currentColor"
            viewBox="0 0 24 24"
          >
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
          </svg>
        </motion.div>
      </a>
    </div>
  );
}