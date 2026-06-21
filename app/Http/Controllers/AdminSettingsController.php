<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternetPackage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminSettingsController extends Controller
{
    // Packages
    public function storePackage(Request $request)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'name' => 'required|string',
            'speed_mbps' => 'required|numeric',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        InternetPackage::create($validated);
        return redirect()->back()->with('success', 'Paket berhasil dibuat.');
    }

    public function updatePackage(Request $request, $id)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $pkg = InternetPackage::findOrFail($id);
        $pkg->update($request->all());
        return redirect()->back()->with('success', 'Paket berhasil diperbarui.');
    }

    public function deletePackage($id)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $pkg = InternetPackage::findOrFail($id);
        $pkg->delete();
        return redirect()->back()->with('success', 'Paket berhasil dihapus.');
    }

    // Villages (Dummy, as we don't have table yet)
    public function storeVillage(Request $request)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        return redirect()->back()->with('success', 'Desa berhasil ditambahkan (Dummy).');
    }

    public function updateVillage(Request $request, $id)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        return redirect()->back()->with('success', 'Desa berhasil diperbarui (Dummy).');
    }

    public function deleteVillage($id)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        return redirect()->back()->with('success', 'Desa berhasil dihapus (Dummy).');
    }

    // Users
    public function storeUser(Request $request)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return redirect()->back()->with('success', 'Admin berhasil ditambahkan.');
    }

    public function updateUser(Request $request, $id)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|string|min:8',
        ]);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->back()->with('success', 'Admin berhasil diperbarui.');
    }

    public function deleteUser($id)
    {
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus diri sendiri.');
        }
        $user->delete();
        return redirect()->back()->with('success', 'Admin berhasil dihapus.');
    }

    // Notifications
    public function markAllRead()
    {
        // Dummy
        return redirect()->back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    public function markRead($id)
    {
        // Dummy
        return redirect()->back()->with('success', 'Notifikasi ditandai dibaca.');
    }
}
