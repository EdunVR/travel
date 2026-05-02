<x-layouts.admin>
    <x-slot name="title">User Management</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">User Management</h1>
                <p class="text-slate-600 text-sm">Kelola pengguna sistem dan hak akses</p>
            </div>

            <button onclick="openUserModal()" 
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 h-10 hover:bg-primary-700">
                <i class='bx bx-plus'></i> Tambah User
            </button>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Total Users</p>
                        <p class="text-2xl font-bold">{{ $users->count() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class='bx bx-user text-2xl text-blue-600'></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Active Users</p>
                        <p class="text-2xl font-bold text-green-600">{{ $users->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                        <i class='bx bx-check-circle text-2xl text-green-600'></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Inactive Users</p>
                        <p class="text-2xl font-bold text-red-600">{{ $users->where('is_active', false)->count() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                        <i class='bx bx-x-circle text-2xl text-red-600'></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-600">Total Roles</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $roles->count() }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                        <i class='bx bx-shield text-2xl text-purple-600'></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="usersTable">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Outlet</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Last Login</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-primary-700 font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $user->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $user->phone ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $user->role->display_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                @if($user->outlets->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->outlets->take(2) as $outlet)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs bg-slate-100 text-slate-700">
                                            {{ $outlet->nama_outlet }}
                                        </span>
                                        @endforeach
                                        @if($user->outlets->count() > 2)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs bg-slate-200 text-slate-600">
                                            +{{ $user->outlets->count() - 2 }}
                                        </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="editUser({{ $user->id }})" 
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                        <i class='bx bx-edit text-lg'></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <button onclick="deleteUser({{ $user->id }})" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                        <i class='bx bx-trash text-lg'></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.user-management.users.modal')

    @push('scripts')
    <script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Handle signature file preview
        $('#signature').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#signatureImage').attr('src', e.target.result);
                    $('#signaturePreview').removeClass('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                $('#signaturePreview').addClass('hidden');
            }
        });
    });

    function openUserModal(userId = null) {
        $('#userModal').modal('show');
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#modalTitle').text('Tambah User');
        $('#passwordNote').show();
        $('#signaturePreview').addClass('hidden');
        
        if (userId) {
            loadUserData(userId);
        }
    }

    function editUser(userId) {
        openUserModal(userId);
    }

    function loadUserData(userId) {
        $.get(`{{ route('admin.users.show', '') }}/${userId}`, function(response) {
            if (response.success) {
                const user = response.data;
                $('#userId').val(user.id);
                $('#name').val(user.name);
                $('#email').val(user.email);
                $('#phone').val(user.phone);
                $('#role_id').val(user.role_id);
                $('#is_active').prop('checked', user.is_active);
                $('#modalTitle').text('Edit User');
                $('#passwordNote').text('(Kosongkan jika tidak diubah)');
                
                // Show existing signature if available
                if (user.signature_url) {
                    $('#signatureImage').attr('src', user.signature_url);
                    $('#signaturePreview').removeClass('hidden');
                }
                
                // Load outlet access
                $('.outlet-checkbox').prop('checked', false);
                if (user.outlet_ids && user.outlet_ids.length > 0) {
                    user.outlet_ids.forEach(outletId => {
                        $(`#outlet_${outletId}`).prop('checked', true);
                    });
                }
            }
        });
    }

    function deleteUser(userId) {
        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            $.ajax({
                url: `{{ route('admin.users.destroy', '') }}/${userId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            });
        }
    }

    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        const userId = $('#userId').val();
        const url = userId ? `{{ route('admin.users.update', '') }}/${userId}` : '{{ route('admin.users.store') }}';
        const method = userId ? 'PUT' : 'POST';
        
        // Create FormData for file upload
        const formData = new FormData(this);
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMsg = '';
                    Object.keys(errors).forEach(key => {
                        errorMsg += errors[key][0] + '\n';
                    });
                    alert(errorMsg);
                } else {
                    alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });
    </script>
    @endpush
</x-layouts.admin>
