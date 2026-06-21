"use client";

import React, { useState, useEffect, useCallback } from "react";
import { supabase } from "@/src/lib/supabase";
import { toast } from "@/src/components/admin/Toast";

type Article = {
  id: number;
  category: string;
  title: string;
  excerpt: string;
  image_url: string;
  created_at: string;
};

export default function ArticlesTab() {
  const [articles, setArticles] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<Article | null>(null);
  const [search, setSearch] = useState("");
  const [category, setCategory] = useState("TEKNOLOGI");
  const [title, setTitle] = useState("");
  const [excerpt, setExcerpt] = useState("");
  const [imageUrl, setImageUrl] = useState("");

  const fetchArticles = useCallback(() => {
    setLoading(true);
    supabase
      .from("articles")
      .select("*")
      .order("id", { ascending: false })
      .then(({ data }) => {
        if (data) setArticles(data as Article[]);
        setLoading(false);
      });
  }, []);

  useEffect(() => {
    fetchArticles();
  }, [fetchArticles]);

  const resetForm = () => {
    setCategory("TEKNOLOGI");
    setTitle("");
    setExcerpt("");
    setImageUrl("");
    setEditing(null);
    setShowForm(false);
  };

  const handleEdit = (a: Article) => {
    setEditing(a);
    setCategory(a.category);
    setTitle(a.title);
    setExcerpt(a.excerpt);
    setImageUrl(a.image_url);
    setShowForm(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (editing) {
      const { error } = await supabase
        .from("articles")
        .update({ category, title, excerpt, image_url: imageUrl })
        .eq("id", editing.id);
      if (error) toast("error", "Gagal update artikel");
      else toast("success", "Artikel berhasil diupdate");
    } else {
      const { error } = await supabase
        .from("articles")
        .insert([{ category, title, excerpt, image_url: imageUrl }]);
      if (error) toast("error", "Gagal menambah artikel");
      else toast("success", "Artikel baru ditambahkan");
    }
    resetForm();
    fetchArticles();
  };

  const handleDelete = async (id: number) => {
    if (!confirm("Yakin hapus artikel ini?")) return;
    const { error } = await supabase.from("articles").delete().eq("id", id);
    if (error) toast("error", "Gagal menghapus artikel");
    else toast("success", "Artikel dihapus");
    fetchArticles();
  };

  const filtered = articles.filter(
    (a) =>
      a.title.toLowerCase().includes(search.toLowerCase()) ||
      a.category.toLowerCase().includes(search.toLowerCase()),
  );

  return (
    <div>
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div className="flex items-center gap-3">
          <h2 className="text-lg font-black text-slate-900">📄 Daftar Artikel</h2>
          <span className="text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
            {articles.length} artikel
          </span>
        </div>
        <div className="flex gap-2">
          <input
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="🔍 Cari artikel..."
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
            {editing ? "✏️ Edit Artikel" : "📝 Tambah Artikel Baru"}
          </h3>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-[10px] font-black uppercase tracking-wider text-slate-600 mb-1">
                Kategori
              </label>
              <select
                value={category}
                onChange={(e) => setCategory(e.target.value)}
                className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none"
              >
                <option>TEKNOLOGI</option>
                <option>KEAMANAN</option>
                <option>INFRASTRUKTUR</option>
                <option>INTERNET OF THINGS</option>
                <option>BISNIS</option>
                <option>GAYA HIDUP</option>
              </select>
            </div>
            <div>
              <label className="block text-[10px] font-black uppercase tracking-wider text-slate-600 mb-1">
                Judul
              </label>
              <input
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                required
                placeholder="Judul artikel..."
                className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label className="block text-[10px] font-black uppercase tracking-wider text-slate-600 mb-1">
              Kutipan (Excerpt)
            </label>
            <textarea
              value={excerpt}
              onChange={(e) => setExcerpt(e.target.value)}
              required
              rows={2}
              placeholder="Ringkasan singkat artikel..."
              className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none"
            />
          </div>

          <div>
            <label className="block text-[10px] font-black uppercase tracking-wider text-slate-600 mb-1">
              URL Gambar
            </label>
            <input
              value={imageUrl}
              onChange={(e) => setImageUrl(e.target.value)}
              required
              placeholder="https://images.unsplash.com/..."
              className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none"
            />
            {imageUrl && (
              <img
                src={imageUrl}
                alt="Preview"
                className="mt-2 h-24 w-full object-cover rounded-lg border border-slate-200"
                onError={(e) => {
                  (e.target as HTMLImageElement).style.display = "none";
                }}
              />
            )}
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
          <p className="text-xs text-slate-400 mt-3">Memuat artikel...</p>
        </div>
      ) : (
        <div className="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
          <table className="w-full text-xs">
            <thead className="bg-slate-50 border-b border-slate-200">
              <tr>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">#</th>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">Gambar</th>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">Judul</th>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">Kategori</th>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">Tanggal</th>
                <th className="text-left px-5 py-4 font-black uppercase tracking-wider text-slate-600">Aksi</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filtered.map((a, i) => (
                <tr key={a.id} className="hover:bg-slate-50 transition-colors">
                  <td className="px-5 py-4 font-bold text-slate-400">{i + 1}</td>
                  <td className="px-5 py-4">
                    <img
                      src={a.image_url}
                      alt=""
                      className="h-10 w-16 object-cover rounded-md border border-slate-200"
                      onError={(e) => {
                        (e.target as HTMLImageElement).style.display = "none";
                      }}
                    />
                  </td>
                  <td className="px-5 py-4 font-bold text-slate-900 max-w-xs">
                    <p className="truncate">{a.title}</p>
                    <p className="text-[10px] text-slate-400 font-normal truncate mt-0.5">
                      {a.excerpt}
                    </p>
                  </td>
                  <td className="px-5 py-4">
                    <span className="rounded-full bg-red-50 px-2.5 py-0.5 text-[10px] font-black text-red-600">
                      {a.category}
                    </span>
                  </td>
                  <td className="px-5 py-4 text-slate-400">
                    {new Date(a.created_at).toLocaleDateString("id-ID")}
                  </td>
                  <td className="px-5 py-4">
                    <div className="flex gap-2">
                      <button
                        onClick={() => handleEdit(a)}
                        className="text-slate-500 hover:text-red-600 font-bold cursor-pointer transition-colors"
                      >
                        ✏️
                      </button>
                      <button
                        onClick={() => handleDelete(a.id)}
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
                      ? "Tidak ada artikel yang cocok."
                      : "Belum ada artikel. Klik + Tambah untuk mulai."}
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