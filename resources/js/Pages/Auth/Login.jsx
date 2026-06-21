import { useEffect } from 'react';
import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import TextInput from '@/Components/TextInput';
import { Head, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Lock, Phone, ArrowRight, ShieldCheck, Zap } from 'lucide-react';

export default function Login({ status }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        whatsapp: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <div className="min-h-screen bg-slate-50 flex flex-col md:flex-row overflow-hidden font-sans">
            <Head title="Login Admin - ARMEDIA" />

            {/* Kiri: Sisi Grafis / Branding */}
            <motion.div 
                initial={{ opacity: 0, x: -50 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.6, ease: "easeOut" }}
                className="hidden md:flex w-full md:w-5/12 lg:w-1/2 bg-[#0d1655] text-white p-12 flex-col justify-between relative overflow-hidden"
            >
                {/* Efek Latar Belakang */}
                <div className="absolute top-[-10%] left-[-10%] w-96 h-96 bg-[#F47920] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                <div className="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob" style={{ animationDelay: '2s' }}></div>

                <div className="relative z-10 flex items-center gap-3">
                    <div className="bg-white/10 p-3 rounded-2xl backdrop-blur-md border border-white/10 shadow-xl">
                        <Zap className="w-8 h-8 text-[#F47920]" fill="currentColor" />
                    </div>
                    <div>
                        <h1 className="text-2xl font-black tracking-tight">AKSES ARTHA MEDIA</h1>
                        <p className="text-blue-200 text-xs tracking-widest uppercase font-bold mt-1">Provider Internet Unggulan</p>
                    </div>
                </div>

                <div className="relative z-10 my-auto">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.3, duration: 0.6 }}
                    >
                        <h2 className="text-4xl lg:text-5xl font-black leading-tight mb-6">
                            Sistem <br/>Manajemen <span className="text-[#F47920]">Terpadu</span>
                        </h2>
                        <p className="text-blue-100/80 text-lg max-w-md leading-relaxed">
                            Akses kendali penuh untuk mengelola pelanggan, registrasi internet, coverage area, serta utilitas jaringan cerdas lainnya.
                        </p>
                    </motion.div>
                </div>

                <div className="relative z-10 text-sm text-blue-200/60 font-medium">
                    &copy; {new Date().getFullYear()} PT. Akses Artha Media. All rights reserved.
                </div>
            </motion.div>

            {/* Kanan: Form Login */}
            <motion.div 
                initial={{ opacity: 0, x: 50 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.6, ease: "easeOut", delay: 0.1 }}
                className="w-full md:w-7/12 lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-24 bg-white relative"
            >
                <div className="w-full max-w-md">
                    
                    {/* Header Khusus Mobile */}
                    <div className="md:hidden flex items-center gap-3 mb-10">
                        <div className="bg-[#0d1655] p-3 rounded-2xl shadow-xl">
                            <Zap className="w-6 h-6 text-[#F47920]" fill="currentColor" />
                        </div>
                        <div>
                            <h1 className="text-xl font-black text-[#0d1655] tracking-tight">AKSES ARTHA MEDIA</h1>
                            <p className="text-slate-400 text-[10px] tracking-widest uppercase font-bold mt-0.5">Management Portal</p>
                        </div>
                    </div>

                    <div className="mb-10">
                        <h2 className="text-3xl font-black text-slate-800 tracking-tight">Selamat Datang 👋</h2>
                        <p className="text-slate-500 mt-2 font-medium">Silakan masuk ke panel admin untuk melanjutkan.</p>
                    </div>

                    {status && (
                        <motion.div initial={{ opacity: 0, y: -10 }} animate={{ opacity: 1, y: 0 }} className="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-sm font-bold text-emerald-600 flex items-center gap-2">
                            <ShieldCheck className="w-5 h-5" />
                            {status}
                        </motion.div>
                    )}

                    <form onSubmit={submit} className="space-y-6">
                        {/* WhatsApp Input */}
                        <div className="space-y-2">
                            <label htmlFor="whatsapp" className="text-sm font-bold text-slate-700">Nomor WhatsApp / ID Akses</label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Phone className="h-5 w-5 text-slate-400" />
                                </div>
                                <TextInput
                                    id="whatsapp"
                                    type="text"
                                    name="whatsapp"
                                    value={data.whatsapp}
                                    className="block w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-200 text-slate-800 rounded-2xl focus:ring-[#F47920] focus:border-[#F47920] transition-all"
                                    autoComplete="username"
                                    isFocused={true}
                                    onChange={(e) => setData('whatsapp', e.target.value)}
                                    placeholder="Contoh: admin@armedia.id atau 0812345"
                                />
                            </div>
                            <InputError message={errors.whatsapp} className="mt-1" />
                        </div>

                        {/* Password Input */}
                        <div className="space-y-2">
                            <label htmlFor="password" className="text-sm font-bold text-slate-700">PIN Rahasia</label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Lock className="h-5 w-5 text-slate-400" />
                                </div>
                                <TextInput
                                    id="password"
                                    type="password"
                                    name="password"
                                    value={data.password}
                                    className="block w-full pl-12 pr-4 py-3.5 bg-slate-50 border-slate-200 text-slate-800 rounded-2xl focus:ring-[#F47920] focus:border-[#F47920] transition-all"
                                    autoComplete="current-password"
                                    onChange={(e) => setData('password', e.target.value)}
                                    placeholder="••••••••"
                                />
                            </div>
                            <InputError message={errors.password} className="mt-1" />
                        </div>

                        {/* Remember Me */}
                        <div className="flex items-center justify-between pt-2">
                            <label className="flex items-center cursor-pointer group">
                                <div className="relative flex items-center">
                                    <Checkbox
                                        name="remember"
                                        checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                        className="w-5 h-5 rounded-md border-slate-300 text-[#F47920] shadow-sm focus:ring-[#F47920] focus:ring-offset-0 transition-all cursor-pointer"
                                    />
                                </div>
                                <span className="ml-3 text-sm font-medium text-slate-600 group-hover:text-slate-800 transition-colors">
                                    Ingat saya
                                </span>
                            </label>
                        </div>

                        {/* Submit Button */}
                        <div className="pt-4">
                            <button
                                type="submit"
                                disabled={processing}
                                className={`w-full flex items-center justify-center gap-3 py-4 px-6 rounded-2xl text-white font-black uppercase tracking-widest text-sm transition-all duration-300 shadow-xl shadow-orange-500/20 
                                    ${processing ? 'bg-slate-400 cursor-not-allowed' : 'bg-[#F47920] hover:bg-[#d86617] hover:scale-[1.02]'}`}
                            >
                                {processing ? 'Memproses...' : 'Masuk Dasbor'}
                                {!processing && <ArrowRight className="w-5 h-5" />}
                            </button>
                        </div>
                    </form>

                </div>
            </motion.div>
        </div>
    );
}
