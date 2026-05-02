<x-layouts.admin :title="'Travel / Customer Communication'">
  <div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h3 mb-0">Customer Communication</h1>
            <p class="text-muted">Kelola komunikasi dan follow-up dengan jamaah</p>
          </div>
          <div>
            @hasPermission('travel.communication.view')
            <button type="button" class="btn btn-warning mr-2" id="btn-view-followups">
              <i class="fas fa-clock"></i> Follow-up Pending
            </button>
            @endhasPermission
            @hasPermission('travel.communication.create')
            <button type="button" class="btn btn-primary" id="btn-create-communication">
              <i class="fas fa-plus-circle"></i> Log Komunikasi
            </button>
            @endhasPermission
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>Jamaah</label>
              <select class="form-control select2" id="filter-member">
                <option value="">Semua Jamaah</option>
                @foreach($members as $member)
                <option value="{{ $member->id_member }}">{{ $member->nama }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Paket</label>
              <select class="form-control select2" id="filter-package">
                <option value="">Semua Paket</option>
                @foreach($packages as $package)
                <option value="{{ $package->id }}">{{ $package->package_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>Metode</label>
              <select class="form-control" id="filter-method">
                <option value="">Semua Metode</option>
                <option value="phone_call">Phone Call</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="email">Email</option>
                <option value="in_person">In Person</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>Status</label>
              <select class="form-control" id="filter-status">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="contacted">Contacted</option>
                <option value="responded">Responded</option>
                <option value="no_response">No Response</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="button" class="btn btn-secondary btn-block" id="btn-reset-filter">
                <i class="fas fa-redo"></i> Reset
              </button>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>Dari Tanggal</label>
              <input type="date" class="form-control" id="filter-date-from">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Sampai Tanggal</label>
              <input type="date" class="form-control" id="filter-date-to">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Communications Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="communications-table">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Jamaah</th>
                <th>Paket</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Follow-up</th>
                <th>Dihubungi Oleh</th>
                <th>Aksi</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Create/Edit Communication Modal -->
  <div class="modal fade" id="communication-modal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="margin-top: 2rem;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="communication-modal-title">Log Komunikasi</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <form id="communication-form">
          <div class="modal-body">
            <input type="hidden" id="communication-id">
            
            <div class="form-group">
              <label>Jamaah <span class="text-danger">*</span></label>
              <select class="form-control select2" id="id_member" name="id_member" required>
                <option value="">Pilih Jamaah</option>
                @foreach($members as $member)
                <option value="{{ $member->id_member }}">{{ $member->nama }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label>Paket (Opsional)</label>
              <select class="form-control select2" id="id_travel_package" name="id_travel_package">
                <option value="">Pilih Paket</option>
                @foreach($packages as $package)
                <option value="{{ $package->id }}">{{ $package->package_name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label>Metode Komunikasi <span class="text-danger">*</span></label>
              <select class="form-control" id="communication_method" name="communication_method" required>
                <option value="">Pilih Metode</option>
                <option value="phone_call">Phone Call</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="email">Email</option>
                <option value="in_person">In Person</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="form-group">
              <label>Tanggal Komunikasi <span class="text-danger">*</span></label>
              <input type="datetime-local" class="form-control" id="communication_date" name="communication_date" required>
            </div>

            <div class="form-group">
              <label>Catatan</label>
              <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Tulis catatan komunikasi..."></textarea>
            </div>

            <div class="form-group">
              <label>Status Follow-up <span class="text-danger">*</span></label>
              <select class="form-control" id="follow_up_status" name="follow_up_status" required>
                <option value="pending">Pending</option>
                <option value="contacted">Contacted</option>
                <option value="responded">Responded</option>
                <option value="no_response">No Response</option>
              </select>
            </div>

            <div class="form-group">
              <label>Tanggal Follow-up Berikutnya</label>
              <input type="date" class="form-control" id="next_follow_up_date" name="next_follow_up_date">
              <small class="form-text text-muted">Kosongkan jika tidak perlu follow-up</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Communication Modal -->
  <div class="modal fade" id="view-communication-modal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="margin-top: 2rem;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Komunikasi</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Jamaah:</strong> <span id="view-member-name"></span></p>
              <p><strong>Paket:</strong> <span id="view-package-name"></span></p>
              <p><strong>Metode:</strong> <span id="view-method"></span></p>
              <p><strong>Tanggal:</strong> <span id="view-date"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Status:</strong> <span id="view-status"></span></p>
              <p><strong>Follow-up:</strong> <span id="view-followup-date"></span></p>
              <p><strong>Dihubungi Oleh:</strong> <span id="view-contacted-by"></span></p>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-12">
              <p><strong>Catatan:</strong></p>
              <p id="view-notes" class="text-muted"></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Pending Follow-ups Modal -->
  <div class="modal fade" id="followups-modal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="margin-top: 2rem;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Follow-up Pending</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="followups-table">
              <thead>
                <tr>
                  <th>Tanggal Follow-up</th>
                  <th>Jamaah</th>
                  <th>Paket</th>
                  <th>Komunikasi Terakhir</th>
                  <th>Catatan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="followups-tbody">
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    $(document).ready(function() {
      // Initialize Select2
      $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
      });

      // Initialize DataTable
      const table = $('#communications-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route("travel.communication.data") }}',
          data: function(d) {
            d.member_id = $('#filter-member').val();
            d.package_id = $('#filter-package').val();
            d.communication_method = $('#filter-method').val();
            d.follow_up_status = $('#filter-status').val();
            d.date_from = $('#filter-date-from').val();
            d.date_to = $('#filter-date-to').val();
          }
        },
        columns: [
          { data: 'communication_date', name: 'communication_date' },
          { data: 'member_name', name: 'member.nama' },
          { data: 'package_name', name: 'travelPackage.package_name' },
          { data: 'method_badge', name: 'communication_method' },
          { data: 'status_badge', name: 'follow_up_status' },
          { 
            data: 'next_follow_up_date', 
            name: 'next_follow_up_date',
            render: function(data, type, row) {
              if (!data) return '-';
              return data + ' ' + row.overdue_indicator;
            }
          },
          { data: 'contacted_by_name', name: 'contactedByUser.name' },
          { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
      });

      // Filter handlers
      $('#filter-member, #filter-package, #filter-method, #filter-status, #filter-date-from, #filter-date-to').on('change', function() {
        table.ajax.reload();
      });

      $('#btn-reset-filter').on('click', function() {
        $('#filter-member, #filter-package, #filter-method, #filter-status').val('').trigger('change');
        $('#filter-date-from, #filter-date-to').val('');
        table.ajax.reload();
      });

      // Create communication
      $('#btn-create-communication').on('click', function() {
        $('#communication-form')[0].reset();
        $('#communication-id').val('');
        $('#communication-modal-title').text('Log Komunikasi');
        $('#communication_date').val(new Date().toISOString().slice(0, 16));
        $('#communication-modal').modal('show');
      });

      // Submit communication form
      $('#communication-form').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#communication-id').val();
        const url = id ? `/travel/communication/${id}` : '{{ route("travel.communication.store") }}';
        const method = id ? 'PUT' : 'POST';
        
        $.ajax({
          url: url,
          method: method,
          data: $(this).serialize(),
          success: function(response) {
            if (response.success) {
              $('#communication-modal').modal('hide');
              table.ajax.reload();
              Swal.fire('Berhasil!', response.message, 'success');
            }
          },
          error: function(xhr) {
            Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
          }
        });
      });

      // View communication
      $(document).on('click', '.view-communication', function() {
        const id = $(this).data('id');
        
        $.get(`/travel/communication/${id}`, function(response) {
          if (response.success) {
            const data = response.data;
            $('#view-member-name').text(data.member?.nama || '-');
            $('#view-package-name').text(data.travel_package?.package_name || '-');
            $('#view-method').text(data.communication_method_label);
            $('#view-date').text(new Date(data.communication_date).toLocaleString());
            $('#view-status').text(data.follow_up_status_label);
            $('#view-followup-date').text(data.next_follow_up_date || '-');
            $('#view-contacted-by').text(data.contacted_by_user?.name || '-');
            $('#view-notes').text(data.notes || '-');
            $('#view-communication-modal').modal('show');
          }
        });
      });

      // Edit communication
      $(document).on('click', '.edit-communication', function() {
        const id = $(this).data('id');
        
        $.get(`/travel/communication/${id}`, function(response) {
          if (response.success) {
            const data = response.data;
            $('#communication-id').val(data.id);
            $('#id_member').val(data.id_member).trigger('change');
            $('#id_travel_package').val(data.id_travel_package).trigger('change');
            $('#communication_method').val(data.communication_method);
            $('#communication_date').val(new Date(data.communication_date).toISOString().slice(0, 16));
            $('#notes').val(data.notes);
            $('#follow_up_status').val(data.follow_up_status);
            $('#next_follow_up_date').val(data.next_follow_up_date);
            $('#communication-modal-title').text('Edit Komunikasi');
            $('#communication-modal').modal('show');
          }
        });
      });

      // Delete communication
      $(document).on('click', '.delete-communication', function() {
        const id = $(this).data('id');
        
        Swal.fire({
          title: 'Hapus Komunikasi?',
          text: 'Data komunikasi akan dihapus permanen',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, Hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: `/travel/communication/${id}`,
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                if (response.success) {
                  table.ajax.reload();
                  Swal.fire('Terhapus!', response.message, 'success');
                }
              },
              error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
              }
            });
          }
        });
      });

      // View pending follow-ups
      $('#btn-view-followups').on('click', function() {
        $.get('{{ route("travel.communication.pending-followups") }}', function(response) {
          if (response.success) {
            const tbody = $('#followups-tbody');
            tbody.empty();
            
            if (response.data.length === 0) {
              tbody.append('<tr><td colspan="6" class="text-center">Tidak ada follow-up pending</td></tr>');
            } else {
              response.data.forEach(function(item) {
                const isOverdue = new Date(item.next_follow_up_date) < new Date();
                const rowClass = isOverdue ? 'table-danger' : '';
                
                tbody.append(`
                  <tr class="${rowClass}">
                    <td>${item.next_follow_up_date} ${isOverdue ? '<i class="fas fa-exclamation-triangle text-danger"></i>' : ''}</td>
                    <td>${item.member?.nama || '-'}</td>
                    <td>${item.travel_package?.package_name || '-'}</td>
                    <td>${new Date(item.communication_date).toLocaleDateString()}</td>
                    <td>${item.notes || '-'}</td>
                    <td>
                      <button class="btn btn-sm btn-primary edit-communication" data-id="${item.id}">
                        <i class="fas fa-edit"></i> Update
                      </button>
                    </td>
                  </tr>
                `);
              });
            }
            
            $('#followups-modal').modal('show');
          }
        });
      });
    });
  </script>
  @endpush
</x-layouts.admin>
