import React, { useState, useEffect, lazy, Suspense } from 'react';
import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';

import WhatsAppButton from '@/Components/OldComponents/ui/wa-button';
import ScrollObserver from '@/Components/OldComponents/ui/scroll-observer';
import DarkModeToggle from '@/Components/OldComponents/ui/dark-mode-toggle';
import FomoNotification from '@/Components/OldComponents/sections/FomoNotification';
import ACRSection from '@/Components/OldComponents/sections/ACRSection';
import BandwidthCalculator from '@/Components/OldComponents/sections/BandwidthCalculator';

const CoverageSection = lazy(() => import('@/Components/OldComponents/sections/Coverage'));

/* ─────────────────────────────────────────────
   TYPES
   ───────────────────────────────────────────── */
type Testimonial = {
  id: number;
  quote: string;
  author_name: string;
  author_role: string;
  avatar_initials: string;
};

type Article = {
  id: number;
  category: string;
  title: string;
  excerpt: string;
  image_url: string;
};

type InternetPackageType = {
  id: number;
  nama_paket: string;
  kecepatan: string;
  harga: number;
  keterangan_promo: string | null;
  is_active: boolean;
};

interface WelcomeProps {
  testimonials: Testimonial[];
  articles: Article[];
  internetPackages: InternetPackageType[];
  webSettings: Record<string, string>;
}

