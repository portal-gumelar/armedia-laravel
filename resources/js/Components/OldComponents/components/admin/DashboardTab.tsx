"use client";

import React, { useState, useEffect } from "react";
import { supabase } from "@/src/lib/supabase";

type TabKey = "dashboard" | "articles" | "testimonials" | "registrations";

type Props = {
  setTab: (t: TabKey) => void;
};

type Stats = {
  totalRegistrations: number;
  newToday: number;
  totalArticles: number;
  totalTestimonials: number;
  statusBreakdown: Record<string, number>;
  packageBreakdown: Record<string, number>;
};

export default function DashboardTab({ setTab }: Props) {
  const [stats, setStats] = useState<Stats | null>(null);

  useEffect(() => {
    Promise.all([
      supabase.from("registrations").select("status, paket, created_at"),
      supabase.from("articles").select("id", { count: "exact", head: true }),
      supabase.from("testimonials").select("id", { count: "exact", head: true }),
    ]).then(([regRes, artRes, tesRes]) => {
      const regs = (regRes.data || []) as { status: string; paket: string; created_at: string }[];
      const today = new Date().toISOString().split("T")[0];

      const statusBreakdown: Record<string, number> = {};
      const packageBreakdown: Record<string, number> = {};
      let newToday = 0;

      regs.forEach((r) => {
        statusBreakdown[r.status] = (statusBreakdown[r.status] || 0) + 1;
        packageBreakdown[r.paket] = (packageBreakdown[r.paket] || 0) + 1;
        if (r.created_at?.startsWith(today)) newToday++;
      });

      setStats({
        totalRegistrations: regs.length,
        newToday,
        totalArticles: artRes.count || 0,
        totalTestimonials: tesRes.count || 0,
        statusBreakdown,
        packageBreakdown,
      });
    });
  }, []);

  const statusColors: Record<string, string> = {
    baru: "bg-blue-100 text-blue-700",
    dihubungi: "bg-yellow-100 text-yellow-700",
    survey: "bg-purple-100 text-purple-700",
    terpasang: "bg-emerald-100 text-emerald-700",
    batal: "bg-red-100 text-red-700",
  };

  return (
    <div className="space-y-8">
      {/* Stat Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          label="Total Pendaftar"
          value={stats?.totalRegistrations ?? "..."}
          icon="👥"
          color="bg-blue-500"
          onClick={() => setTab("registrations")}
        />
        <StatCard
          label="Pendaftar Hari Ini"
          value={stats?.newToday ?? "..."}
          icon="🆕"
          color="bg-emerald-500"
          onClick={() => setTab("registrations")}
        />
        <StatCard
          label="Total Artikel"
          value={stats?.totalArticles ?? "..."}
          icon="📄"
          color="bg-violet-500"
          onClick={() => setTab("articles")}
        />
        <StatCard
          label="Total Testimoni"
          value={stats?.totalTestimonials ?? "..."}
          icon="⭐"
          color="bg-amber-500"
          onClick={() => setTab("testimonials")}
        />
      </div>

      {/* Status Breakdown */}
      <div className="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 className="text-sm font-black text-slate-900 mb-4">📊 Status Pendaftaran</h3>
        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
          {stats &&
            Object.entries(stats.statusBreakdown).map(([status, count]) => (
              <div
                key={status}
                className="text-center p-4 rounded-xl bg-slate-50 border border-slate-100"
              >
                <span
                  className={`inline-block rounded-full px-3 py-1 text-[10px] font-black uppercase mb-2 ${
                    statusColors[status] || "bg-slate-100 text-slate-600"
                  }`}
                >
                  {status}
                </span>
                <p className="text-2xl font-black text-slate-900">{count}</p>
              </div>
            ))}
          {!stats && (
            <p className="text-xs text-slate-400 col-span-full">Memuat statistik...</p>
          )}
        </div>
      </div>

      {/* Package Breakdown */}
      <div className="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 className="text-sm font-black text-slate-900 mb-4">📦 Paket Dipilih</h3>
        <div className="space-y-3">
          {stats &&
            Object.entries(stats.packageBreakdown).map(([pkg, count]) => (
              <div key={pkg} className="flex items-center gap-4">
                <span className="text-xs font-bold text-slate-700 w-24">{pkg}</span>
                <div className="flex-1 bg-slate-100 rounded-full h-3 overflow-hidden">
                  <div
                    className="h-full bg-red-600 rounded-full transition-all duration-500"
                    style={{ width: `${(count / stats.totalRegistrations) * 100}%` }}
                  />
                </div>
                <span className="text-xs font-black text-slate-600 w-10 text-right">
                  {count}
                </span>
              </div>
            ))}
          {!stats && <p className="text-xs text-slate-400">Memuat paket...</p>}
        </div>
      </div>
    </div>
  );
}

function StatCard({
  label,
  value,
  icon,
  color,
  onClick,
}: {
  label: string;
  value: number | string;
  icon: string;
  color: string;
  onClick?: () => void;
}) {
  return (
    <div
      onClick={onClick}
      className={`bg-white rounded-2xl border border-slate-200 p-6 hover:shadow-md transition-all ${
        onClick ? "cursor-pointer" : ""
      }`}
    >
      <div className="flex items-center justify-between mb-4">
        <span className="text-2xl">{icon}</span>
        <div className={`h-10 w-10 rounded-xl ${color} opacity-10`} />
      </div>
      <p className="text-3xl font-black text-slate-900">{value}</p>
      <p className="text-[11px] font-bold text-slate-500 mt-1">{label}</p>
    </div>
  );
}