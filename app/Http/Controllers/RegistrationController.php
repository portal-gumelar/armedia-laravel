<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia\Inertia::render('RegisterWifi', [
            'villages' => [],
            'packages' => []
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return \Inertia\Inertia::render('RegisterWifi');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'village_id' => 'nullable|integer',
            'package_id' => 'nullable|integer',
            'notes' => 'nullable|string',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
            'nik' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'desa' => 'nullable|string',
            'paket' => 'nullable|string',
            'provider_saat_ini' => 'nullable|string',
            'sumber_info' => 'nullable|string',
            'link_google_maps' => 'nullable|string',
            'foto_ktp' => 'nullable|string',
            'tanggal_pemasangan' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        // Map names to db columns
        $dbRecord = [
            'nama' => $request->name ?? $request->nama_lengkap ?? '',
            'whatsapp' => $request->phone ?? $request->no_hp_wa ?? '',
            'alamat' => $request->address ?? $request->alamat_pemasangan ?? '',
            'kecamatan' => $request->kecamatan ?? 'GUMELAR',
            'desa' => $request->desa ?? '',
            'rw' => $request->rw ?? '',
            'rt' => $request->rt ?? '',
            'nik' => $request->nik ?? '',
            'paket' => $request->paket ?? '',
            'status' => $request->status ?? 'baru',
            'provider_saat_ini' => $request->provider_saat_ini ?? '',
            'sumber_info' => $request->sumber_info ?? '',
            'link_google_maps' => $request->link_google_maps ?? '',
            'foto_ktp' => $request->foto_ktp ?? '',
            'catatan' => $request->notes ?? $request->catatan ?? '',
            'tanggal_pemasangan' => $request->tanggal_rencana_pasang ?? $request->tanggal_pemasangan ?? '',
            'referral_id_arm' => $request->ref ?? null,
        ];

        $reg = Registration::create($dbRecord);

        return redirect()->back()->with('success', 'Pendaftaran Berhasil! Kami akan segera menghubungi Anda.');
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('ktp', 'public');
            return response()->json(['url' => '/storage/' . $path]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
