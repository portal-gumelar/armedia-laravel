<?php

namespace App\Services;

use App\Enums\MonitoringStatus;
use App\Models\Customer;
use App\Models\NetwatchLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NetwatchMatchingService
{
    /**
     * Proses array baris dari export Netwatch Mikrotik.
     * Format baris yang diharapkan: ['ip' => '10.x.x.x', 'status' => 'up|down', 'time' => '...']
     *
     * @param  array  $rows      Array baris data netwatch
     * @param  string $checkedAt Timestamp cek (default: now)
     * @return array  Statistik: matched, unmatched, total
     */
    public function processLog(array $rows, ?string $checkedAt = null): array
    {
        $checkedAt = $checkedAt ? Carbon::parse($checkedAt) : now();
        $stats = ['matched' => 0, 'unmatched' => 0, 'total' => count($rows)];

        // Ambil semua customer yang punya ip_address sekaligus (satu query)
        $customersByIp = Customer::whereNotNull('ip_address')
            ->get()
            ->keyBy('ip_address');

        DB::transaction(function () use ($rows, $customersByIp, $checkedAt, &$stats) {
            foreach ($rows as $row) {
                $ip     = trim($row['ip'] ?? '');
                $status = strtolower(trim($row['status'] ?? 'down'));

                if (empty($ip)) {
                    continue;
                }

                $customer = $customersByIp->get($ip);

                // Tulis log (customer_id nullable — aman walau tidak cocok)
                NetwatchLog::create([
                    'customer_id' => $customer?->id,
                    'ip_address'  => $ip,
                    'status'      => $status,
                    'checked_at'  => $checkedAt,
                ]);

                if ($customer) {
                    // Update status monitoring di customer
                    $customer->update([
                        'monitoring_status'    => $status === 'up' ? MonitoringStatus::UP->value : MonitoringStatus::DOWN->value,
                        'monitoring_checked_at' => $checkedAt,
                    ]);
                    $stats['matched']++;
                } else {
                    $stats['unmatched']++;
                }
            }
        });

        return $stats;
    }

    /**
     * Parse format CSV teks Netwatch ke array baris.
     * Format: "10.1.1.1,up" atau "10.1.1.1 up" per baris.
     */
    public function parseCsvText(string $text): array
    {
        $rows  = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($text));

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Support koma atau spasi sebagai delimiter
            $parts = preg_split('/[,\s]+/', $line, 3);
            if (count($parts) >= 2) {
                $rows[] = [
                    'ip'     => trim($parts[0]),
                    'status' => strtolower(trim($parts[1])),
                ];
            }
        }

        return $rows;
    }
}
