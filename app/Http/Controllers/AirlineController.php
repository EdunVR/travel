<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    public function index()
    {
        return view('admin.inventaris.airline.index');
    }

    public function getData(Request $request)
    {
        $query = Airline::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('iata_code', 'like', '%' . $request->search . '%');
        }
        $airlines = $query->orderBy('name')->get();
        return response()->json(['data' => $airlines]);
    }

    public function list()
    {
        $airlines = Airline::where('is_active', true)->orderBy('name')->get(['id', 'name', 'iata_code']);
        return response()->json($airlines);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'iata_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
        ]);
        $airline = Airline::create($validated);
        return response()->json(['message' => 'Maskapai berhasil ditambahkan', 'data' => $airline], 201);
    }

    public function show($id)
    {
        return response()->json(Airline::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $airline = Airline::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'iata_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);
        $airline->update($validated);
        return response()->json(['message' => 'Maskapai berhasil diupdate', 'data' => $airline]);
    }

    public function destroy($id)
    {
        Airline::findOrFail($id)->delete();
        return response()->json(['message' => 'Maskapai berhasil dihapus']);
    }
}
