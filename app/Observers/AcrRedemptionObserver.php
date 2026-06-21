<?php

namespace App\Observers;

use App\Models\AcrRedemption;
use App\Models\AcrRewardsCatalog;
use App\Models\AcrPointTransaction;

class AcrRedemptionObserver
{
    /**
     * Handle the AcrRedemption "created" event.
     */
    public function created(AcrRedemption $redemption): void
    {
        // When a redemption is requested/created, we deduct the points immediately
        // and reduce the stock
        $reward = AcrRewardsCatalog::find($redemption->id_hadiah);
        
        if ($reward) {
            // Deduct stock
            $reward->stok -= 1;
            $reward->save();

            // Create Point Transaction (KELUAR)
            AcrPointTransaction::create([
                'id_member' => $redemption->id_member,
                'jenis' => 'KELUAR',
                'jumlah_poin' => $reward->poin_dibutuhkan,
                'keterangan' => 'Penukaran Hadiah: ' . $reward->nama_hadiah
            ]);
        }
    }
}
