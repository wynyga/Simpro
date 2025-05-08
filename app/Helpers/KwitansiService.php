<?php

namespace App\Helpers;

use App\Models\Kwitansi;
use App\Models\Perumahan;

class KwitansiService
{
    public static function generateNoDoc(int $perumahanId, string $kodeJenis): string
    {
        $perumahan = Perumahan::findOrFail($perumahanId);

        $lastNo = Kwitansi::where('perumahan_id', $perumahanId)
            ->where('no_doc', 'like', '%/' . $kodeJenis . '-%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;

        if ($lastNo && preg_match('/^(\d{2})\/' . $kodeJenis . '/', $lastNo->no_doc, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        return sprintf(
            "%02d/%s-%s/%d",
            $nextNumber,
            $kodeJenis,
            $perumahan->inisial,
            now()->year
        );
    }
}
