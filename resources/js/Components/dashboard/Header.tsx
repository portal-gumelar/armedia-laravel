// AUDIT FIX: Header.tsx - NotificationBell dengan polling real API
// - Polling GET /api/notifications/unread-count tiap 30 detik
// - Badge merah jika count > 0
// - Dropdown 10 notif terbaru
// - Klik item → mark as read

import React, { useState, useEffect, useRef } from "react";
import * as Lucide from "lucide-react";
import { router } from "@inertiajs/react";

interface HeaderProps {
  activeTab?:       string;
  isDarkMode?:       boolean;
  setIsDarkMode?:    (val: boolean) => void;
  isSidebarOpen:    boolean;
  setIsSidebarOpen: (val: boolean) => void;
  searchTerm?:       string;
  setSearchTerm?:    (val: string) => void;
  userRole?:        string;
  showNotif?:        boolean;
  setShowNotif?:     (val: boolean) => void;
  unreadNotifCount?: number;
  handleLogout?:     () => void;
  notifications?:    any[];
}

interface NotificationItem {
  id:              number;
  type:            string;
  title:           string;
  message:         string;
  subscriber_id:   number | null;
  is_read:         boolean;
  subscriber_name: string | null;
  created_at:      string;
}

// NotificationBell is now fully driven by props

export const Header: React.FC<HeaderProps> = ({
  isSidebarOpen, setIsSidebarOpen, searchTerm = "", setSearchTerm = () => {}, activeTab = "", userRole = "admin",
  notifications = [], unreadNotifCount = 0, showNotif = false, setShowNotif = () => {}
}) => {

  const dropdownRef = useRef<HTMLDivElement>(null);
  
  useEffect(() => {
    const handler = (e: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target as Node)) {
        setShowNotif(false);
      }
    };
    document.addEventListener("mousedown", handler);
    return () => document.removeEventListener("mousedown", handler);
  }, [setShowNotif]);

  const handleMarkAllRead = () => {
    router.post('/admin/notifications/mark-all-read', {}, { preserveScroll: true });
  };
  
  const handleMarkRead = (n: any) => {
    router.post(`/admin/notifications/${n.id}/read`, {}, { preserveScroll: true });
  };
  
  const formatTime = (dateStr: string) => {
    const d = new Date(dateStr);
    const now = new Date();
    const diffMs = now.getTime() - d.getTime();
    const diffMin = Math.floor(diffMs / 60000);
    if (diffMin < 1)  return "Baru saja";
    if (diffMin < 60) return `${diffMin} menit lalu`;
    const diffHr = Math.floor(diffMin / 60);
    if (diffHr < 24)  return `${diffHr} jam lalu`;
    return d.toLocaleDateString("id-ID", { day: "numeric", month: "short" });
  };

  return (
    <header className="h-[75px] px-4 sm:px-6 flex items-center justify-between sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-100 w-full shrink-0">
      <div className="flex items-center gap-3">
        <button
          onClick={() => setIsSidebarOpen(!isSidebarOpen)}
          className="hidden md:flex w-10 h-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-[#F47920] border border-slate-100 transition-colors"
        >
          <Lucide.Menu size={18} />
        </button>

        <div className="hidden sm:flex items-center bg-slate-50 rounded-xl px-3.5 py-2 w-60 border border-slate-100 focus-within:border-[#F47920] focus-within:ring-4 focus-within:ring-orange-500/10 transition-all">
          <Lucide.Search size={14} className="text-slate-400 mr-2 shrink-0" />
          <input
            type="text"
            placeholder="Cari data pelanggan..."
            className="bg-transparent border-none outline-none text-xs w-full text-slate-700 font-bold placeholder:text-slate-400"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>

        <span className="sm:hidden font-black text-[#0d1655] text-sm uppercase tracking-wider bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">
          ⚙️ {activeTab.slice(0, 12)}
        </span>
      </div>

      <div className="flex items-center gap-3 text-sm">
        <div className="flex items-center gap-2">
          {/* Role Badge */}
          <div className={`hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl border ${
            userRole === "superadmin"
              ? "bg-amber-50 border-amber-200 text-amber-700"
              : "bg-blue-50 border-blue-200 text-blue-700"
          }`}>
            {userRole === "superadmin" ? <Lucide.ShieldAlert size={14} /> : <Lucide.ShieldCheck size={14} />}
            <span className="text-[10px] font-black uppercase tracking-widest">{userRole}</span>
          </div>

          <div className="relative" ref={dropdownRef}>
            <button
              onClick={() => setShowNotif(!showNotif)}
              className="relative w-9 h-9 rounded-xl bg-slate-50 text-slate-400 border border-slate-100 flex items-center justify-center hover:border-orange-300 hover:text-[#F47920] transition-all shadow-sm"
            >
              <Lucide.Bell size={16} />
              {unreadNotifCount > 0 && (
                <span className="absolute -top-1 -right-1 min-w-[16px] h-4 px-0.5 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center">
                  {unreadNotifCount > 99 ? "99+" : unreadNotifCount}
                </span>
              )}
            </button>
            {showNotif && (
              <div className="absolute right-0 top-11 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden">
                <div className="px-4 py-3 border-b flex justify-between items-center">
                  <span className="text-xs font-bold">Notifikasi</span>
                  <button onClick={handleMarkAllRead} className="text-[10px] text-blue-600 hover:underline">Tandai semua</button>
                </div>
                <div className="max-h-72 overflow-y-auto">
                  {notifications.length === 0 ? (
                    <div className="p-4 text-center text-xs text-slate-400">Tidak ada notifikasi</div>
                  ) : (
                    notifications.map((n: any) => (
                      <div key={n.id} onClick={() => handleMarkRead(n)} className="p-3 border-b hover:bg-slate-50 cursor-pointer">
                        <p className="text-xs font-bold">{n.title}</p>
                        <p className="text-[10px] text-slate-500 truncate">{n.message}</p>
                        <p className="text-[9px] text-slate-400 mt-1">{formatTime(n.created_at)}</p>
                      </div>
                    ))
                  )}
                </div>
              </div>
            )}
          </div>

          <div className="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center shadow-sm p-1">
            <img
              src="https://ik.imagekit.io/Gumelar/LogO/logo%20pt.png?updatedAt=1778213993513"
              alt="Logo PT"
              className="w-full h-full object-contain"
            />
          </div>
        </div>
      </div>
    </header>
  );
};