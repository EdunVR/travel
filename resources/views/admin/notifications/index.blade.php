@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Notifikasi
            <small>Kelola notifikasi Anda</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Notifikasi</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Semua Notifikasi</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-sm btn-default" onclick="markAllAsRead()">
                                <i class="fa fa-check"></i> Tandai Semua Dibaca
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        @if($notifications->count() > 0)
                            <div class="list-group">
                                @foreach($notifications as $notification)
                                    <a href="#" 
                                       class="list-group-item {{ !$notification->is_read ? 'active' : '' }}"
                                       onclick="markAsRead({{ $notification->id }}); return false;">
                                        <div class="row">
                                            <div class="col-md-1 text-center">
                                                <i class="fa {{ $notification->icon }} fa-2x text-{{ $notification->color }}"></i>
                                            </div>
                                            <div class="col-md-11">
                                                <h4 class="list-group-item-heading">
                                                    {{ $notification->title }}
                                                    @if(!$notification->is_read)
                                                        <span class="label label-warning pull-right">Baru</span>
                                                    @endif
                                                </h4>
                                                <p class="list-group-item-text">{{ $notification->message }}</p>
                                                <small class="text-muted">
                                                    <i class="fa fa-clock-o"></i> 
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            
                            <div class="text-center" style="margin-top: 20px;">
                                {{ $notifications->links() }}
                            </div>
                        @else
                            <div class="text-center" style="padding: 40px;">
                                <i class="fa fa-bell-o fa-4x text-muted"></i>
                                <p class="text-muted" style="margin-top: 20px;">Tidak ada notifikasi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`{{ url('notifications') }}/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('{{ route("notifications.read-all") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
