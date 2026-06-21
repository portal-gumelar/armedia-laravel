"use client";

import React, { useState, useEffect, useCallback } from "react";
import { supabase } from "@/src/lib/supabase";
import { toast } from "@/src/components/admin/Toast";

type Registration = {
  id: number;
  paket: string;
  langganan_sebelumnya: string | null;
  nama: string;
  whatsapp: string;
  kecamatan: string;
  desa: string;
  alamat: string;
  tanggal_pemasangan: string;
  waktu_survei: string;
  status: string;
  created_at: string;
};

export default function RegistrationsTab() {
  const [registrations, setRegistrations] = useState<Registration[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [statusFilter, setStatusFilter] = useState("");
  const [dateFilter, setDateFilter] = useState("");

  const fetchRegistrations = useCallback(() => {
    setLoading(true);
    supabase
      .from("registrations")
      .select("*")
      .order("id", { ascending: false })
      .then(({ data }) => {
        if (data) setRegistrations(data as Registration[]);
        setLoading(false);
      });
  }, []);

  useEffect(() => {
    fetchRegistrations();
  }, [fetchRegistrations]);

  const handleStatusUpdate = async (id: number, newStatus: string) => {
    const { error } = await supabase
      .from("registrations")
      .update({ status: newStatus })
      .eq("id", id);
    if (error) toast("error", "Gagal update status");
    else {
      toast("success", `Status diubah ke "${newStatus}"`);
      fetchRegistrations();
    }
  };

  const handleDelete = async (id: number) => {
    if (!confirm("Yakin hapus data pendaftaran ini? Tindakan ini tidak dapat dibatalkan.")) return;
    const { error } = await supabase.from("registrations").delete().eq("id", id);
    if (error) toast("error", "Gagal menghapus data");
    else toast("success", "Data pendaftaran dihapus");
    fetchRegistrations();
  };

  const handleExportCsv = () => {
    const header = [
      "ID",
      "Nama",
      "WhatsApp",
      "Paket",
      "Langganan Sebelumnya",
      "Kecamatan",
      "Desa",
      "Alamat",
      "Tanggal Pemasangan",
      "Waktu Survei",
      "Status",
      "Tanggal Daftar",
    ];
    const rows = filtered.map((r) => [
      r.id,
      r.nama,
      r.whatsapp,
      r.paket,
      r.langganan_sebelumnya || "-",
      r.kecamatan,
      r.desa,
      r.alamat,
      r.tanggal_pemasangan,
      r.waktu_survei,
      r.status,
      new Date(r.created_at).toLocaleDateString("id-ID"),
    ]);
    const csv = [header, ...rows]
      .map((row) => row.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(","))
      .join("\n");
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `pendaftaran-armedia-${new Date().toISOString().split("T")[0]}.csv`;
    a.click();
    URL.revokeObjectURL(url);
    toast("success", "CSV berhasil di-export");
  };

  const handleSyncSheets = async () => {
    if (!confirm("Sinkronisasi akan mengambil semua data dari Google Sheets lama ke database. Lanjutkan?")) return;
    setLoading(true);
    try {
      const res = await fetch("https://script.google.com/macros/s/AKfycbysJJibkHgTnACVYXaYCwG1R4JnnQHuxe8tmvEuHWqLjJ0s0bN1DtQuc5_9uv9gOw6EEw/exec");
      const data = await res.json();
      
      let syncedCount = 0;
      for (const row of data) {
        const mapped = {
          paket: String(row["Paket"] || ""),
          langganan_sebelumnya: String(row["Provider Saat Ini"] || "Belum Pernah Pasang"),
          nama: String(row["Nama Lengkap"] || "Tanpa Nama"),
          whatsapp: String(row["No HP / WA"] || ""),
          kecamatan: String(row["Kecamatan"] || "GUMELAR"),
          desa: String(row["Desa"] || ""),
          alamat: String(row["Alamat Pemasangan"] || ""),
          tanggal_pemasangan: String(row["Tanggal Rencana Pasang"] || "Secepatnya"),
          waktu_survei: String(row["Waktu Survei"] || "Pagi (08:00 - 11:00)"),
          status: row["Status"] === "AKTIF" ? "terpasang" : 
                  row["Status"] === "BATAL" ? "batal" : 
                  "baru",
          created_at: row["Timestamp"] && !isNaN(new Date(row["Timestamp"]).getTime()) 
                        ? new Date(row["Timestamp"]).toISOString() 
                        : new Date().toISOString()
        };

        const { data: existing } = await supabase
          .from("registrations")
          .select("id")
          .eq("whatsapp", mapped.whatsapp)
          .eq("nama", mapped.nama)
          .maybeSingle();

        if (existing) {
          await supabase.from("registrations").update(mapped).eq("id", existing.id);
        } else {
          await supabase.from("registrations").insert([mapped]);
        }
        syncedCount++;
      }
      
      toast("success", `Berhasil sinkronisasi ${syncedCount} data dari Google Sheets!`);
      fetchRegistrations(); // Reload data
    } catch (err: any) {
      console.error(err);
      toast("error", "Gagal sinkronisasi data dari Google Sheets");
      setLoading(false);
    }
  };

  const filtered = registrations.filter((r) => {
    const matchSearch =
      r.nama.toLowerCase().includes(search.toLowerCase()) ||
      r.whatsapp.includes(search) ||
      r.kecamatan.toLowerCase().includes(search.toLowerCase()) ||
      r.desa.toLowerCase().includes(search.toLowerCase());
    const matchStatus = statusFilter ? r.status === statusFilter : true;
    const matchDate = dateFilter ? r.created_at?.startsWith(dateFilter) : true;
    return matchSearch && matchStatus && matchDate;
  });

  const statusColors: Record<string, string> = {
    baru: "bg-blue-100 text-blue-700",
    dihubungi: "bg-yellow-100 text-yellow-700",
    survey: "bg-purple-100 text-purple-700",
    terpasang: "bg-emerald-100 text-emerald-700",
    batal: "bg-red-100 text-red-700",
  };

  const allStatuses = ["", "baru", "dihubungi", "survey", "terpasang", "batal"];

  return (
    <div>
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div className="flex items-center gap-3">
          <h2 className="text-lg font-black text-slate-900">📋 Data Pendaftaran</h2>
          <span className="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
            {registrations.length} pendaftar
          </span>
        </div>
        <div className="flex items-center gap-3">
          <button
            onClick={handleSyncSheets}
            className="rounded-lg border border-slate-300 bg-white px-5 py-2 text-xs font-bold uppercase text-slate-700 hover:bg-slate-50 transition-all cursor-pointer whitespace-nowrap"
          >
            🔄 Sync Google Sheets
          </button>
          <button
            onClick={handleExportCsv}
            className="rounded-lg bg-emerald-600 px-5 py-2 text-xs font-bold uppercase text-white hover:bg-emerald-700 transition-all cursor-pointer whitespace-nowrap"
          >
            📥 Export CSV
          </button>
        </div>
      </div>

      {/* Filters */}
      <div className="flex flex-col sm:flex-row gap-3 mb-6">
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="🔍 Cari nama / WA / desa..."
          className="rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-red-600 focus:outline-none flex-1"
        />
        <select
          value={statusFilter}
          onChange={(e) => setStatusFilter(e.target.value)}
          className="rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-red-600 focus:outline-none"
        >
          <option value="">Semua Status</option>
          <option value="baru">Baru</option>
          <option value="dihubungi">Dihubungi</option>
          <option value="survey">Survey</option>
          <option value="terpasang">Terpasang</option>
          <option value="batal">Batal</option>
        </select>
        <input
          type="date"
          value={dateFilter}
          onChange={(e) => setDateFilter(e.target.value)}
          className="rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-red-600 focus:outline-none"
        />
      </div>

      {/* Table */}
      {loading ? (
        <div className="text-center py-12">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-2 border-slate-300 border-t-red-600" />
          <p className="text-xs text-slate-400 mt-3">Memuat data pendaftaran...</p>
        </div>
      ) : (
        <div className="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
          <table className="w-full text-xs">
            <thead className="bg-slate-50 border-b border-slate-200">
              <tr>
                <th className="text-left px-4 py-4 font-black uppercase tracking-wider text-slate-600">
                  #
                </th>
                <th className="text-left px-4 py-4 font-black uppercase tracking-wider text-slate-600">
                  Nama / WA
                </th>
                <th className="text-left px-4 py-4 font-black uppercase tracking-wider text-slate-600">
                  Paket
                </th>
                <th className="text-left px-4 py-4 font-black uppercase tracking-wider text-slate-600">
                  Lokasi
                </th>
                <th className="text-left px-4 py-4 font-black uppercase tracking-wider text-slate-600">
                  Pemasangan
                </th>
                <th className="text-left px-4 py-4 font-black uppercase tracking-wider text-slate-600">
                  Status
                </th>
                <th className="text-left px-4 py-4 font-black uppercase tracking-wider text-slate-600">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filtered.map((r, i) => (
                <tr key={r.id} className="hover:bg-slate-50 transition-colors">
                  <td className="px-4 py-4 font-bold text-slate-400">{i + 1}</td>
                  <td className="px-4 py-4">
                    <p className="font-bold text-slate-900 truncate max-w-[160px]">
                      {r.nama}
                    </p>
                    <p className="text-[10px] text-slate-400 mt-0.5">
                      📱 {r.whatsapp}
                    </p>
                  </td>
                  <td className="px-4 py-4">
                    <p className="font-bold text-slate-700">{r.paket}</p>
                    {r.langganan_sebelumnya && (
                      <p className="text-[10px] text-slate-400 mt-0.5">
                        Prev: {r.langganan_sebelumnya}
                      </p>
                    )}
                  </td>
                  <td className="px-4 py-4">
                    <p className="text-slate-600 truncate max-w-[120px]">
                      {r.kecamatan}, {r.desa}
                    </p>
                    <p className="text-[10px] text-slate-400 truncate max-w-[120px] mt-0.5">
                      {r.alamat}
                    </p>
                  </td>
                  <td className="px-4 py-4">
                    <p className="text-slate-600">{r.tanggal_pemasangan}</p>
                    <p className="text-[10px] text-slate-400 mt-0.5">
                      🕐 {r.waktu_survei}
                    </p>
                  </td>
                  <td className="px-4 py-4">
                    <select
                      value={r.status}
                      onChange={(e) => handleStatusUpdate(r.id, e.target.value)}
                      className={`rounded-full px-3 py-1 text-[10px] font-black uppercase cursor-pointer border-0 outline-none ${
                        statusColors[r.status] || "bg-slate-100 text-slate-600"
                      }`}
                    >
                      {allStatuses.filter(Boolean).map((s) => (
                        <option key={s} value={s}>
                          {s}
                        </option>
                      ))}
                    </select>
                  </td>
                  <td className="px-4 py-4">
                    <div className="flex gap-2">
                      <a
                        href={`https://wa.me/${r.whatsapp.replace(/\D/g, "")}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-emerald-500 hover:text-emerald-700 font-bold cursor-pointer transition-colors text-base"
                        title="Chat WhatsApp"
                      >
                        💬
                      </a>
                      <button
                        onClick={() => handleDelete(r.id)}
                        className="text-slate-400 hover:text-red-500 font-bold cursor-pointer transition-colors"
                        title="Hapus"
                      >
                        🗑️
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
              {filtered.length === 0 && (
                <tr>
                  <td colSpan={7} className="px-4 py-12 text-center text-slate-400">
                    {registrations.length === 0
                      ? "Belum ada pendaftaran."
                      : "Tidak ada data yang cocok dengan filter."}
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}

      {/* Summary */}
      {!loading && filtered.length > 0 && (
        <p className="text-[10px] text-slate-400 mt-4 text-right">
          Menampilkan {filtered.length} dari {registrations.length} pendaftar
        </p>
      )}
    </div>
  );
}