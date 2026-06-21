<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::query();

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $limit = $request->input('limit', 100);
        $customers = $query->orderBy('created_at', 'desc')->paginate($limit);

        // KPI Stats
        $totalCustomers = Registration::count();
        $activeCustomers = Registration::where('status', 'active')->orWhere('status', 'AKTIF')->count();
        $pendingCustomers = Registration::whereIn('status', ['pending', 'baru', 'PENGAJUAN'])->count();
        $monthlyRevenue = $activeCustomers * 150000; // Simplified

        $kpiStats = [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'pending_customers' => $pendingCustomers,
            'monthly_revenue' => $monthlyRevenue,
        ];

        $packages = \App\Models\InternetPackage::all();
        $users = \App\Models\User::all();
        $villages = [
            ['id' => 1, 'name' => 'GUMELAR'],
            ['id' => 2, 'name' => 'CIHONJE'],
            ['id' => 3, 'name' => 'TLAGA'],
            ['id' => 4, 'name' => 'SAMUDRA'],
            ['id' => 5, 'name' => 'SAMUDRA KULON'],
            ['id' => 6, 'name' => 'CILANGKAP'],
            ['id' => 7, 'name' => 'PANINGKABAN'],
            ['id' => 8, 'name' => 'KARANG KEMOJING'],
            ['id' => 9, 'name' => 'GANCANG'],
            ['id' => 10, 'name' => 'KEDUNG URANG'],
        ];

        return Inertia::render('Admin/Dashboard', [
            'userRole' => auth()->user()->roles->first()->name ?? 'admin',
            'user' => auth()->user(),
            'initialCustomers' => $customers,
            'initialKpiStats' => $kpiStats,
            'filters' => request()->all(['search', 'status']),
            'packages' => $packages,
            'villages' => $villages,
            'users' => $users,
            'notifications' => [],
            'unreadCount' => 0,
        ]);
    }

    public function createCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            // other validation rules...
        ]);

        $dbRecord = [
            'nama' => $request->name ?? '',
            'whatsapp' => $request->phone ?? '',
            'alamat' => $request->address ?? '',
            'kecamatan' => $request->kecamatan ?? 'GUMELAR',
            'desa' => $request->desa ?? '',
            'rw' => $request->rw ?? '',
            'rt' => $request->rt ?? '',
            'nik' => $request->nik ?? '',
            'paket' => $request->package_id ?? $request->paket ?? '', // Simplified, as we don't have package model connected yet fully
            'status' => $request->status ?? 'baru',
            'provider_saat_ini' => $request->current_provider ?? '',
            'sumber_info' => $request->source_info ?? '',
            'link_google_maps' => $request->link_google_maps ?? '',
            'foto_ktp' => $request->foto_ktp ?? '',
            'catatan' => $request->notes ?? '',
            'tanggal_pemasangan' => $request->tanggal_rencana_pasang ?? '',
        ];

        $reg = Registration::create($dbRecord);

        return redirect()->back()->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function updateCustomer(Request $request, $id)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $reg = Registration::findOrFail($id);
        
        $dbRecord = [
            'nama' => $request->name ?? $reg->nama,
            'whatsapp' => $request->phone ?? $reg->whatsapp,
            'alamat' => $request->address ?? $reg->alamat,
            'kecamatan' => $request->kecamatan ?? $reg->kecamatan,
            'desa' => $request->desa ?? $reg->desa,
            'rw' => $request->rw ?? $reg->rw,
            'rt' => $request->rt ?? $reg->rt,
            'nik' => $request->nik ?? $reg->nik,
            'paket' => $request->package_id ?? $request->paket ?? $reg->paket,
            'status' => $request->status ?? $reg->status,
            'provider_saat_ini' => $request->current_provider ?? $reg->provider_saat_ini,
            'sumber_info' => $request->source_info ?? $reg->sumber_info,
            'link_google_maps' => $request->link_google_maps ?? $reg->link_google_maps,
            'foto_ktp' => $request->foto_ktp ?? $reg->foto_ktp,
            'catatan' => $request->notes ?? $reg->catatan,
            'tanggal_pemasangan' => $request->tanggal_rencana_pasang ?? $reg->tanggal_pemasangan,
        ];

        $reg->update($dbRecord);

        return redirect()->back()->with('success', 'Pelanggan berhasil diperbarui');
    }

    public function deleteCustomer($id)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $reg = Registration::findOrFail($id);
        $reg->delete();
        return redirect()->back()->with('success', 'Pelanggan berhasil dihapus');
    }

    public function updateCustomerStatus(Request $request, $id)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate(['status' => 'required|string']);
        
        $reg = Registration::findOrFail($id);
        $reg->status = $request->status;
        
        if ($request->status === 'active') {
            $reg->tanggal_aktif = now();
        }
        
        $reg->save();

        return redirect()->back()->with('success', 'Status pelanggan diperbarui');
    }

    public function uploadKtp(Request $request)
    {
        $request->validate([
            'ktp' => 'required|image|max:10240', // 10MB
        ]);

        $path = $request->file('ktp')->store('public/ktp');
        return response()->json([
            'success' => true,
            'url' => \Illuminate\Support\Facades\Storage::url($path)
        ]);
    }
}
