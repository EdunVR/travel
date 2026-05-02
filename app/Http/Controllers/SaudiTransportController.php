<?php

namespace App\Http\Controllers;

use App\Models\SaudiTransport;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SaudiTransportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:travel.transport.view')->only(['index', 'getData']);
        $this->middleware('permission:travel.transport.create')->only(['store']);
        $this->middleware('permission:travel.transport.update')->only(['update']);
        $this->middleware('permission:travel.transport.delete')->only(['destroy']);
    }

    public function index()
    {
        $user = Auth::user();
        $outlets = $user && method_exists($user, 'outlets') ? $user->outlets : Outlet::all();
        if ($outlets->isEmpty()) $outlets = Outlet::all();
        return view('admin.inventaris.transport.index', compact('outlets'));
    }

    public function getData(Request $request)
    {
        $query = SaudiTransport::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('transport_name', 'like', "%$s%")
                  ->orWhere('transport_code', 'like', "%$s%")
                  ->orWhere('operator', 'like', "%$s%");
            });
        }
        if ($request->filled('type_filter') && $request->type_filter !== 'ALL') {
            $query->where('transport_type', $request->type_filter);
        }
        if ($request->filled('outlet_filter') && $request->outlet_filter !== 'ALL') {
            $query->where('id_outlet', $request->outlet_filter);
        }

        $typeLabels = [
            'kereta_cepat' => 'Kereta Cepat (Haramain)',
            'bus' => 'Bus',
            'lainnya' => 'Lainnya',
        ];

        $data = $query->orderBy('transport_name')->get()->map(function($t) use ($typeLabels) {
            return [
                'id' => $t->id,
                'transport_code' => $t->transport_code,
                'transport_name' => $t->transport_name,
                'transport_type' => $t->transport_type,
                'type_label' => $typeLabels[$t->transport_type] ?? $t->transport_type,
                'route' => trim(($t->route_from ?? '') . ' → ' . ($t->route_to ?? ''), ' → '),
                'operator' => $t->operator ?? '-',
                'price_per_person' => $t->price_per_person,
                'price_formatted' => 'Rp ' . number_format($t->price_per_person, 0, ',', '.'),
                'seller_name' => $t->seller_name ?? '-',
                'seller_phone' => $t->seller_phone ?? '-',
                'notes' => $t->notes,
                'id_outlet' => $t->id_outlet,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transport_name' => 'required|string|max:255',
            'transport_type' => 'required|in:kereta_cepat,bus,lainnya',
            'route_from' => 'nullable|string|max:255',
            'route_to' => 'nullable|string|max:255',
            'operator' => 'nullable|string|max:255',
            'price_per_person' => 'nullable|numeric|min:0',
            'seller_name' => 'nullable|string|max:255',
            'seller_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'id_outlet' => 'nullable|exists:outlets,id_outlet',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prefix = strtoupper(substr($request->transport_type, 0, 3));
        $code = $prefix . '-' . now()->format('Ymd') . '-' . str_pad(SaudiTransport::count() + 1, 4, '0', STR_PAD_LEFT);

        $transport = SaudiTransport::create(array_merge($request->all(), ['transport_code' => $code]));

        return response()->json(['message' => 'Data berhasil disimpan', 'data' => $transport], 200);
    }

    public function show($id)
    {
        $t = SaudiTransport::findOrFail($id);
        return response()->json($t);
    }

    public function update(Request $request, $id)
    {
        $t = SaudiTransport::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'transport_name' => 'required|string|max:255',
            'transport_type' => 'required|in:kereta_cepat,bus,lainnya',
            'route_from' => 'nullable|string|max:255',
            'route_to' => 'nullable|string|max:255',
            'operator' => 'nullable|string|max:255',
            'price_per_person' => 'nullable|numeric|min:0',
            'seller_name' => 'nullable|string|max:255',
            'seller_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'id_outlet' => 'nullable|exists:outlets,id_outlet',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $t->update($request->all());
        return response()->json(['message' => 'Data berhasil diupdate', 'data' => $t], 200);
    }

    public function destroy($id)
    {
        SaudiTransport::findOrFail($id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }
}
