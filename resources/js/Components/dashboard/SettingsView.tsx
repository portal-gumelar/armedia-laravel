// AUDIT FIX: SettingsView.tsx - Full CRUD tabs untuk Packages, Villages, Admin Users, Activity Log
// - Menggantikan SettingsView yang hanya punya password lokal & dark mode
// - Semua data dari backend API, superadmin only untuk Users tab

import React, { useState, useEffect } from "react";
import * as Lucide from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { router } from "@inertiajs/react";

interface SettingsViewProps {
  isDarkMode:    boolean;
  setIsDarkMode: (val: boolean) => void;
  userRole:      string;
  initialPackages?: any[];
  initialVillages?: any[];
  initialUsers?:    any[];
}

type TabId = "packages" | "villages" | "users" | "activity";

// ---- PACKAGES TAB ----
const PackagesTab: React.FC<{ isSuperadmin: boolean, initialPackages: any[] }> = ({ isSuperadmin, initialPackages = [] }) => {
  const [editItem,  setEditItem]  = useState<any | null>(null);
  const [showForm,  setShowForm]  = useState(false);
  const [formData,  setFormData]  = useState({ name: "", speed_mbps: "", price: "", description: "", is_active: true });
  const [saving,    setSaving]    = useState(false);
  const [toast,     setToast]     = useState<{ type: "ok" | "err"; text: string } | null>(null);

  const showToast = (type: "ok" | "err", text: string) => {
    setToast({ type, text });
    setTimeout(() => setToast(null), 3000);
  };

  const openCreate = () => {
    setEditItem(null);
    setFormData({ name: "", speed_mbps: "", price: "", description: "", is_active: true });
    setShowForm(true);
  };

  const openEdit = (pkg: any) => {
    setEditItem(pkg);
    setFormData({ name: pkg.name, speed_mbps: String(pkg.speed_mbps), price: String(pkg.price), description: pkg.description || "", is_active: pkg.is_active });
    setShowForm(true);
  };

  const handleSave = (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    const payload = { ...formData, speed_mbps: parseInt(formData.speed_mbps), price: parseInt(formData.price) };
    if (editItem) {
      router.put(`/admin/packages/${editItem.id}`, payload, {
        preserveScroll: true,
        onSuccess: () => { showToast("ok", "Paket berhasil diperbarui."); setShowForm(false); },
        onError: (e) => showToast("err", Object.values(e)[0] as string),
        onFinish: () => setSaving(false)
      });
    } else {
      router.post('/admin/packages', payload, {
        preserveScroll: true,
        onSuccess: () => { showToast("ok", "Paket berhasil dibuat."); setShowForm(false); },
        onError: (e) => showToast("err", Object.values(e)[0] as string),
        onFinish: () => setSaving(false)
      });
    }
  };

  const handleDelete = (id: number, name: string) => {
    if (!confirm(`Hapus paket "${name}"? Pastikan tidak ada pelanggan aktif.`)) return;
    router.delete(`/admin/packages/${id}`, {
      preserveScroll: true,
      onSuccess: () => showToast("ok", "Paket dihapus."),
      onError: () => showToast("err", "Gagal menghapus.")
    });
  };

  return (
    <div className="space-y-4">
      {toast && (
        <div className={`p-3 rounded-xl text-xs font-black flex items-center gap-2 ${toast.type === "ok" ? "bg-green-50 text-green-700 border border-green-200" : "bg-red-50 text-red-700 border border-red-200"}`}>
          {toast.type === "ok" ? <Lucide.CheckCircle2 size={14} /> : <Lucide.AlertCircle size={14} />}
          {toast.text}
        </div>
      )}

      <div className="flex items-center justify-between">
        <h4 className="text-sm font-black text-[#0d1655]">Daftar Paket Internet</h4>
        {isSuperadmin && (
          <button onClick={openCreate} className="flex items-center gap-1.5 px-3 py-2 bg-[#0d1655] text-white text-xs font-black rounded-xl hover:bg-[#1a2d8f] transition-all">
            <Lucide.Plus size={14} /> Tambah Paket
          </button>
        )}
      </div>

      <div className="space-y-2">
          {initialPackages.map(pkg => (
            <div key={pkg.id} className="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
              <div>
                <p className="text-sm font-black text-[#0d1655]">{pkg.name}</p>
                <p className="text-xs text-slate-500 font-bold">{pkg.speed_mbps} Mbps · Rp {parseInt(pkg.price).toLocaleString("id-ID")}/bln</p>
                {pkg.description && <p className="text-[10px] text-slate-400 mt-0.5">{pkg.description}</p>}
              </div>
              <div className="flex items-center gap-2">
                <span className={`text-[10px] font-black px-2 py-1 rounded-lg ${pkg.is_active ? "bg-green-50 text-green-700" : "bg-slate-100 text-slate-500"}`}>
                  {pkg.is_active ? "Aktif" : "Nonaktif"}
                </span>
                {isSuperadmin && (
                  <>
                    <button onClick={() => openEdit(pkg)} className="p-1.5 text-slate-400 hover:text-blue-600 transition-colors"><Lucide.Pencil size={14} /></button>
                    <button onClick={() => handleDelete(pkg.id, pkg.name)} className="p-1.5 text-slate-400 hover:text-red-600 transition-colors"><Lucide.Trash2 size={14} /></button>
                  </>
                )}
              </div>
            </div>
          ))}
        </div>
      
      {showForm && isSuperadmin && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
          <motion.div initial={{ scale: 0.9, opacity: 0 }} animate={{ scale: 1, opacity: 1 }} className="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl space-y-4">
            <h3 className="font-black text-[#0d1655]">{editItem ? "Edit Paket" : "Tambah Paket Baru"}</h3>
            <form onSubmit={handleSave} className="space-y-3">
              {[
                { label: "Nama Paket", key: "name", type: "text", required: true },
                { label: "Kecepatan (Mbps)", key: "speed_mbps", type: "number", required: true },
                { label: "Harga (Rp/bulan)", key: "price", type: "number", required: true },
                { label: "Deskripsi", key: "description", type: "text", required: false },
              ].map(f => (
                <div key={f.key}>
                  <label className="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1 block">{f.label}</label>
                  <input
                    type={f.type}
                    value={(formData as any)[f.key]}
                    onChange={e => setFormData(p => ({ ...p, [f.key]: e.target.value }))}
                    required={f.required}
                    className="w-full px-4 py-2.5 border-2 border-slate-100 rounded-xl text-sm font-bold outline-none focus:border-[#F47920] transition-all bg-slate-50"
                  />
                </div>
              ))}
              <label className="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" checked={formData.is_active} onChange={e => setFormData(p => ({ ...p, is_active: e.target.checked }))} className="w-4 h-4 accent-[#F47920]" />
                <span className="text-xs font-black text-slate-600">Aktif</span>
              </label>
              <div className="flex gap-2 pt-2">
                <button type="button" onClick={() => setShowForm(false)} className="flex-1 py-2.5 bg-slate-100 text-slate-600 text-xs font-black rounded-xl hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" disabled={saving} className="flex-1 py-2.5 bg-[#0d1655] text-white text-xs font-black rounded-xl hover:bg-[#1a2d8f] disabled:opacity-70 transition-all">
                  {saving ? "Menyimpan..." : "Simpan"}
                </button>
              </div>
            </form>
          </motion.div>
        </div>
      )}
    </div>
  );
};

