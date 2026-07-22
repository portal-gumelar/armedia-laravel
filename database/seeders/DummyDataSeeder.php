<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mitra;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\InternetPackage;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Ticket;
use App\Models\InstallationTask;
use App\Models\Radius\RadCheck;
use App\Models\Payroll;
use App\Enums\InvoiceStatus;
use App\Enums\TicketStatus;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Create Mitra (2 records)
        $mitra1 = Mitra::firstOrCreate(
            ['kode_mitra' => 'MTR-001'],
            [
                'nama_mitra' => 'Mitra Gemilang Timur',
                'email' => 'gemilang@mitra.armedia.co.id',
                'whatsapp' => '08111222333',
                'alamat' => 'Jl. Timur Raya No. 10',
                'status' => 'aktif',
            ]
        );
        
        $mitra2 = Mitra::firstOrCreate(
            ['kode_mitra' => 'MTR-002'],
            [
                'nama_mitra' => 'Mitra Cepat Barat',
                'email' => 'cepat@mitra.armedia.co.id',
                'whatsapp' => '08111222444',
                'alamat' => 'Jl. Barat Indah No. 8',
                'status' => 'aktif',
            ]
        );

        // 2. Create Internet Packages
        $packages = [];
        $packageSpeeds = [
            ['name' => 'Paket Bronze 20Mbps', 'price' => 150000, 'speed' => 20],
            ['name' => 'Paket Silver 30Mbps', 'price' => 250000, 'speed' => 30],
            ['name' => 'Paket Gold 50Mbps', 'price' => 350000, 'speed' => 50],
        ];

        foreach ($packageSpeeds as $pkg) {
            $packages[] = InternetPackage::firstOrCreate(
                ['nama_paket' => $pkg['name']],
                [
                    'harga' => $pkg['price'],
                    'kecepatan' => $pkg['speed'],
                    'speed_mbps' => $pkg['speed'],
                    'is_active' => true,
                    'keterangan_promo' => 'Kecepatan hingga ' . $pkg['speed'] . ' Mbps'
                ]
            );
        }

        // 3. Create Employees (Users + HRM)
        $roles = [
            ['name' => 'Budi Teknisi', 'role' => 'teknisi', 'email' => 'budi.tek@armedia.co.id'],
            ['name' => 'Agus Teknisi', 'role' => 'teknisi', 'email' => 'agus.tek@armedia.co.id'],
            ['name' => 'Siti CS', 'role' => 'cs', 'email' => 'siti.cs@armedia.co.id'],
            ['name' => 'Rina Finance', 'role' => 'finance', 'email' => 'rina.fin@armedia.co.id'],
            ['name' => 'Pak Manager', 'role' => 'admin', 'email' => 'manager@armedia.co.id'],
        ];

        $employees = [];
        foreach ($roles as $index => $r) {
            $user = User::firstOrCreate(
                ['email' => $r['email']],
                [
                    'name' => $r['name'],
                    'password' => Hash::make('password'),
                ]
            );

            // Add HRM Employee Profile
            $employee = Employee::create([
                'name' => $r['name'],
                'email' => $r['email'],
                'position' => strtoupper($r['role']),
                'division' => $r['role'] === 'teknisi' ? 'NOC' : ($r['role'] === 'finance' ? 'Finance' : 'Operations'),
                'basic_salary' => $faker->numberBetween(3000000, 7000000),
                'join_date' => Carbon::now()->subMonths(rand(6, 24)),
                'status' => 'active',
            ]);
            $employee->user_id = $user->id; // Store temporarily for ticket assignment
            $employees[] = $employee;

            // Generate dummy Payroll for last month
            Payroll::create([
                'employee_id' => $employee->id,
                'period' => Carbon::now()->subMonth()->startOfMonth(),
                'basic_salary' => $employee->basic_salary,
                'allowance' => $faker->numberBetween(200000, 500000),
                'deduction' => 0,
                'status' => 'paid',
                'paid_at' => Carbon::now()->subMonth()->endOfMonth(),
            ]);
        }

        // 4. Create 20 Customers
        $customers = [];
        for ($i = 1; $i <= 20; $i++) {
            $status = $faker->randomElement(['aktif', 'aktif', 'aktif', 'aktif', 'berhenti', 'isolir']);
            $mitra = $i % 5 === 0 ? $mitra1->id : ($i % 6 === 0 ? $mitra2->id : null);
            $package = $faker->randomElement($packages);

            $customer = Customer::create([
                'mitra_id' => $mitra,
                'name' => $faker->name,
                'whatsapp' => '081' . $faker->numerify('#########'),
                'password' => Hash::make('password'), // For member login
                'alamat' => $faker->address,
                'nik' => $faker->numerify('327#############'),
                'subscription_status' => $status,
                'internet_package_id' => $package->id,
            ]);
            $customers[] = $customer;

            // PPPoE Username for RadCheck
            if ($status === 'aktif') {
                $pppoeUser = 'user' . $customer->id . '@armedia';
                $customer->update(['pppoe_username' => $pppoeUser]);

                RadCheck::create([
                    'username' => $pppoeUser,
                    'attribute' => 'Cleartext-Password',
                    'op' => ':=',
                    'value' => 'pass' . $customer->id,
                ]);
            }

            // Invoices: 1 for last month (LUNAS), 1 for this month (BELUM/LUNAS depending on status)
            // Last month
            Invoice::create([
                'customer_id' => $customer->id,
                'mitra_id' => $mitra,
                'invoice_no' => 'INV-' . Carbon::now()->subMonth()->format('Ym') . '-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
                'period' => Carbon::now()->subMonth()->startOfMonth(),
                'amount' => $package->harga,
                'status' => InvoiceStatus::LUNAS,
                'paid_at' => Carbon::now()->subMonth()->addDays(rand(1, 15)),
                'payment_method' => 'Transfer Bank',
                'due_date' => Carbon::now()->subMonth()->startOfMonth()->addDays(10), // Tambahkan due_date agar tidak error
            ]);

            // This month
            $thisMonthStatus = $status === 'isolir' ? InvoiceStatus::BELUM : ($status === 'aktif' ? InvoiceStatus::LUNAS : InvoiceStatus::BELUM);
            Invoice::create([
                'customer_id' => $customer->id,
                'mitra_id' => $mitra,
                'invoice_no' => 'INV-' . Carbon::now()->format('Ym') . '-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
                'period' => Carbon::now()->startOfMonth(),
                'amount' => $package->harga,
                'status' => $thisMonthStatus,
                'paid_at' => $thisMonthStatus === InvoiceStatus::LUNAS ? Carbon::now()->addDays(rand(-5, 0)) : null,
                'payment_method' => $thisMonthStatus === InvoiceStatus::LUNAS ? 'Transfer Bank' : null,
                'due_date' => Carbon::now()->startOfMonth()->addDays(20), // Jatuh tempo tgl 20
            ]);
        }

        // 5. Generate Expenses for Finance
        $expenseCategories = ['Operasional', 'Pembelian Alat', 'Bensin', 'Listrik', 'Lainnya'];
        for ($i = 0; $i < 10; $i++) {
            Expense::create([
                'category' => $faker->randomElement($expenseCategories),
                'description' => 'Pengeluaran rutin ' . $faker->words(3, true),
                'amount' => $faker->numberBetween(50000, 1500000),
                'expense_date' => Carbon::now()->subDays(rand(1, 30)),
                'notes' => 'Generated by seeder',
                'payment_method' => 'Kas Kecil',
            ]);
        }

        // 6. Generate Tickets
        for ($i = 0; $i < 5; $i++) {
            $customer = $faker->randomElement($customers);
            $hasTicket = $faker->boolean(40);
            if ($hasTicket) {
                Ticket::create([
                    'customer_id' => $customer->id,
                    'ticket_no' => 'TKT-' . Carbon::now()->format('Ymd') . '-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
                    'category' => $faker->randomElement(['internet_mati', 'lambat', 'wifi_masalah', 'lainnya']),
                    'status' => $faker->randomElement(['open', 'process', 'resolved', 'closed']),
                    'priority' => $faker->randomElement(['low', 'medium', 'high', 'urgent']),
                    'subject' => $faker->sentence(4),
                    'description' => $faker->paragraph(),
                    'resolution_notes' => 'Sudah diperbaiki teknisi',
                    'assigned_to' => $employees[rand(0, 1)]->user_id, // assign to teknisi 1 or 2
                ]);
            }
        }

        // 7. Generate Installation Tasks
        $taskStatuses = ['survey', 'tarik_kabel', 'aktivasi', 'selesai'];
        for ($i = 0; $i < 3; $i++) {
            $customer = $faker->randomElement($customers);
            InstallationTask::create([
                'customer_id' => $customer->id,
                'assigned_to' => $employees[rand(0, 1)]->user_id,
                'status' => $faker->randomElement($taskStatuses),
                'scheduled_date' => Carbon::now()->addDays(rand(-2, 5)),
                'notes' => 'Pasang baru area ' . $faker->city,
            ]);
        }

        echo "✅ Dummy Data Seeder completed! Populated Mitra, Packages, Employees, Customers, Invoices, Expenses, Tickets, and Installation Tasks.\n";
    }
}