export default function Welcome({ testimonials, articles, internetPackages, webSettings }: WelcomeProps) {
  // State Manajemen Komponen Form Modal & Mobile Drawer
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [selectedPackage, setSelectedPackage] = useState<number | string>(internetPackages?.[0]?.id || '');
  const [activeTab, setActiveTab] = useState('home');

  // Handler interaksi paket & registrasi otomatis ke portal baru
  const openRegisterModal = (paket: string) => {
    // Membuka modal pendaftaran (bukan redirect ke domain luar)
    setSelectedPackage(paket);
    setIsModalOpen(true);
  };

  // Handler smooth scroll navigasi internal
  const scrollToSection = (sectionId: string, tabName: string) => {
    setActiveTab(tabName);
    setIsMobileMenuOpen(false);
    const element = document.getElementById(sectionId);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  return (
    <div className="relative min-h-screen bg-white font-sans text-slate-900 antialiased selection:bg-red-500/10 scroll-smooth">
      <Head title="PT Akses Artha Media" />
      
      {/* FOMO Social Proof Notification */}
      <FomoNotification />
      
      {/* WhatsApp Floating Button */}
      <WhatsAppButton />
      
      {/* ================= 1. NAVBAR SECTION ================= */}
      <header className="fixed inset-x-0 top-0 z-50 border-b border-slate-100 bg-white/90 backdrop-blur-md">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
          <a href="#" onClick={(e) => { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); }} className="transition-transform duration-300 hover:scale-105 active:scale-95 block py-2" aria-label="Home">
            <img 
              src="https://ik.imagekit.io/Gumelar/LogO/logo%20pt.png?updatedAt=1778213993513" 
              alt="PT Akses Artha Media Logo" 
              className="h-10 w-auto object-contain"
            />
          </a>
          
          {/* Navigasi Desktop Menu */}
          <nav className="hidden items-center gap-6 text-xs font-bold uppercase tracking-wider text-slate-600 md:flex">
            <button onClick={() => { window.scrollTo({ top: 0, behavior: 'smooth' }); setActiveTab('home'); }} className={`cursor-pointer py-2 px-1 transition-colors duration-300 ${activeTab === 'home' ? 'text-red-600' : 'hover:text-red-600'}`}>HOME</button>
            <button onClick={() => scrollToSection('about-section', 'about')} className={`cursor-pointer py-2 px-1 transition-colors duration-300 ${activeTab === 'about' ? 'text-red-600' : 'hover:text-red-600'}`}>TENTANG KAMI</button>
            <button onClick={() => scrollToSection('services-section', 'services')} className={`cursor-pointer py-2 px-1 transition-colors duration-300 ${activeTab === 'services' ? 'text-red-600' : 'hover:text-red-600'}`}>LAYANAN</button>
            <button onClick={() => scrollToSection('acr-section', 'acr')} className={`cursor-pointer py-2 px-1 transition-colors duration-300 ${activeTab === 'acr' ? 'text-red-600' : 'hover:text-red-600'}`}>ACR REWARD</button>
            <button onClick={() => scrollToSection('testimonials-section', 'testimonials')} className={`cursor-pointer py-2 px-1 transition-colors duration-300 ${activeTab === 'testimonials' ? 'text-red-600' : 'hover:text-red-600'}`}>TESTIMONI</button>
            <button onClick={() => scrollToSection('blog-section', 'blog')} className={`cursor-pointer py-2 px-1 transition-colors duration-300 ${activeTab === 'blog' ? 'text-red-600' : 'hover:text-red-600'}`}>ARTIKEL & BERITA</button>
          </nav>

          <div className="hidden md:flex gap-4">
            <Link
              href="/login-member"
              className="inline-block rounded-md bg-slate-100 px-6 py-3 text-xs font-bold uppercase tracking-wider text-slate-900 shadow-sm transition-all duration-300 hover:bg-slate-200 cursor-pointer"
            >
              LOGIN MEMBER
            </Link>
            <button
              onClick={() => openRegisterModal(internetPackages?.[0]?.id?.toString() || '')}
              className="inline-block rounded-md bg-red-600 px-6 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-sm transition-all duration-300 hover:bg-slate-900 cursor-pointer transform hover:-translate-y-0.5 active:translate-y-0"
            >
              MENU DAFTAR
            </button>
          </div>

          {/* Hamburger Trigger Tombol Mode HP */}
          <button 
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
            className="flex h-12 w-12 items-center justify-center rounded-md border border-slate-200 text-slate-700 md:hidden hover:bg-slate-50 cursor-pointer transition-colors"
            aria-label="Toggle Menu"
          >
            <span className="text-xl font-bold">{isMobileMenuOpen ? '✕' : '☰'}</span>
          </button>
        </div>

        {/* Dropdown Menu Khusus Mode HP */}
        {isMobileMenuOpen && (
          <div className="border-t border-slate-100 bg-white px-6 py-4 space-y-3 flex flex-col md:hidden shadow-xl animate-fade-in">
            <button onClick={() => { window.scrollTo({ top: 0, behavior: 'smooth' }); setIsMobileMenuOpen(false); setActiveTab('home'); }} className="text-left py-4 block w-full text-sm font-bold text-red-600">HOME</button>
            <button onClick={() => scrollToSection('about-section', 'about')} className="text-left py-4 block w-full text-sm font-bold text-slate-600 hover:text-red-600">TENTANG KAMI</button>
            <button onClick={() => scrollToSection('services-section', 'services')} className="text-left py-4 block w-full text-sm font-bold text-slate-600 hover:text-red-600">LAYANAN</button>
            <button onClick={() => scrollToSection('acr-section', 'acr')} className="text-left py-4 block w-full text-sm font-bold text-slate-600 hover:text-red-600">ACR REWARD</button>
            <button onClick={() => scrollToSection('testimonials-section', 'testimonials')} className="text-left py-4 block w-full text-sm font-bold text-slate-600 hover:text-red-600">TESTIMONI</button>
            <button onClick={() => scrollToSection('blog-section', 'blog')} className="text-left py-4 block w-full text-sm font-bold text-slate-600 hover:text-red-600">ARTIKEL & BERITA</button>
            <Link
              href="/login-member"
              className="block w-full mt-2 rounded-md bg-slate-100 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-900 shadow-sm"
            >
              LOGIN MEMBER
            </Link>
            <button
              onClick={() => openRegisterModal(internetPackages?.[0]?.id?.toString() || '')}
              className="block w-full mt-2 rounded-md bg-red-600 py-4 text-center text-xs font-bold uppercase tracking-wider text-white shadow-sm"
            >
              MENU DAFTAR
            </button>
          </div>
        )}
      </header>

      {/* Scroll Observer Wrapper */}
      <ScrollObserver className="contents">
      
      <main className="pt-24">
        
        {/* ================= 2. HERO SECTION WITH RESPONSIVE BANNER ================= */}
        <section className="mx-auto max-w-7xl px-6 py-16 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <motion.div 
            initial={{ opacity: 0, x: -30 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.8, ease: "easeOut" }}
            className="flex flex-col items-start text-left"
          >
            <div className="inline-flex items-center gap-2 rounded bg-red-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-red-600 animate-pulse">
              🇮🇩 Konektivitas Tanpa Batas Masa Depan
            </div>
            <h1 className="mt-6 text-5xl font-black leading-[1.1] tracking-tight text-slate-900 sm:text-6xl xl:text-7xl">
              Jembatan Digital <br />
              <span className="text-red-600">Masa Depan</span>
            </h1>
            <p className="mt-6 text-base leading-relaxed text-slate-600 max-w-xl text-justify">
              Solusi internet Fiber Optic dan VSAT terbaik untuk mengakselerasi digitalisasi bisnis Anda. Kami percaya bahwa akses internet yang stabil dan cepat adalah fondasi utama dalam percepatan digitalisasi di Indonesia.
            </p>
            <div className="mt-10 flex flex-wrap items-center gap-6">
              <motion.button
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
                onClick={() => openRegisterModal(internetPackages?.[0]?.id?.toString() || '')}
                className="rounded-md bg-red-600 px-8 py-4 text-sm font-bold uppercase tracking-wider text-white shadow-lg shadow-red-600/10 transition-all duration-300 hover:bg-slate-900 hover:shadow-xl cursor-pointer"
              >
                MULAI DAFTAR SEKARANG
              </motion.button>
              <div className="flex flex-col border-l border-slate-200 pl-6">
                <span className="text-[10px] text-slate-600 uppercase font-black tracking-widest">CUKUP MBAYAR WULANANE</span>
                <span className="text-2xl font-black tracking-tight text-red-600">Rp {internetPackages?.[0]?.harga?.toLocaleString('id-ID') || '115.000'}<span className="text-sm font-medium text-slate-600">/bln</span></span>
              </div>
            </div>
          </motion.div>
          
          <motion.div 
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.8, delay: 0.2, ease: "easeOut" }}
            className="relative h-[400px] w-full rounded-2xl overflow-hidden shadow-xl border border-slate-100 group"
          >
            <img 
              src="/images/hero-fiber-optic.png" 
              onError={(e) => { e.currentTarget.src = "/hero-fiber-optic.png" }}
              alt="ARMEDIA Fiber Infrastructure" 
              className="absolute inset-0 object-cover w-full h-full transition-transform duration-700 group-hover:scale-105"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent pointer-events-none"></div>
            <div className="absolute bottom-6 left-6 right-6 flex justify-between items-center z-10 backdrop-blur-md bg-white/20 p-4 rounded-xl border border-white/10">
              <span className="text-xs font-bold text-white uppercase tracking-widest flex items-center gap-2">
                <span className="h-2 w-2 rounded-full bg-emerald-400 animate-ping"></span> Jaringan Aktif Fiber Optik
              </span>
              <span className="text-[10px] font-black text-white bg-red-600 px-2 py-0.5 rounded">CORE NODE</span>
            </div>
          </motion.div>
        </section>

        {/* ================= 3. TENTANG KAMI SECTION ================= */}
        <section id="about-section" className="mx-auto max-w-7xl px-6 py-24 lg:px-8 border-t border-slate-100 scroll-mt-20">
          <motion.div 
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: "-100px" }}
            transition={{ duration: 0.6 }}
            className="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start"
          >
            <div className="relative group">
              <span className="text-xs font-bold uppercase tracking-widest text-red-600">ABOUT COMPANY</span>
              <h2 className="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                PT AKSES ARTHA MEDIA (ARMEDIA)
              </h2>
              <p className="mt-3 text-sm font-black text-red-600 tracking-wide uppercase flex items-center gap-2">
                "Jembatan Digital Masa Depan"
                <span className="animate-bounce">🚀</span>
              </p>
            </div>
            <div className="lg:col-span-2 space-y-6 text-base leading-relaxed text-slate-600 font-medium text-justify">
              <p>
                PT AKSES ARTHA MEDIA atau ARMEDIA adalah perusahaan penyedia layanan internet (ISP) yang berkomitmen untuk menghadirkan konektivitas tanpa batas bagi masyarakat dan pelaku usaha. Kami percaya bahwa akses internet yang stabil dan cepat adalah fondasi utama dalam percepatan digitalisasi di Indonesia.
              </p>
              <p>
                Melalui infrastruktur jaringan yang andal dan inovatif, ARMEDIA hadir sebagai mitra strategis yang berorientasi pada kepuasan pelanggan. Kami menyediakan solusi konektivitas berkecepatan tinggi yang dirancang untuk mendukung berbagai kebutuhan—mulai dari kenyamanan residensial, produktivitas perkantoran, hingga efisiensi operasional UMKM.
              </p>
            </div>
          </motion.div>
        </section>

        {/* ================= 3.5. KALKULATOR BANDWIDTH SECTION ================= */}
        <BandwidthCalculator onSelectPackage={(pkgId) => {
          setSelectedPackage(pkgId);
          const el = document.getElementById('services-section');
          if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }} />

        {/* ================= 4. VALUE PLANS / LAYANAN SECTION ================= */}
        <section id="services-section" className="bg-slate-50 py-24 border-t border-slate-100 scroll-mt-20">
          <motion.div 
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: "-100px" }}
            transition={{ duration: 0.6 }}
            className="mx-auto max-w-7xl px-6 lg:px-8"
          >
            <div className="text-center">
              <span className="text-xs font-bold uppercase tracking-widest text-red-600">PAKET INTERNET</span>
              <h2 className="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                Cukup Mbayar Wulanane
              </h2>
            </div>

            <div className="mt-16 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
              {internetPackages?.map((pkg) => {
                const isSelected = selectedPackage == pkg.id;
                const isPopular = pkg.keterangan_promo?.toLowerCase().includes('populer');
                return (
                  <motion.div
                    key={pkg.id}
                    onClick={() => setSelectedPackage(pkg.id)}
                    whileHover={{ y: -8, transition: { duration: 0.2 } }}
                    className={`rounded-2xl border bg-white p-6 shadow-sm flex flex-col justify-between transition-all duration-300 cursor-pointer relative
                      ${isSelected 
                        ? 'border-red-600 border-2 ring-4 ring-red-600/10 shadow-md' 
                        : 'border-slate-200 hover:shadow-md hover:border-slate-300'
                      }`}
                  >
                    {isPopular && (
                      <div className="absolute -top-3.5 inset-x-0 mx-auto w-max rounded-full bg-red-600 px-4 py-1 text-[10px] font-black text-white uppercase tracking-widest flex items-center gap-1 shadow-sm">
                        🔥 POPULER
                      </div>
                    )}
                    <div>
                      <div className="flex items-center justify-between gap-2">
                        <h3 className="text-sm font-extrabold text-slate-900 truncate">{pkg.nama_paket}</h3>
                        <span className={`shrink-0 rounded px-2 py-0.5 text-[10px] font-black uppercase tracking-wider
                          ${isPopular 
                            ? 'bg-red-600 text-white' 
                            : 'bg-slate-100 text-slate-700'
                          }`}
                        >
                          {pkg.kecepatan}
                        </span>
                      </div>
                      <div className="mt-6 border-t border-slate-100 pt-6">
                        <p className="mt-1 flex items-baseline gap-1">
                          <span className={`text-2xl sm:text-3xl font-black tracking-tight ${isSelected || isPopular ? 'text-red-600' : 'text-slate-900'}`}>
                            Rp {pkg.harga.toLocaleString('id-ID')}
                          </span>
                          <span className="text-xs font-semibold text-slate-600">/bln</span>
                        </p>
                      </div>
                      <ul className="mt-8 space-y-4 text-xs font-medium text-slate-600">
                        <li className="flex items-center gap-3">
                          <span className={`${isPopular || isSelected ? 'text-red-600' : 'text-slate-600'} font-bold text-xs`}>✓</span> 
                          Download / Upload Unlimited
                        </li>
                        <li className="flex items-center gap-3">
                          <span className={`${isPopular || isSelected ? 'text-red-600' : 'text-slate-600'} font-bold text-xs`}>✓</span> 
                          DUAL BAND 2,4G & 5G
                        </li>
                        {pkg.keterangan_promo && !isPopular && (
                           <li className="flex items-center gap-3">
                             <span className={`${isSelected ? 'text-red-600' : 'text-slate-600'} font-bold text-xs`}>✓</span> 
                             {pkg.keterangan_promo}
                           </li>
                        )}
                      </ul>
                    </div>
                    <button
                      onClick={(e) => {
                        e.stopPropagation();
                        openRegisterModal(pkg.id.toString());
                      }}
                      className={`mt-8 block w-full rounded-md py-4 text-center text-xs font-bold uppercase tracking-wider transition-all cursor-pointer shadow-sm
                        ${isSelected 
                          ? 'bg-red-600 text-white hover:bg-slate-900' 
                          : 'bg-slate-900 text-white hover:bg-red-600'
                        }`}
                    >
                      {isSelected ? '✓ DIPILIH' : 'PILIH PAKET'}
                    </button>
                  </motion.div>
                );
              })}
            </div>
          </motion.div>
        </section>

        {/* ================= 5. KEUNGGULAN / WHY US SECTION WITH VECTOR SVG ICONS ================= */}
        <section id="why-us-section" className="mx-auto max-w-7xl px-6 py-24 lg:px-8 scroll-mt-20">
          <motion.div 
            initial={{ opacity: 0, scale: 0.95 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true, margin: "-100px" }}
            transition={{ duration: 0.6 }}
            className="grid grid-cols-1 gap-12 lg:grid-cols-3 text-center lg:text-left"
          >
            <div className="lg:col-span-1 flex flex-col justify-center">
              <span className="text-xs font-bold uppercase tracking-widest text-red-600">MENGAPA BERMITRA</span>
              <h2 className="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                Keunggulan Utama Layanan ARMEDIA<span className="text-red-600">.</span>
              </h2>
            </div>
            
            <div className="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-6">
              {/* SVG Icon 1 */}
              <div className="rounded-2xl border border-slate-100 bg-slate-50/50 p-8 flex flex-col items-center justify-between text-center group hover:bg-white hover:shadow-lg transition-all duration-300">
                <div className="h-16 w-16 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-3xl transition-transform duration-500 group-hover:rotate-12 group-hover:scale-105">
                  <svg className="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
                <span className="mt-5 text-sm font-black text-red-600 tracking-tight uppercase">100% Local Support</span>
                <span className="mt-2 text-[11px] font-medium text-slate-600 leading-relaxed">Respon teknis mendampingi transformasi digital Anda kapanpun dibutuhkan.</span>
              </div>

              {/* SVG Icon 2 */}
              <div className="rounded-2xl border border-slate-100 bg-slate-50/50 p-8 flex flex-col items-center justify-between text-center group hover:bg-white hover:shadow-lg transition-all duration-300">
                <div className="h-16 w-16 rounded-full bg-slate-900 text-white flex items-center justify-center text-3xl transition-transform duration-500 group-hover:scale-110">
                  <svg className="h-7 w-7 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <span className="mt-5 text-sm font-black text-slate-900 tracking-tight uppercase">Koneksi Stabil</span>
                <span className="mt-2 text-[11px] font-medium text-slate-600 leading-relaxed">Infrastruktur Fiber Optic modern menjamin ketahanan bottleneck jam sibuk.</span>
              </div>

              {/* SVG Icon 3 */}
              <div className="rounded-2xl border border-slate-100 bg-slate-50/50 p-8 flex flex-col items-center justify-between text-center group hover:bg-white hover:shadow-lg transition-all duration-300">
                <div className="h-16 w-16 rounded-full bg-slate-900 text-white flex items-center justify-center text-3xl transition-transform duration-500 group-hover:-translate-y-1">
                  <svg className="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <span className="mt-5 text-sm font-black text-slate-900 tracking-tight uppercase">Layanan Terintegrasi</span>
                <span className="mt-2 text-[11px] font-medium text-slate-600 leading-relaxed">Solusi digital adaptif dirancang khusus skala personal hingga enterprise.</span>
              </div>
            </div>
          </motion.div>
        </section>

        {/* ================= 5.5. REWARD SECTION ================= */}
        <ACRSection />

        {/* ================= 6. TESTIMONI SECTION ================= */}
        <section id="testimonials-section" className="bg-slate-50 py-24 border-t border-b border-slate-100 scroll-mt-20">
          <motion.div 
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: "-100px" }}
            transition={{ duration: 0.6 }}
            className="mx-auto max-w-7xl px-6 lg:px-8"
          >
            <div className="max-w-2xl mx-auto text-center mb-16">
              <span className="text-xs font-bold uppercase tracking-widest text-red-600">TESTIMONI</span>
              <h2 className="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                Apa Kata Mereka?
              </h2>
            </div>

            <div className="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
              {testimonials && testimonials.length > 0 ? (
                testimonials.map((t) => (
                  <div key={t.id} className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between transition-all duration-300 transform hover:-translate-y-1 hover:shadow-md">
                    <p className="text-xs italic text-slate-600 leading-relaxed">
                      "{t.quote}"
                    </p>
                    <div className="mt-6 border-t border-slate-100 pt-4 flex items-center gap-3">
                      <div className="h-8 w-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-xs">{t.avatar_initials}</div>
                      <div>
                        <h3 className="text-xs font-bold text-slate-900">{t.author_name}</h3>
                        <p className="text-[10px] text-slate-600 font-semibold">{t.author_role}</p>
                      </div>
                    </div>
                  </div>
                ))
              ) : (
                <p className="col-span-full text-center text-xs text-slate-600">Belum ada testimoni.</p>
              )}
            </div>
          </motion.div>
        </section>

        {/* ================= 7. ARTIKEL & BERITA ================= */}
        <section id="blog-section" className="mx-auto max-w-7xl px-6 py-24 lg:px-8 scroll-mt-20">
          <motion.div 
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: "-100px" }}
            transition={{ duration: 0.6 }}
          >
            <div className="max-w-2xl mx-auto text-center mb-16">
            <span className="text-xs font-bold uppercase tracking-widest text-red-600">WAWASAN DIGITAL</span>
            <h2 className="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
              Artikel & Berita Terbaru
            </h2>
          </div>

          <div className="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
            {articles && articles.length > 0 ? (
              articles.map((a) => (
                <div key={a.id} className="group rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-lg">
                  <div className="h-40 w-full relative overflow-hidden bg-slate-100">
                    <img src={a.image_url} alt={a.title} className="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" />
                  </div>
                  <div className="p-5 flex-1 flex flex-col justify-between">
                    <div>
                      <span className="text-[9px] font-black tracking-widest uppercase text-red-600">{a.category}</span>
                      <h3 className="mt-2 text-sm font-bold text-slate-900 leading-snug group-hover:text-red-600 transition-colors">{a.title}</h3>
                      <p className="mt-1 text-xs text-slate-600 line-clamp-2">{a.excerpt}</p>
                    </div>
                    <a href="#" aria-label={`Baca artikel: ${a.title}`} className="mt-4 inline-flex items-center text-xs font-bold text-red-600 uppercase tracking-wider py-2">
                      BACA <span className="ml-1 transition-transform group-hover:translate-x-1">→</span>
                    </a>
                  </div>
                </div>
              ))
            ) : (
              <p className="col-span-full text-center text-xs text-slate-600">Belum ada artikel.</p>
            )}
          </div>
          </motion.div>
        </section>

        {/* ================= 8.5 COVERAGE SECTION ================= */}
        <Suspense fallback={<div>Loading map...</div>}>
          <CoverageSection />
        </Suspense>

      </main>

      {/* ================= 8. OVERLAY MODAL FORM ================= */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm transition-opacity duration-300">
          <div className="absolute inset-0" onClick={() => setIsModalOpen(false)}></div>
          
          <div className="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-2xl z-10 transform scale-100 transition-transform duration-300">
            <button 
              onClick={() => setIsModalOpen(false)}
              className="absolute top-4 right-4 text-slate-600 hover:text-slate-900 font-bold text-lg p-2 rounded-full hover:bg-slate-100 transition-all cursor-pointer"
            >
              ✕
            </button>

            <div className="text-center mb-8 pr-6">
              <span className="text-xs font-black uppercase tracking-widest text-red-600">📡 ONLINE APPLICATION</span>
              <h2 className="mt-1 text-2xl font-black tracking-tight text-slate-900">Formulir Registrasi Pemasangan</h2>
              <p className="text-xs text-slate-600 mt-1">Lengkapi data untuk konfirmasi node area cakupan tim lapangan.</p>
            </div>

            <form onSubmit={async (e) => { 
                e.preventDefault(); 
                const form = e.currentTarget; 
                const namaInput = form.querySelector<HTMLInputElement>("input[name='nama']"); 
                const waInput = form.querySelector<HTMLInputElement>("input[type='tel']"); 
                const langgananSelect = form.querySelector<HTMLSelectElement>("select"); 
                const allSelects = form.querySelectorAll<HTMLSelectElement>("select"); 
                const desaSelect = allSelects[2]; 
                const tglSelect = allSelects[3]; 
                const waktuSelect = allSelects[4]; 
                const alamatTextarea = form.querySelector<HTMLTextAreaElement>("textarea"); 
                const nama = namaInput?.value?.trim() || "Pelanggan"; 
                
                try { 
                  // Post ke endpoint Laravel kita yang baru
                  const res = await fetch("/api/register", { 
                    method: "POST", 
                    headers: { "Content-Type": "application/json" }, 
                    body: JSON.stringify({ 
                      paket: selectedPackage, 
                      langganan_sebelumnya: langgananSelect?.value, 
                      nama, 
                      whatsapp: waInput?.value?.trim(), 
                      desa: desaSelect?.value, 
                      alamat: alamatTextarea?.value?.trim(), 
                      tanggal_pemasangan: tglSelect?.value, 
                      waktu_survei: waktuSelect?.value, 
                    }), 
                  }); 
                  if (!res.ok) throw new Error(); 
                } catch (_) {} 
                setIsModalOpen(false); 
                window.location.href = `/terima-kasih?paket=${encodeURIComponent(selectedPackage)}&nama=${encodeURIComponent(nama)}`; 
              }} 
              className="space-y-5 text-left"
            >
              <div>
                <label className="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1.5">PAKET LAYANAN INTERNET PILIHAN *</label>
                <select 
                  value={selectedPackage} 
                  onChange={(e) => setSelectedPackage(e.target.value)}
                  className="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs font-bold text-slate-900 focus:border-red-600 focus:outline-none"
                >
                  {internetPackages?.map(pkg => (
                    <option key={pkg.id} value={pkg.id}>
                      {pkg.nama_paket} - {pkg.kecepatan} (Rp {pkg.harga.toLocaleString('id-ID')} / bln)
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1.5">APAKAH SAAT INI ANDA SUDAH LANGGANAN INTERNET? *</label>
                <select className="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-900 focus:border-red-600 focus:outline-none">
                  <option>Belum Pernah Pasang</option>
                  <option>Internet Lokal (RT/RW NET)</option>
                  <option>ISP Besar (Indihome, Biznet, dll)</option>
                </select>
              </div>

              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label className="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1.5">NAMA LENGKAP (SESUAI KTP) *</label>
                  <input name="nama" type="text" required placeholder="Contoh: Ahmad Tauchid" className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none" />
                </div>
                <div>
                  <label className="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1.5">NOMOR WHATSAPP AKTIF *</label>
                  <input type="tel" required placeholder="Contoh: 08XXXXXXXXXX" className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none" />
                </div>
              </div>

              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label className="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1.5">KECAMATAN *</label>
                  <select disabled className="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-xs font-bold text-slate-700">
                    <option>GUMELAR</option>
                  </select>
                </div>
                <div>
                  <label className="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1.5">DESA / KELURAHAN *</label>
                  <select className="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-900 focus:border-red-600 focus:outline-none">
                    <option>GUMELAR</option>
                    <option>CIHONJE</option>
                    <option>TLAGA</option>
                    <option>SAMUDRA KULON</option>
                    <option>SAMUDRA</option>
                    <option>PANINGKABAN</option>
                    <option>KARANG KEMOJING</option>
                    <option>GANCANG</option>
                    <option>KEDUNGURANG</option>
                  </select>
                </div>
              </div>

              <div>
                <label className="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1.5">ALAMAT LENGKAP (RT/RW) *</label>
                <textarea required rows={2} placeholder="Sebutkan rincian alamat rumah lengkap" className="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-xs focus:border-red-600 focus:outline-none"></textarea>
              </div>

              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label className="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1.5">PILIH TANGGAL PEMASANGAN *</label>
                  <select className="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-900 focus:border-red-600 focus:outline-none">
                    <option>Secepatnya</option>
                    <option>Hari Ini</option>
                    <option>Besok</option>
                  </select>
                </div>
                <div>
                  <label className="block text-[10px] font-black uppercase tracking-wider text-slate-700 mb-1.5">WAKTU SURVEI YANG PAS *</label>
                  <select className="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-900 focus:border-red-600 focus:outline-none">
                    <option>Pagi (08:00 - 11:00)</option>
                    <option>Siang (11:00 - 14:00)</option>
                    <option>Sore (14:00 - 17:00)</option>
                    <option>Malam (18:00 - 20:00)</option>
                  </select>
                </div>
              </div>

              <button type="submit" className="w-full rounded-lg bg-red-600 py-3.5 text-center text-xs font-black uppercase tracking-wider text-white shadow-md shadow-red-600/10 transition-all hover:bg-slate-900 cursor-pointer">
                🚀 KIRIM PENDAFTARAN LAYANAN
              </button>
            </form>
          </div>
        </div>
      )}

        {/* ================= 9. FOOTER SECTION ================= */}
      <footer className="bg-slate-900 text-white pt-20 pb-8 border-t border-slate-800">
        <motion.div 
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          viewport={{ once: true }}
          transition={{ duration: 0.8 }}
          className="mx-auto max-w-7xl px-6 lg:px-8"
        >
          <div className="grid grid-cols-1 gap-12 md:grid-cols-3">
            <div className="space-y-4">
              <a href="#" onClick={(e) => { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); }} className="inline-block" aria-label="Home">
                <img 
                  src="https://ik.imagekit.io/Gumelar/LogO/logo%20pt.png?updatedAt=1778213993513" 
                  alt="PT Akses Artha Media Logo Footer" 
                  className="h-10 w-auto object-contain brightness-0 invert"
                />
              </a>
              <p className="text-xs text-slate-400 leading-relaxed text-justify">
                PT AKSES ARTHA MEDIA adalah perusahaan penyedia layanan internet (ISP) berlisensi resmi yang berkomitmen menghadirkan konektivitas tanpa batas bagi masyarakat dan pelaku usaha.
              </p>
            </div>
            <div>
              <h3 className="text-xs font-black uppercase tracking-widest text-slate-300 border-l-2 border-red-600 pl-3 mb-6">NAVIGASI ELEMEN</h3>
              <ul className="space-y-1 text-xs text-slate-400 font-semibold">
                <li><button onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })} className="hover:text-red-500 transition-colors cursor-pointer text-left py-4 block w-full">Home</button></li>
                <li><button onClick={() => scrollToSection('about-section', 'about')} className="hover:text-red-500 transition-colors cursor-pointer text-left py-4 block w-full">Tentang Kami</button></li>
                <li><button onClick={() => scrollToSection('services-section', 'services')} className="hover:text-red-500 transition-colors cursor-pointer text-left py-4 block w-full">Infrastruktur Jaringan</button></li>
                <li><button onClick={() => scrollToSection('blog-section', 'blog')} className="hover:text-red-500 transition-colors cursor-pointer text-left py-4 block w-full">Artikel & Berita</button></li>
              </ul>
            </div>
            <div>
              <h3 className="text-xs font-black uppercase tracking-widest text-slate-300 border-l-2 border-red-600 pl-3 mb-6">HUBUNGI KAMI</h3>
              <div className="space-y-4 text-xs text-slate-400">
                <div>
                  <p className="font-bold text-slate-300">Email Dukungan Layanan:</p>
                  <p className="mt-0.5 font-mono text-red-400">admin@armedia.id</p>
                </div>
                <div>
                  <p className="font-bold text-slate-300">Nomor Telepon Kantor (Office):</p>
                  <p className="mt-0.5 font-mono text-slate-300">(021) 22472319</p>
                </div>
              </div>
            </div>
          </div>
          <div className="mt-16 pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 text-[11px] font-semibold text-slate-600">
            <p>© 2026 PT AKSES ARTHA MEDIA. All rights reserved.</p>
          </div>
        </motion.div>
      </footer>

      </ScrollObserver>
    </div>
  );
}