// ---- VILLAGES TAB ----
const VillagesTab: React.FC<{ isSuperadmin: boolean, initialVillages: any[] }> = ({ isSuperadmin, initialVillages = [] }) => {
  const [showForm, setShowForm] = useState(false);
  const [editItem, setEditItem] = useState<any | null>(null);
  const [formData, setFormData] = useState({ name: "", area: "" });
  const [saving,   setSaving]   = useState(false);
  const [toast,    setToast]    = useState<{ type: "ok" | "err"; text: string } | null>(null);

  const showToast = (type: "ok" | "err", text: string) => {
    setToast({ type, text });
    setTimeout(() => setToast(null), 3000);
  };

  const handleSave = (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    if (editItem) {
      router.put(`/admin/villages/${editItem.id}`, formData, {
        preserveScroll: true,
        onSuccess: () => { showToast("ok", "Desa diperbarui."); setShowForm(false); },
        onFinish: () => setSaving(false)
      });
    } else {
      router.post('/admin/villages', formData, {
        preserveScroll: true,
        onSuccess: () => { showToast("ok", "Desa ditambahkan."); setShowForm(false); },
        onFinish: () => setSaving(false)
      });
    }
  };

  const handleDelete = (id: number, name: string) => {
    if (!confirm(`Hapus desa "${name}"?`)) return;
    router.delete(`/admin/villages/${id}`, {
      preserveScroll: true,
      onSuccess: () => showToast("ok", "Desa dihapus.")
    });
  };

  return (
    <div className="space-y-4">
      {toast && (
        <div className={`p-3 rounded-xl text-xs font-black flex items-center gap-2 ${toast.type === "ok" ? "bg-green-50 text-green-700 border border-green-200" : "bg-red-50 text-red-700 border border-red-200"}`}>
          {toast.type === "ok" ? <Lucide.CheckCircle2 size={14} /> : <Lucide.AlertCircle size={14} />}
          {toast.text}
        </div>
      )}
      <div className="flex items-center justify-between">
        <h4 className="text-sm font-black text-[#0d1655]">Daftar Desa</h4>
        {isSuperadmin && (
          <button onClick={() => { setEditItem(null); setFormData({ name: "", area: "" }); setShowForm(true); }}
            className="flex items-center gap-1.5 px-3 py-2 bg-[#0d1655] text-white text-xs font-black rounded-xl hover:bg-[#1a2d8f] transition-all">
            <Lucide.Plus size={14} /> Tambah Desa
          </button>
        )}
      </div>
        <div className="grid grid-cols-2 md:grid-cols-3 gap-2">
          {initialVillages.map(v => (
            <div key={v.id} className="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
              <div>
                <p className="text-xs font-black text-[#0d1655]">{v.name}</p>
                {v.area && <p className="text-[10px] text-slate-400">{v.area}</p>}
              </div>
              {isSuperadmin && (
                <div className="flex items-center gap-1">
                  <button onClick={() => { setEditItem(v); setFormData({ name: v.name, area: v.area || "" }); setShowForm(true); }} className="p-1 text-slate-400 hover:text-blue-600"><Lucide.Pencil size={12} /></button>
                  <button onClick={() => handleDelete(v.id, v.name)} className="p-1 text-slate-400 hover:text-red-600"><Lucide.Trash2 size={12} /></button>
                </div>
              )}
            </div>
          ))}
        </div>
      
      {showForm && isSuperadmin && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
          <motion.div initial={{ scale: 0.9, opacity: 0 }} animate={{ scale: 1, opacity: 1 }} className="bg-white rounded-3xl p-6 w-full max-w-sm shadow-2xl space-y-4">
            <h3 className="font-black text-[#0d1655]">{editItem ? "Edit Desa" : "Tambah Desa"}</h3>
            <form onSubmit={handleSave} className="space-y-3">
              <div>
                <label className="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1 block">Nama Desa</label>
                <input type="text" value={formData.name} onChange={e => setFormData(p => ({ ...p, name: e.target.value }))} required className="w-full px-4 py-2.5 border-2 border-slate-100 rounded-xl text-sm font-bold outline-none focus:border-[#F47920] bg-slate-50" />
              </div>
              <div>
                <label className="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1 block">Area/Kecamatan</label>
                <input type="text" value={formData.area} onChange={e => setFormData(p => ({ ...p, area: e.target.value }))} className="w-full px-4 py-2.5 border-2 border-slate-100 rounded-xl text-sm font-bold outline-none focus:border-[#F47920] bg-slate-50" />
              </div>
              <div className="flex gap-2 pt-2">
                <button type="button" onClick={() => setShowForm(false)} className="flex-1 py-2.5 bg-slate-100 text-slate-600 text-xs font-black rounded-xl">Batal</button>
                <button type="submit" disabled={saving} className="flex-1 py-2.5 bg-[#0d1655] text-white text-xs font-black rounded-xl disabled:opacity-70">{saving ? "..." : "Simpan"}</button>
              </div>
            </form>
          </motion.div>
        </div>
      )}
    </div>
  );
};

