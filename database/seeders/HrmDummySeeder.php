<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\Carbon;
use Faker\Factory as Faker;

class HrmDummySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->command->info('Tidak ada data Karyawan. Silakan buat Karyawan dummy terlebih dahulu.');
            return;
        }

        $this->command->info('Mulai generate data Attendances dan Leaves dummy...');

        foreach ($employees as $employee) {
            // Generate 20 data absensi bulan ini
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now();

            for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
                // Lewati hari Minggu
                if ($date->isSunday()) {
                    continue;
                }

                // Random absensi (80% hadir, 10% telat, 10% izin/alpha)
                $rand = rand(1, 100);
                
                if ($rand <= 80) {
                    $status = 'hadir';
                    $checkIn = clone $date;
                    $checkIn->setTime(rand(7, 8), rand(0, 59), 0);
                    $checkOut = clone $date;
                    $checkOut->setTime(rand(16, 18), rand(0, 30), 0);
                } elseif ($rand <= 90) {
                    $status = 'terlambat';
                    $checkIn = clone $date;
                    $checkIn->setTime(rand(9, 10), rand(0, 59), 0);
                    $checkOut = clone $date;
                    $checkOut->setTime(rand(16, 18), rand(0, 30), 0);
                } else {
                    $status = ['izin', 'sakit', 'alpha'][array_rand(['izin', 'sakit', 'alpha'])];
                    $checkIn = null;
                    $checkOut = null;
                }

                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => $date->format('Y-m-d'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
                    'notes' => $status === 'hadir' ? '' : $faker->sentence(3),
                ]);
            }

            // Generate 1-2 data Cuti (Leaves)
            $leaveCount = rand(0, 2);
            for ($i = 0; $i < $leaveCount; $i++) {
                $leaveStart = Carbon::now()->subDays(rand(1, 60));
                $leaveEnd = (clone $leaveStart)->addDays(rand(1, 3));
                
                Leave::create([
                    'employee_id' => $employee->id,
                    'type' => ['tahunan', 'sakit', 'melahirkan', 'penting'][array_rand(['tahunan', 'sakit', 'melahirkan', 'penting'])],
                    'start_date' => $leaveStart->format('Y-m-d'),
                    'end_date' => $leaveEnd->format('Y-m-d'),
                    'days_count' => $leaveStart->diffInDays($leaveEnd) + 1,
                    'reason' => $faker->sentence(6),
                    'status' => ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])],
                ]);
            }
        }

        $this->command->info('Berhasil generate data HRM (Absensi & Cuti)!');
    }
}
