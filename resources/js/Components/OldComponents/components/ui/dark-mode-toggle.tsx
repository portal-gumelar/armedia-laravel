"use client";

import React, { useState, useEffect, useSyncExternalStore } from "react";

export default function DarkModeToggle() {
  const [isDark, setIsDark] = useState(false);
  
  const isClient = useSyncExternalStore(
    () => () => {},
    () => true,
    () => false
  );

  useEffect(() => {
    const stored = localStorage.getItem("theme");
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    
    if (stored === "dark" || (!stored && prefersDark)) {
      document.documentElement.classList.add("dark");
      setIsDark(true);
      if (!stored) localStorage.setItem("theme", "dark");
    } else {
      document.documentElement.classList.remove("dark");
      setIsDark(false);
      if (!stored) localStorage.setItem("theme", "light");
    }
  }, []);

  const toggle = () => {
    const next = !isDark;
    setIsDark(next);
    if (next) {
      document.documentElement.classList.add("dark");
      localStorage.setItem("theme", "dark");
    } else {
      document.documentElement.classList.remove("dark");
      localStorage.setItem("theme", "light");
    }
  };

  if (!isClient) return null;

  return (
    <button
      onClick={toggle}
      className="h-9 w-9 rounded-full flex items-center justify-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-300 cursor-pointer text-sm"
      aria-label={isDark ? "Switch ke Light Mode" : "Switch ke Dark Mode"}
    >
      {isDark ? "☀️" : "🌙"}
    </button>
  );
}