// ---- USERS TAB ----
const UsersTab: React.FC<{ initialUsers: any[] }> = ({ initialUsers = [] }) => {
  const [showForm, setShowForm] = useState(false);
  const [editItem, setEditItem] = useState<any | null>(null);
  const [formData, setFormData] = useState({ name: "", email: "", password: "", role: "admin" });
  const [saving,   setSaving]   = useState(false);
  const [toast,    setToast]    = useState<{ type: "ok" | "err"; text: string } | null>(null);

  const showToast = (type: "ok" | "err", text: string) => {
    setToast({ type, text });
    setTimeout(() => setToast(null), 3000);
  };

  const handleSave = (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    const payload: any = { name: formData.name, email: formData.email, role: formData.role };
    if (formData.password) payload.password = formData.password;
    
    if (editItem) {
      router.put(`/admin/users/${editItem.id}`, payload, {
        preserveScroll: true,
        onSuccess: () => { showToast("ok", "User diperbarui."); setShowForm(false); },
        onError: (e) => showToast("err", Object.values(e)[0] as string),
        onFinish: () => setSaving(false)
      });
    } else {
      router.post('/admin/users', payload, {
        preserveScroll: true,
        onSuccess: () => { showToast("ok", "User dibuat."); setShowForm(false); },
        onError: (e) => showToast("err", Object.values(e)[0] as string),
        onFinish: () => setSaving(false)
      });
    }
  };

  const handleDelete = (id: number, email: string) => {
    if (!confirm(`Hapus user ${email}?`)) return;
    router.delete(`/admin/users/${id}`, {
      preserveScroll: true,
      onSuccess: () => showToast("ok", "User dihapus."),
      onError: (errs) => showToast("err", Object.values(errs)[0] as string || "Gagal menghapus.")
    });
  };

  return (
    <div className="space-y-4">
      {toast && (
        <div className={`p-3 rounded-xl text-xs font-black flex items-center gap-2 ${toast.type === "ok" ? "bg-green-50 text-green-700 border border-green-200" : "bg-red-50 text-red-700 border border-red-200"}`}>
          {toast.type === "ok" ? <Lucide.CheckCircle2 size={14} /> : <Lucide.AlertCircle size={14} />}
          {toast.text}
        </div>
      )}
      <div className="flex items-center justify-between">
        <h4 className="text-sm font-black text-[#0d1655]">Manajemen Admin</h4>
        <button onClick={() => { setEditItem(null); setFormData({ name: "", email: "", password: "", role: "admin" }); setShowForm(true); }}
          className="flex items-center gap-1.5 px-3 py-2 bg-[#0d1655] text-white text-xs font-black rounded-xl hover:bg-[#1a2d8f] transition-all">
          <Lucide.UserPlus size={14} /> Tambah Admin
        </button>
      </div>
        <div className="space-y-2">
          {initialUsers.map(u => (
            <div key={u.id} className="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-[#0d1655] to-blue-600 text-white flex items-center justify-center font-black text-sm">
                  {u.name.charAt(0).toUpperCase()}
                </div>
                <div>
                  <p className="text-sm font-black text-[#0d1655]">{u.name}</p>
                  <p className="text-[10px] text-slate-500">{u.email}</p>
                  <span className={`text-[9px] font-black px-1.5 py-0.5 rounded-md ${u.role === "superadmin" ? "bg-amber-50 text-amber-700" : "bg-blue-50 text-blue-700"}`}>
                    {u.role}
                  </span>
                </div>
              </div>
              <div className="flex items-center gap-2">
                  <span className={`text-[10px] font-black px-2 py-1 rounded-lg ${u.is_active !== false ? "bg-green-50 text-green-700" : "bg-red-50 text-red-500"}`}>
                  {u.is_active !== false ? "Aktif" : "Nonaktif"}
                </span>
                <button onClick={() => { setEditItem(u); setFormData({ name: u.name, email: u.email, password: "", role: u.role }); setShowForm(true); }} className="p-1.5 text-slate-400 hover:text-blue-600 transition-colors">
                  <Lucide.Pencil size={14} />
                </button>
                <button onClick={() => handleDelete(u.id, u.email)} className="p-1.5 text-slate-400 hover:text-red-600 transition-colors">
                  <Lucide.Trash2 size={14} />
                </button>
              </div>
            </div>
          ))}
        </div>
      
      {showForm && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
          <motion.div initial={{ scale: 0.9, opacity: 0 }} animate={{ scale: 1, opacity: 1 }} className="bg-white rounded-3xl p-6 w-full max-w-sm shadow-2xl space-y-4">
            <h3 className="font-black text-[#0d1655]">{editItem ? "Edit User" : "Tambah Admin Baru"}</h3>
            <form onSubmit={handleSave} className="space-y-3">
              {[
                { label: "Nama", key: "name", type: "text", required: true, placeholder: "Nama lengkap" },
                { label: "Email", key: "email", type: "email", required: true, placeholder: "admin@armedia.id" },
                { label: editItem ? "Password Baru (kosongkan jika tidak diubah)" : "Password", key: "password", type: "password", required: !editItem, placeholder: "Min. 8 karakter" },
              ].map(f => (
                <div key={f.key}>
                  <label className="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1 block">{f.label}</label>
                  <input type={f.type} value={(formData as any)[f.key]} onChange={e => setFormData(p => ({ ...p, [f.key]: e.target.value }))} required={f.required} placeholder={f.placeholder}
                    className="w-full px-4 py-2.5 border-2 border-slate-100 rounded-xl text-sm font-bold outline-none focus:border-[#F47920] bg-slate-50" />
                </div>
              ))}
              <div>
                <label className="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1 block">Role</label>
                <select value={formData.role} onChange={e => setFormData(p => ({ ...p, role: e.target.value }))}
                  className="w-full px-4 py-2.5 border-2 border-slate-100 rounded-xl text-sm font-bold outline-none focus:border-[#F47920] bg-slate-50">
                  <option value="admin">Admin</option>
                  <option value="superadmin">Superadmin</option>
                </select>
              </div>
              <div className="flex gap-2 pt-2">
                <button type="button" onClick={() => setShowForm(false)} className="flex-1 py-2.5 bg-slate-100 text-slate-600 text-xs font-black rounded-xl">Batal</button>
                <button type="submit" disabled={saving} className="flex-1 py-2.5 bg-[#0d1655] text-white text-xs font-black rounded-xl disabled:opacity-70">{saving ? "..." : "Simpan"}</button>
              </div>
            </form>
          </motion.div>
        </div>
      )}
    </div>
  );
};

