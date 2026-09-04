<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAspirasiRequest;
use App\Services\AspirasiService;

class AspirasiController extends Controller
{
    public function __construct(protected AspirasiService $aspirasiService) {}

    public function store(StoreAspirasiRequest $request)
    {
        $aspirasi = $this->aspirasiService->create(
            $request->validated(),
            $request->file('foto')
        );

        return response()->json([
            'message' => 'Aspirasi berhasil diajukan',
            'nomor_tiket' => $aspirasi->id,
            'data' => $aspirasi,
        ], 201);
    }

    public function show(int $ticket)
    {
        $aspirasi = $this->aspirasiService->findByTicket($ticket);

        if (!$aspirasi) {
            return response()->json(['message' => 'Tiket tidak ditemukan'], 404);
        }

        return response()->json($aspirasi);
    }
}
