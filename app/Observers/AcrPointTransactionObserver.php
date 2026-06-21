<?php

namespace App\Observers;

use App\Models\AcrPointTransaction;
use App\Models\AcrMember;

class AcrPointTransactionObserver
{
    /**
     * Handle the AcrPointTransaction "created" event.
     */
    public function created(AcrPointTransaction $transaction): void
    {
        $member = AcrMember::find($transaction->id_member);
        
        if ($member) {
            // Update total points
            if ($transaction->jenis === 'MASUK') {
                $member->total_poin += $transaction->jumlah_poin;
            } else if ($transaction->jenis === 'KELUAR') {
                $member->total_poin -= $transaction->jumlah_poin;
            }

            // Define standard thresholds
            // Reguler: 0 - 99
            // Silver: 100 - 499
            // Gold: 500 - 999
            // Platinum: >= 1000
            if ($member->total_poin >= 1000) {
                $member->level_member = 'Platinum';
            } elseif ($member->total_poin >= 500) {
                $member->level_member = 'Gold';
            } elseif ($member->total_poin >= 100) {
                $member->level_member = 'Silver';
            } else {
                $member->level_member = 'Reguler';
            }

            $member->save();
        }
    }

    /**
     * Handle the AcrPointTransaction "deleted" event.
     */
    public function deleted(AcrPointTransaction $transaction): void
    {
        $member = AcrMember::find($transaction->id_member);
        
        if ($member) {
            // Reverse the effect
            if ($transaction->jenis === 'MASUK') {
                $member->total_poin -= $transaction->jumlah_poin;
            } else if ($transaction->jenis === 'KELUAR') {
                $member->total_poin += $transaction->jumlah_poin;
            }

            // Re-evaluate level
            if ($member->total_poin >= 1000) {
                $member->level_member = 'Platinum';
            } elseif ($member->total_poin >= 500) {
                $member->level_member = 'Gold';
            } elseif ($member->total_poin >= 100) {
                $member->level_member = 'Silver';
            } else {
                $member->level_member = 'Reguler';
            }

            $member->save();
        }
    }
}