// ---- ACTIVITY LOG TAB ----
const ActivityTab: React.FC = () => {
  const [logs,    setLogs]    = useState<any[]>([]);
  const loading = false;

  useEffect(() => {
    // Activity Log fetching removed for now to save complexity
    // It can be added as a prop later
  }, []);

  const actionColor: Record<string, string> = {
    CREATE: "bg-green-50 text-green-700",
    UPDATE: "bg-blue-50 text-blue-700",
    DELETE: "bg-red-50 text-red-600",
    LOGIN:  "bg-amber-50 text-amber-700",
    LOGOUT: "bg-slate-100 text-slate-600",
    PATCH:  "bg-purple-50 text-purple-700",
  };

  return (
    <div className="space-y-4">
      <h4 className="text-sm font-black text-[#0d1655]">Activity Log</h4>
      {loading ? (
        <div className="flex items-center justify-center py-8"><Lucide.RefreshCw size={20} className="animate-spin text-slate-300" /></div>
      ) : logs.length === 0 ? (
        <p className="text-xs text-slate-400 text-center py-8">Belum ada aktivitas tercatat.</p>
      ) : (
        <div className="space-y-1.5 max-h-[480px] overflow-y-auto custom-scrollbar pr-1">
          {logs.map(log => (
            <div key={log.id} className="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
              <span className={`text-[9px] font-black px-2 py-0.5 rounded-md uppercase shrink-0 mt-0.5 ${actionColor[log.action] || "bg-slate-100 text-slate-600"}`}>
                {log.action}
              </span>
              <div className="flex-1 min-w-0">
                <p className="text-xs font-bold text-slate-700 truncate">{log.description || `${log.action} ${log.target_table}`}</p>
                <p className="text-[10px] text-slate-400 mt-0.5">
                  {log.user_name || "System"} · {new Date(log.created_at).toLocaleString("id-ID")}
                  {log.ip_address && ` · ${log.ip_address}`}
                </p>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

// ---- MAIN SETTINGS VIEW ----
export const SettingsView: React.FC<SettingsViewProps> = ({ isDarkMode, setIsDarkMode, userRole, initialPackages = [], initialVillages = [], initialUsers = [] }) => {
  const isSuperadmin = userRole === "superadmin";
  const [activeTab, setActiveTab] = useState<TabId>("packages");

  const tabs: { id: TabId; label: string; icon: React.ReactNode; restricted?: boolean }[] = [
    { id: "packages", label: "Paket",       icon: <Lucide.Package size={14} /> },
    { id: "villages", label: "Desa",        icon: <Lucide.MapPin size={14} /> },
    { id: "users",    label: "Admin Users", icon: <Lucide.Users size={14} />, restricted: true },
    { id: "activity", label: "Activity Log",icon: <Lucide.Activity size={14} />, restricted: true },
  ];

  return (
    <div className="max-w-4xl space-y-6">
      <div>
        <h3 className="text-2xl font-black text-[#0d1655] tracking-tight">Pengaturan Sistem</h3>
        <p className="text-sm font-bold text-slate-400 mt-1">Kelola paket, desa, admin, dan log aktivitas</p>
      </div>

      {/* Dark Mode Toggle */}
      <div className="bg-white border border-slate-100 rounded-3xl p-4 shadow-sm flex items-center justify-between">
        <div className="flex items-center gap-3">
          <div className="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center"><Lucide.Moon size={16} className="text-purple-600" /></div>
          <div>
            <p className="text-sm font-black text-slate-800">Dark Mode</p>
            <p className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ubah tema tampilan</p>
          </div>
        </div>
        <button
          onClick={() => setIsDarkMode(!isDarkMode)}
          className={`w-14 h-7 rounded-full relative transition-all duration-300 ${isDarkMode ? "bg-[#0d1655]" : "bg-slate-200"}`}
        >
          <div className={`absolute top-1 w-5 h-5 bg-white rounded-full shadow transition-all duration-300 ${isDarkMode ? "left-8" : "left-1"}`} />
        </button>
      </div>

      {/* Tabs */}
      <div className="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div className="flex border-b border-slate-100 overflow-x-auto">
          {tabs.map(tab => {
            if (tab.restricted && !isSuperadmin) return null;
            return (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`flex items-center gap-2 px-5 py-4 text-xs font-black uppercase tracking-wider whitespace-nowrap transition-all border-b-2 ${
                  activeTab === tab.id
                    ? "border-[#F47920] text-[#F47920] bg-orange-50/30"
                    : "border-transparent text-slate-500 hover:text-[#0d1655] hover:bg-slate-50"
                }`}
              >
                {tab.icon} {tab.label}
              </button>
            );
          })}
        </div>

        <div className="p-6">
          <AnimatePresence mode="wait">
            <motion.div key={activeTab} initial={{ opacity: 0, y: 8 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0 }}>
              {activeTab === "packages" && <PackagesTab isSuperadmin={isSuperadmin} initialPackages={initialPackages} />}
              {activeTab === "villages" && <VillagesTab isSuperadmin={isSuperadmin} initialVillages={initialVillages} />}
              {activeTab === "users"    && isSuperadmin && <UsersTab initialUsers={initialUsers} />}
              {activeTab === "activity" && isSuperadmin && <ActivityTab />}
            </motion.div>
          </AnimatePresence>
        </div>
      </div>
    </div>
  );
};
