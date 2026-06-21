import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth }) {
    const member = auth.user;

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard Member ACR
                </h2>
            }
        >
            <Head title="Dashboard - ARMEDIA" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                    
                    {/* Welcome Card */}
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 flex items-center justify-between">
                            <div>
                                <h3 className="text-2xl font-bold">Halo, {member.nama}! 👋</h3>
                                <p className="text-sm text-slate-500 mt-1">ID Pelanggan: {member.id_pelanggan || member.whatsapp}</p>
                            </div>
                            <div className="text-right">
                                <p className="text-sm text-slate-500">Level Member</p>
                                <span className="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
                                    {member.level_member || 'Reguler'}
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* Points Card */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="overflow-hidden bg-gradient-to-br from-red-600 to-red-800 text-white shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <h4 className="text-lg font-medium opacity-90">Total Poin ACR</h4>
                                <div className="mt-2 flex items-baseline gap-2">
                                    <span className="text-5xl font-black">{member.total_poin || 0}</span>
                                    <span className="text-sm opacity-80">pts</span>
                                </div>
                                <p className="mt-4 text-sm opacity-80">Terus gunakan layanan ARMEDIA untuk mengumpulkan poin!</p>
                            </div>
                        </div>

                        {/* Quick Actions */}
                        <div className="md:col-span-2 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div className="p-6 h-full flex flex-col justify-center">
                                <h4 className="text-lg font-bold text-gray-900 mb-4">Akses Cepat</h4>
                                <div className="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                    <button className="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-all group">
                                        <div className="h-10 w-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                            🎁
                                        </div>
                                        <span className="text-sm font-semibold text-slate-700">Tukar Poin</span>
                                    </button>
                                    <button className="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-all group">
                                        <div className="h-10 w-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                            📜
                                        </div>
                                        <span className="text-sm font-semibold text-slate-700">Riwayat</span>
                                    </button>
                                    <button className="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 hover:border-red-500 hover:bg-red-50 transition-all group">
                                        <div className="h-10 w-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                            💬
                                        </div>
                                        <span className="text-sm font-semibold text-slate-700">Hubungi CS</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Catalog Section Preview */}
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h4 className="text-lg font-bold text-gray-900 mb-4">Katalog Hadiah Populer</h4>
                            <div className="text-center py-8 text-slate-500">
                                <span className="text-4xl mb-3 block">🛍️</span>
                                <p>Belum ada hadiah yang tersedia saat ini.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
