<x-layouts.admin :title="'Travel / My Tasks'">
  <div class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">My Tasks</h1>
        <p class="text-slate-600 text-sm">Tugas yang ditugaskan kepada Anda.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.inventaris.travel.tasks.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-600 text-white px-4 py-2 hover:bg-slate-700">
          <i class='bx bx-arrow-back text-lg'></i> All Tasks
        </a>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select id="statusFilter" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Priority</label>
          <select id="priorityFilter" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="">All</option>
            <option value="overdue">Overdue</option>
            <option value="due_soon">Due Soon (3 days)</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Sort By</label>
          <select id="sortFilter" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="due_date_asc">Due Date (Earliest)</option>
            <option value="due_date_desc">Due Date (Latest)</option>
            <option value="created_desc">Recently Added</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Pending</p>
            <p class="text-2xl font-bold text-yellow-600" id="myPendingCount">0</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
            <i class='bx bx-time text-2xl text-yellow-600'></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">In Progress</p>
            <p class="text-2xl font-bold text-blue-600" id="myInProgressCount">0</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
            <i class='bx bx-loader-alt text-2xl text-blue-600'></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Overdue</p>
            <p class="text-2xl font-bold text-red-600" id="myOverdueCount">0</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
            <i class='bx bx-error text-2xl text-red-600'></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table id="myTasksTable" class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Task</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Package</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Stage</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Team</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Due Date</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Complete Task Modal -->
  <div id="completeTaskModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable" style="margin-top: 2rem;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Complete Task</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Notes (Optional)</label>
            <textarea id="completeNotes" class="form-control" rows="3" placeholder="Add completion notes..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" id="confirmComplete">Complete Task</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Update Status Modal -->
  <div id="updateStatusModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable" style="margin-top: 2rem;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Task Status</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Status</label>
            <select id="newStatus" class="form-control">
              <option value="pending">Pending</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Update Status</button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    $(document).ready(function() {
      let table;
      let currentTaskId = null;
      const userId = {{ $userId }};

      // Initialize DataTable
      function initTable() {
        if (table) {
          table.destroy();
        }

        table = $('#myTasksTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: '{{ route("admin.inventaris.travel.tasks.data") }}',
            data: function(d) {
              d.user = userId;
              d.status = $('#statusFilter').val();
              
              const priority = $('#priorityFilter').val();
              if (priority === 'overdue') {
                d.overdue = '1';
              }
            }
          },
          columns: [
            { 
              data: 'task_name',
              render: function(data, type, row) {
                let html = '<div class="font-medium">' + data + '</div>';
                if (row.task_description) {
                  html += '<div class="text-sm text-slate-600">' + row.task_description + '</div>';
                }
                return html;
              }
            },
            { data: 'package_name' },
            { data: 'stage_name' },
            { data: 'team_name' },
            { 
              data: 'due_date',
              render: function(data) {
                return data ? moment(data).format('DD MMM YYYY') : '-';
              }
            },
            { 
              data: 'status_badge',
              render: function(data, type, row) {
                return data + ' ' + row.overdue_indicator;
              }
            },
            { 
              data: null,
              orderable: false,
              searchable: false,
              render: function(data, type, row) {
                let actions = '<div class="btn-group">';
                
                // View button
                actions += '<a href="/admin/travel/package/' + row.id_travel_package + '/detail" class="btn btn-sm btn-info" title="View Package">';
                actions += '<i class="fas fa-eye"></i></a>';
                
                // Update status button
                if (row.status !== 'completed') {
                  actions += '<button type="button" class="btn btn-sm btn-warning update-status" data-id="' + row.id + '" title="Update Status">';
                  actions += '<i class="fas fa-edit"></i></button>';
                }
                
                // Complete button
                if (row.status !== 'completed') {
                  actions += '<button type="button" class="btn btn-sm btn-success complete-task" data-id="' + row.id + '" title="Complete Task">';
                  actions += '<i class="fas fa-check"></i></button>';
                }
                
                actions += '</div>';
                return actions;
              }
            }
          ],
          order: [[4, 'asc']],
          pageLength: 25,
          drawCallback: function() {
            updateStats();
          }
        });
      }

      // Update statistics
      function updateStats() {
        $.ajax({
          url: '{{ route("admin.inventaris.travel.tasks.data") }}',
          data: {
            user: userId,
            length: -1
          },
          success: function(response) {
            let pending = 0, inProgress = 0, overdue = 0;
            
            response.data.forEach(function(task) {
              if (task.status === 'pending') pending++;
              else if (task.status === 'in_progress') inProgress++;
              
              if (task.isOverdue) overdue++;
            });

            $('#myPendingCount').text(pending);
            $('#myInProgressCount').text(inProgress);
            $('#myOverdueCount').text(overdue);
          }
        });
      }

      // Filter change handlers
      $('#statusFilter, #priorityFilter, #sortFilter').on('change', function() {
        table.ajax.reload();
      });

      // Complete task
      $(document).on('click', '.complete-task', function() {
        currentTaskId = $(this).data('id');
        $('#completeNotes').val('');
        $('#completeTaskModal').modal('show');
      });

      $('#confirmComplete').on('click', function() {
        $.ajax({
          url: '/admin/travel/tasks/' + currentTaskId + '/complete',
          method: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            notes: $('#completeNotes').val()
          },
          success: function(response) {
            $('#completeTaskModal').modal('hide');
            table.ajax.reload();
            toastr.success(response.message);
          },
          error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Failed to complete task');
          }
        });
      });

      // Update status
      $(document).on('click', '.update-status', function() {
        currentTaskId = $(this).data('id');
        $('#updateStatusModal').modal('show');
      });

      $('#confirmStatusUpdate').on('click', function() {
        const status = $('#newStatus').val();

        $.ajax({
          url: '/admin/travel/tasks/' + currentTaskId + '/status',
          method: 'PUT',
          data: {
            _token: '{{ csrf_token() }}',
            status: status
          },
          success: function(response) {
            $('#updateStatusModal').modal('hide');
            table.ajax.reload();
            toastr.success(response.message);
          },
          error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Failed to update status');
          }
        });
      });

      // Initialize
      initTable();
    });
  </script>
  @endpush
</x-layouts.admin>
