<?php

namespace App\Services;

use App\Models\Aspirasi;
use Illuminate\Support\Facades\Storage;

class AspirasiService
{
    public function create(array $data, $fotoFile = null): Aspirasi
    {
        if ($fotoFile) {
            $path = $fotoFile->store('aspirasi-foto', 'public');
            $data['foto_url'] = Storage::url($path);
        }

        return Aspirasi::create($data);
        // TODO nanti: dispatch job kirim WA notif (BAB 6.2) + AI kategorisasi (BAB 6.3)
    }

    public function findByTicket(int $id): ?Aspirasi
    {
        return Aspirasi::find($id);
    }
}