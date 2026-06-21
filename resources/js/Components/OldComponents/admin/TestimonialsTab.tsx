"use client";

import React, { useState, useEffect, useCallback } from "react";
import { supabase } from "@/src/lib/supabase";
import { toast } from "@/src/components/admin/Toast";

type Testimonial = {
  id: number;
  quote: string;
  author_name: string;
  author_role: string;
  avatar_initials: string;
  created_at: string;
};

export default function TestimonialsTab() {
  const [testimonials, setTestimonials] = useState<Testimonial[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<Testimonial | null>(null);
  const [search, setSearch] = useState("");
  const [quote, setQuote] = useState("");
  const [authorName, setAuthorName] = useState("");
  const [authorRole, setAuthorRole] = useState("");
  const [avatarInitials, setAvatarInitials] = useState("");

  const fetchTestimonials = useCallback(() => {
    setLoading(true);
    supabase
      .from("testimonials")
      .select("*")
      .order("id", { ascending: false })
      .then(({ data }) => {
        if (data) setTestimonials(data as Testimonial[]);
        setLoading(false);
      });
  }, []);

  useEffect(() => {
    fetchTestimonials();
  }, [fetchTestimonials]);

  const resetForm = () => {
    setQuote("");
    setAuthorName("");
    setAuthorRole("");
    setAvatarInitials("");
    setEditing(null);
    setShowForm(false);
  };

  const handleEdit = (t: Testimonial) => {
    setEditing(t);
    setQuote(t.quote);
    setAuthorName(t.author_name);
    setAuthorRole(t.author_role);
    setAvatarInitials(t.avatar_initials);
    setShowForm(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (editing) {
      const { error } = await supabase
        .from("testimonials")
        .update({
          quote,
          author_name: authorName,
          author_role: authorRole,
          avatar_initials: avatarInitials,
        })
        .eq("id", editing.id);
      if (error) toast("error", "Gagal update testimoni");
      else toast("success", "Testimoni berhasil diupdate");
    } else {
      const { error } = await supabase.from("testimonials").insert([
        {
          quote,
          author_name: authorName,
          author_role: authorRole,
          avatar_initials: avatarInitials,
        },
      ]);
      if (error) toast("error", "Gagal menambah testimoni");
      else toast("success", "Testimoni baru ditambahkan");
    }
    resetForm();
    fetchTestimonials();
  };

  const handleDelete = async (id: number) => {
    if (!confirm("Yakin hapus testimoni ini?")) return;
    const { error } = await supabase.from("testimonials").delete().eq("id", id);
    if (error) toast("error", "Gagal menghapus testimoni");
    else toast("success", "Testimoni dihapus");
    fetchTestimonials();
  };

  const filtered = testimonials.filter(
    (t) =>
      t.author_name.toLowerCase().includes(search.toLowerCase()) ||
      t.quote.toLowerCase().includes(search.toLowerCase()),
  );

  return (
    <div>
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div className="flex items-center gap-3">
          <h2 className="text-lg font-black text-slate-900">⭐ Daftar Testimoni</h2>
          <span className="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
            {testimonials.length} testimoni
          </span>
        </div>
        <div className="flex gap-2">
          <input
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="🔍 Cari testimoni..."
            className="rounded-lg border border-slate-300 px-3 py-2 text-xs focus:border-red-600 focus:outline-none w-48"
          />
          <button
            onClick={() => {
              resetForm();
              setShowForm(true);
            }}
            className="rounded-lg bg-red-600 px-5 py-2 text-xs font-bold uppercase text-white hover:bg-slate-900 transition-all cursor-pointer whitespace-nowrap"
          >
            + Tambah
          </button>
        </div>
      </div>

      {/* Form */}
      {showForm && (
        <form
          onSubmit={handleSubmit}
          className="bg-white rounded-2xl border border-slate-200 p-6 mb-6 space-y-4 shadow-sm"
        >
          <h3 className="text-sm font-black text-slate-900">
            {editing ? "✏️ Edit Testimoni" : "📝 Tambah Testimoni Baru"}
          </h3>

          <div>
            <label className="block text-[10px] font-black uppercase tracking-wider text-slate-600 mb-1">
              Kutipan (Quote)
            </label>
            <textarea
              value={quote}
              onChange={(e) => setQuote(e.target.value)}
              required
              rows={3}
              placeholder="Tulis testimoni pelanggan..."
              className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none"
            />
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label className="block text-[10px] font-black uppercase tracking-wider text-slate-600 mb-1">
                Nama
              </label>
              <input
                value={authorName}
                onChange={(e) => setAuthorName(e.target.value)}
                required
                placeholder="Nama lengkap"
                className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none"
              />
            </div>
            <div>
              <label className="block text-[10px] font-black uppercase tracking-wider text-slate-600 mb-1">
                Jabatan / Peran
              </label>
              <input
                value={authorRole}
                onChange={(e) => setAuthorRole(e.target.value)}
                required
                placeholder="Contoh: Manager IT, Jakarta"
                className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none"
              />
            </div>
            <div>
              <label className="block text-[10px] font-black uppercase tracking-wider text-slate-600 mb-1">
                Inisial (2 huruf)
              </label>
              <input
                value={avatarInitials}
                onChange={(e) => setAvatarInitials(e.target.value.toUpperCase())}
                required
                maxLength={2}
                placeholder="AB"
                className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none"
              />
            </div>
          </div>

          <div className="flex gap-2">
            <button
              type="submit"
              className="rounded-lg bg-red-600 px-6 py-2.5 text-xs font-bold uppercase text-white hover:bg-slate-900 transition-all cursor-pointer"
            >
              {editing ? "💾 Simpan" : "➕ Tambah"}
            </button>
            <button
              type="button"
              onClick={resetForm}
              className="rounded-lg border border-slate-300 px-6 py-2.5 text-xs font-bold uppercase text-slate-600 hover:bg-slate-100 transition-all cursor-pointer"
            >
              Batal
            </button>
          </div>
        </form>
      )}

      {/* Table */}
      {loading ? (
        <div className="text-center py-12">
          <div className="inline-block h-8 w-8 animate-spin rounded-full border-2 border-slate-300 border-t-red-600" />
          <p className="text-xs text-slate-400 mt-3">Memuat testimoni...</p>
        </div>
      ) : (
        <div className="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
          <table className="w-full text-xs">
            <thead className="bg-slate-50 border-b border-slate-200">
              <tr>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">#</th>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">Quote</th>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">Nama</th>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">Peran</th>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">Tanggal</th>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filtered.map((t, i) => (
                <tr key={t.id} className="hover:bg-slate-50 transition-colors">
                  <td className="px-5 py-4 font-bold text-slate-400">{i + 1}</td>
                  <td className="px-5 py-4 text-slate-600 italic max-w-xs truncate">
                    "{t.quote}"
                  </td>
                  <td className="px-5 py-4">
                    <div className="flex items-center gap-2.5">
                      <div className="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-[10px] font-black text-red-600 shrink-0">
                        {t.avatar_initials}
                      </div>
                      <span className="font-bold text-slate-900 truncate">
                        {t.author_name}
                      </span>
                    </div>
                  </td>
                  <td className="px-5 py-4 text-slate-500 truncate max-w-[140px]">
                    {t.author_role}
                  </td>
                  <td className="px-5 py-4 text-slate-400">
                    {new Date(t.created_at).toLocaleDateString("id-ID")}
                  </td>
                  <td className="px-5 py-4">
                    <div className="flex gap-2">
                      <button
                        onClick={() => handleEdit(t)}
                        className="text-slate-500 hover:text-red-600 font-bold cursor-pointer transition-colors"
                      >
                        ✏️
                      </button>
                      <button
                        onClick={() => handleDelete(t.id)}
                        className="text-slate-400 hover:text-red-500 font-bold cursor-pointer transition-colors"
                      >
                        🗑️
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
              {filtered.length === 0 && (
                <tr>
                  <td colSpan={6} className="px-5 py-12 text-center text-slate-400">
                    {search
                      ? "Tidak ada testimoni yang cocok."
                      : "Belum ada testimoni. Klik + Tambah untuk mulai."}
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}