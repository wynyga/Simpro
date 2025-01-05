<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Blok;
use App\Models\TipeRumah;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $units = Unit::whereHas('blok', function($query) use ($user) {
            $query->where('perumahan_id', $user->perumahan_id);
        })->with('blok', 'tipeRumah')->get();

        return response()->json($units);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        Log::info('Attempting to add unit with user data: ', ['user_id' => $user->id, 'perumahan_id' => $user->perumahan_id]);

        $validated = $request->validate([
            'blok_id' => 'required|exists:blok,id',
            'tipe_rumah_id' => 'required|exists:tipe_rumah,id',
            'nomor_unit' => 'required|string|max:255'
        ]);

        $blok = Blok::find($validated['blok_id']);
        if (!$blok || $blok->perumahan_id != $user->perumahan_id) {
            Log::error('Unauthorized access attempt to blok not in user perumahan.', ['blok_id' => $validated['blok_id'], 'user_perumahan_id' => $user->perumahan_id]);
            return response()->json(['error' => 'Unauthorized: You cannot add units to a blok from another perumahan.'], 403);
        }

        $unit = Unit::create($validated);
        Log::info('Unit created successfully.', ['unit_id' => $unit->id]);

        return response()->json(['message' => 'Unit successfully added', 'data' => $unit], 201);
    }

    public function show($id)
    {
        $user = auth()->user();
        $unit = Unit::whereHas('blok', function($query) use ($user) {
            $query->where('perumahan_id', $user->perumahan_id);
        })->with('blok', 'tipeRumah')->findOrFail($id);

        return response()->json($unit);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $unit = Unit::findOrFail($id);

        if ($unit->blok->perumahan_id != $user->perumahan_id) {
            return response()->json(['error' => 'Unauthorized: You cannot update units in another perumahan.'], 403);
        }

        $validated = $request->validate([
            'nomor_unit' => 'required|string|max:255'
        ]);

        $unit->update($validated);
        return response()->json(['message' => 'Unit updated successfully', 'data' => $unit], 200);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $unit = Unit::findOrFail($id);

        if ($unit->blok->perumahan_id != $user->perumahan_id) {
            return response()->json(['error' => 'Unauthorized: You cannot delete units in another perumahan.'], 403);
        }

        $unit->delete();
        return response()->json(['message' => 'Unit deleted successfully'], 204);
    }
}
