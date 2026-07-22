<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;

class RadiusService
{
    /**
     * Daftarkan atau update pelanggan di FreeRADIUS
     */
    public function createUser(string $username, string $password, string $profile): bool
    {
        try {
            DB::beginTransaction();

            // 1. Simpan Cleartext-Password ke radcheck
            DB::table('radcheck')->updateOrInsert(
                ['username' => $username, 'attribute' => 'Cleartext-Password'],
                ['op' => ':=', 'value' => $password]
            );

            // 2. Simpan profil/paket ke radusergroup
            DB::table('radusergroup')->updateOrInsert(
                ['username' => $username],
                ['groupname' => $profile, 'priority' => 1]
            );

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("RadiusService createUser Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ganti paket pelanggan di FreeRADIUS
     */
    public function changeProfile(string $username, string $newProfile): bool
    {
        try {
            DB::table('radusergroup')
                ->where('username', $username)
                ->update(['groupname' => $newProfile]);
            return true;
        } catch (\Exception $e) {
            Log::error("RadiusService changeProfile Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Masukkan pelanggan ke profil Isolir
     */
    public function suspendUser(string $username): bool
    {
        return $this->changeProfile($username, 'ISOLIR');
    }

    /**
     * Hapus pelanggan dari FreeRADIUS
     */
    public function deleteUser(string $username): bool
    {
        try {
            DB::beginTransaction();
            DB::table('radcheck')->where('username', $username)->delete();
            DB::table('radreply')->where('username', $username)->delete();
            DB::table('radusergroup')->where('username', $username)->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("RadiusService deleteUser Error: " . $e->getMessage());
            return false;
        }
    }
}
