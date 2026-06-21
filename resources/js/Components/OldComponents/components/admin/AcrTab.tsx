"use client";

import React, { useState, useEffect } from "react";
import { supabase } from "@/src/lib/supabase";
import { toast } from "@/src/components/admin/Toast";

type Member = {
  id: number;
  id_pelanggan: string;
  nama: string;
  whatsapp: string;
  total_poin: number;
  level_member: string;
  created_at: string;
};

type Reward = {
  id: number;
  nama_hadiah: string;
  poin_dibutuhkan: number;
  stok: number;
  is_active: boolean;
};

export default function AcrTab() {
  const [activeSubTab, setActiveSubTab] = useState<"members" | "rewards">("members");
  
  const [members, setMembers] = useState<Member[]>([]);
  const [rewards, setRewards] = useState<Reward[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  const fetchAcrData = async () => {
    setIsLoading(true);
    try {
      const [membersRes, rewardsRes] = await Promise.all([
        supabase.from("acr_members").select("*").order("total_poin", { ascending: false }),
        supabase.from("acr_rewards_catalog").select("*").order("id", { ascending: true })
      ]);

      if (membersRes.data) setMembers(membersRes.data);
      if (rewardsRes.data) setRewards(rewardsRes.data);
    } catch (error) {
      console.error(error);
      toast("error", "Gagal mengambil data ACR.");
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchAcrData();
  }, []);

  return (
    <div className="space-y-6">
      {/* Sub Tabs */}
      <div className="flex items-center gap-4 border-b border-slate-200 pb-4">
        <button
          onClick={() => setActiveSubTab("members")}
          className={`px-4 py-2 text-sm font-bold rounded-xl transition-all ${
            activeSubTab === "members" ? "bg-red-50 text-red-600 border border-red-100" : "text-slate-500 hover:bg-slate-100"
          }`}
        >
          👥 Member ACR
        </button>
        <button
          onClick={() => setActiveSubTab("rewards")}
          className={`px-4 py-2 text-sm font-bold rounded-xl transition-all ${
            activeSubTab === "rewards" ? "bg-red-50 text-red-600 border border-red-100" : "text-slate-500 hover:bg-slate-100"
          }`}
        >
          🎁 Katalog Hadiah
        </button>
        <button
          onClick={fetchAcrData}
          className="ml-auto flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-lg hover:bg-slate-50"
        >
          🔄 Refresh
        </button>
      </div>

      {isLoading ? (
        <div className="text-center py-12">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-red-600 border-r-transparent"></div>
          <p className="mt-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Memuat Data ACR...</p>
        </div>
      ) : (
        <>
          {activeSubTab === "members" && (
            <div className="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full text-left text-sm text-slate-600">
                  <thead className="bg-slate-50 text-xs font-black uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                      <th className="px-6 py-4">ID Pelanggan</th>
                      <th className="px-6 py-4">Nama Lengkap</th>
                      <th className="px-6 py-4">WhatsApp</th>
                      <th className="px-6 py-4">Level</th>
                      <th className="px-6 py-4 text-right">Total Poin</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {members.length === 0 ? (
                      <tr>
                        <td colSpan={5} className="px-6 py-8 text-center text-slate-400">
                          Belum ada member ACR terdaftar.
                        </td>
                      </tr>
                    ) : (
                      members.map((m) => (
                        <tr key={m.id} className="hover:bg-slate-50 transition-colors">
                          <td className="px-6 py-4 font-mono text-xs font-bold text-slate-900">{m.id_pelanggan}</td>
                          <td className="px-6 py-4 font-bold text-slate-700">{m.nama}</td>
                          <td className="px-6 py-4">{m.whatsapp}</td>
                          <td className="px-6 py-4">
                            <span className={`inline-flex px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest ${
                              m.level_member === 'Gold' ? 'bg-amber-100 text-amber-700' :
                              m.level_member === 'Platinum' ? 'bg-slate-800 text-slate-200' :
                              'bg-slate-100 text-slate-600'
                            }`}>
                              {m.level_member}
                            </span>
                          </td>
                          <td className="px-6 py-4 text-right font-black text-red-600 text-base">{m.total_poin}</td>
                        </tr>
                      ))
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {activeSubTab === "rewards" && (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {rewards.length === 0 ? (
                <div className="col-span-full py-8 text-center text-slate-400">Belum ada katalog hadiah.</div>
              ) : (
                rewards.map((r) => (
                  <div key={r.id} className="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div className="flex justify-between items-start mb-4">
                      <div className="h-12 w-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-2xl">
                        🎁
                      </div>
                      <span className={`px-2 py-1 text-[10px] font-black uppercase tracking-widest rounded ${r.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}`}>
                        {r.is_active ? 'Aktif' : 'Nonaktif'}
                      </span>
                    </div>
                    <h4 className="font-bold text-slate-900 text-sm mb-1">{r.nama_hadiah}</h4>
                    <p className="text-2xl font-black text-red-600 mb-4">{r.poin_dibutuhkan} <span className="text-xs text-slate-400 font-bold">POIN</span></p>
                    <div className="flex items-center justify-between pt-4 border-t border-slate-100">
                      <span className="text-xs text-slate-500 font-medium">Stok Tersedia:</span>
                      <span className="text-xs font-bold text-slate-900 bg-slate-100 px-2 py-1 rounded">{r.stok} unit</span>
                    </div>
                  </div>
                ))
              )}
            </div>
          )}
        </>
      )}
    </div>
  );
}
