"use client";

import React, { useEffect, useState } from "react";

type ToastType = "success" | "error" | "info";

interface ToastMessage {
  id: number;
  type: ToastType;
  message: string;
}

let addToastFn: ((type: ToastType, message: string) => void) | null = null;

export function toast(type: ToastType, message: string) {
  if (addToastFn) addToastFn(type, message);
}

export default function ToastContainer() {
  const [toasts, setToasts] = useState<ToastMessage[]>([]);

  useEffect(() => {
    addToastFn = (type: ToastType, message: string) => {
      const id = Date.now();
      setToasts((prev) => [...prev, { id, type, message }]);
      setTimeout(() => {
        setToasts((prev) => prev.filter((t) => t.id !== id));
      }, 3500);
    };
    return () => { addToastFn = null; };
  }, []);

  const colors: Record<ToastType, string> = {
    success: "bg-emerald-600",
    error: "bg-red-600",
    info: "bg-slate-800",
  };

  const icons: Record<ToastType, string> = {
    success: "✅",
    error: "❌",
    info: "ℹ️",
  };

  return (
    <div className="fixed bottom-6 right-6 z-[9999] flex flex-col gap-2">
      {toasts.map((t) => (
        <div
          key={t.id}
          className={`${colors[t.type]} text-white px-5 py-3 rounded-xl shadow-lg text-xs font-bold flex items-center gap-2 animate-slide-up min-w-[280px]`}
        >
          <span>{icons[t.type]}</span>
          <span>{t.message}</span>
        </div>
      ))}
    </div>
  );
}