"use client";

import React from "react";

type TabKey = "dashboard" | "articles" | "testimonials" | "registrations" | "acr";

type Props = {
  tab: TabKey;
  setTab: (t: TabKey) => void;
  sidebarOpen: boolean;
  setSidebarOpen: (v: boolean) => void;
  onLogout: () => void;
};

const menuItems: { key: TabKey; label: string; icon: string }[] = [
  { key: "dashboard", label: "📊 Dashboard", icon: "📊" },
  { key: "registrations", label: "📋 Pendaftaran", icon: "📋" },
  { key: "acr", label: "🏆 ACR Reward", icon: "🏆" },
  { key: "articles", label: "📄 Artikel", icon: "📄" },
  { key: "testimonials", label: "⭐ Testimoni", icon: "⭐" },
];

export default function Sidebar({ tab, setTab, sidebarOpen, setSidebarOpen, onLogout }: Props) {
  return (
    <aside
      className={`${
        sidebarOpen ? "w-64" : "w-20"
      } bg-white border-r border-slate-200 flex flex-col transition-all duration-300 sticky top-0 h-screen`}
    >
      {/* Brand */}
      <div className="p-5 border-b border-slate-100 flex items-center gap-3">
        <span className="text-2xl shrink-0">⚡</span>
        {sidebarOpen && <span className="text-sm font-black text-slate-900 truncate">ARMEDIA</span>}
      </div>

      {/* Nav */}
      <nav className="flex-1 py-4 px-3 space-y-1">
        {menuItems.map((item) => (
          <button
            key={item.key}
            onClick={() => setTab(item.key)}
            className={`w-full text-left px-4 py-3 rounded-xl text-xs font-bold flex items-center gap-3 transition-all ${
              tab === item.key
                ? "bg-red-50 text-red-600 shadow-sm"
                : "text-slate-600 hover:bg-slate-50 hover:text-slate-900"
            }`}
          >
            <span className="text-base shrink-0">{item.icon}</span>
            {sidebarOpen && <span className="truncate">{item.label}</span>}
          </button>
        ))}
      </nav>

      {/* Footer */}
      <div className="p-4 border-t border-slate-100 space-y-2">
        <button
          onClick={() => setSidebarOpen(!sidebarOpen)}
          className="w-full text-xs text-slate-400 hover:text-slate-700 font-bold py-2 cursor-pointer transition-colors"
        >
          {sidebarOpen ? "◀ Collapse" : "▶"}
        </button>
        <button
          onClick={onLogout}
          className="w-full rounded-lg border border-slate-200 py-2.5 text-xs font-bold text-slate-500 hover:text-red-600 hover:border-red-200 transition-all cursor-pointer"
        >
          🚪 Logout
        </button>
      </div>
    </aside>
  );
}