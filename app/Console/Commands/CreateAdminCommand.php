<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature   = 'app:create-admin {--email=admin@armedia.id} {--name=Admin Armedia} {--password=}';
    protected $description = 'Buat atau reset akun Super Admin ARMEDIA. Jalankan via CLI saja, tidak tersedia via HTTP.';

    public function handle(): void
    {
        $email    = $this->option('email');
        $name     = $this->option('name');
        $password = $this->option('password');

        if (!$password) {
            $password = $this->secret('Masukkan password baru untuk akun admin (disembunyikan)');
        }

        if (!$password || strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');
            return;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => $name,
                'password' => Hash::make($password),
            ]
        );

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate([
                'name'       => 'super_admin',
                'guard_name' => 'web',
            ]);
            $user->syncRoles([$role]);
        }

        $this->info("✅ Akun admin [{$email}] berhasil dibuat/diperbarui dengan role super_admin.");
        $this->warn('⚠️  Jangan jalankan perintah ini di environment Production kecuali benar-benar diperlukan.');
    }
}
