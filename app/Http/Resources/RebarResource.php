<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RebarResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => $this->kode,
            'nama_barang' => $this->nama_barang,
            'uty' => $this->uty,
            'harga_satuan' => $this->harga_satuan,
            'total_price' => $this->quantity * $this->unit_price,
            'stock_bahan' => $this->stock_bahan
        ];
    }
}
