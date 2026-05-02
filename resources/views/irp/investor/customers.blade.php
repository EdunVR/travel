@extends('app')

@section('title', 'Kelola Customer Investor')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Customer Investor: {{ $investor->name }}</h6>
            <a href="{{ route('irp.investor.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <form action="{{ route('irp.investor.customer.store', $investor->id) }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6">
                                <select name="id_member" class="form-control" required>
                                    <option value="">Pilih Customer</option>
                                    @foreach($availableMembers as $member)
                                        <option value="{{ $member->id_member }}">{{ $member->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="biaya" class="form-control" placeholder="Biaya" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-right">
                    <div class="alert alert-info">
                        <strong>Status:</strong> 
                        {{ $investor->customers->where('status', 'paid')->count() }}/{{ $investor->kuota }} kursi terisi
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Customer</th>
                            <th>Telepon</th>
                            <th>Biaya</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $customer->member->nama }}</td>
                            <td>{{ $customer->member->telepon }}</td>
                            <td class="text-right">{{ format_uang($customer->biaya) }}</td>
                            <td>
                                <span class="badge badge-{{ $customer->status == 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td>{{ $customer->payment_date ? tanggal_indonesia($customer->payment_date) : '-' }}</td>
                            <td>
                                @if($customer->status != 'paid')
                                <form action="{{ route('irp.investor.customer.verify', [$investor->id, $customer->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Verifikasi pembayaran?')">
                                        <i class="fas fa-check"></i> Verifikasi
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('irp.investor.customer.destroy', [$investor->id, $customer->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus customer ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
