<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:travel.airport.view')->only(['index', 'getData', 'list']);
        $this->middleware('permission:travel.airport.create')->only(['store']);
        $this->middleware('permission:travel.airport.update')->only(['update']);
        $this->middleware('permission:travel.airport.delete')->only(['destroy']);
    }

    public function index()
    {
        return view('admin.inventaris.airport.index');
    }

    public function getData(Request $request)
    {
        $query = Airport::query();
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('iata_code', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }
        $airports = $query->orderBy('iata_code')->get();
        return response()->json(['data' => $airports]);
    }

    public function list()
    {
        $airports = Airport::where('is_active', true)->orderBy('iata_code')
            ->get(['id', 'iata_code', 'name', 'city']);
        return response()->json($airports);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'iata_code' => 'required|string|max:10',
            'name' => 'required|string|max:150',
            'city' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);
        $airport = Airport::create($validated);
        return response()->json(['message' => 'Bandara berhasil ditambahkan', 'data' => $airport], 201);
    }

    public function show($id)
    {
        return response()->json(Airport::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $airport = Airport::findOrFail($id);
        $validated = $request->validate([
            'iata_code' => 'required|string|max:10',
            'name' => 'required|string|max:150',
            'city' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);
        $airport->update($validated);
        return response()->json(['message' => 'Bandara berhasil diupdate', 'data' => $airport]);
    }

    public function destroy($id)
    {
        Airport::findOrFail($id)->delete();
        return response()->json(['message' => 'Bandara berhasil dihapus']);
    }
}
