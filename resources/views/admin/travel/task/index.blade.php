<x-layouts.admin :title="'Travel / Task Management'">
  <div class="space-y-4 overflow-x-hidden">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Task Management</h1>
        <p class="text-slate-600 text-sm">Kelola dan pantau tugas tim untuk paket perjalanan.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.inventaris.travel.tasks.my-tasks') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-user text-lg'></i> My Tasks
        </a>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Team</label>
          <select id="teamFilter" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="">All Teams</option>
            @foreach($teams as $team)
              <option value="{{ $team->team_code }}" {{ $selectedTeam == $team->team_code ? 'selected' : '' }}>
                {{ $team->team_name }}
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
          <select id="statusFilter" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Assigned To</label>
          <select id="userFilter" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="">All Users</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Show Overdue</label>
          <div class="flex items-center h-10">
            <input type="checkbox" id="overdueFilter" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
            <label for="overdueFilter" class="ml-2 text-sm text-slate-700">Only overdue tasks</label>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Pending</p>
            <p class="text-2xl font-bold text-yellow-600" id="pendingCount">0</p>
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
            <p class="text-2xl font-bold text-blue-600" id="inProgressCount">0</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
            <i class='bx bx-loader-alt text-2xl text-blue-600'></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Completed</p>
            <p class="text-2xl font-bold text-green-600" id="completedCount">0</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
            <i class='bx bx-check-circle text-2xl text-green-600'></i>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-600">Overdue</p>
            <p class="text-2xl font-bold text-red-600" id="overdueCount">0</p>
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
        <table id="tasksTable" class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Task</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Package</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Stage</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Team</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 uppercase tracking-wider">Assigned To</th>
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

  <!-- Reassign Task Modal -->
  <div id="reassignTaskModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable" style="margin-top: 2rem;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Reassign Task</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Assign To</label>
            <select id="reassignUser" class="form-control">
              <option value="">Select user...</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="confirmReassign">Reassign</button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    $(document).ready(function() {
      let table;
      let currentTaskId = null;
      let currentTeamCode = null;

      // Initialize DataTable
      function initTable() {
        if (table) {
          table.destroy();
        }

        table = $('#tasksTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: '{{ route("admin.inventaris.travel.tasks.data") }}',
            data: function(d) {
              d.team = $('#teamFilter').val();
              d.status = $('#statusFilter').val();
              d.user = $('#userFilter').val();
              d.overdue = $('#overdueFilter').is(':checked') ? '1' : '0';
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
            { data: 'assigned_user_name' },
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
            { data: 'action', orderable: false, searchable: false }
          ],
          order: [[5, 'asc']],
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
            team: $('#teamFilter').val(),
            length: -1
          },
          success: function(response) {
            let pending = 0, inProgress = 0, completed = 0, overdue = 0;
            
            response.data.forEach(function(task) {
              if (task.status === 'pending') pending++;
              else if (task.status === 'in_progress') inProgress++;
              else if (task.status === 'completed') completed++;
              
              if (task.isOverdue) overdue++;
            });

            $('#pendingCount').text(pending);
            $('#inProgressCount').text(inProgress);
            $('#completedCount').text(completed);
            $('#overdueCount').text(overdue);
          }
        });
      }

      // Filter change handlers
      $('#teamFilter, #statusFilter, #userFilter').on('change', function() {
        table.ajax.reload();
        
        // Load team members when team changes
        if ($(this).attr('id') === 'teamFilter') {
          loadTeamMembers($(this).val());
        }
      });

      $('#overdueFilter').on('change', function() {
        table.ajax.reload();
      });

      // Load team members
      function loadTeamMembers(teamCode) {
        if (!teamCode) {
          $('#userFilter').html('<option value="">All Users</option>');
          return;
        }

        $.ajax({
          url: '/admin/travel/tasks/team/' + teamCode + '/members',
          success: function(response) {
            let options = '<option value="">All Users</option>';
            response.members.forEach(function(member) {
              options += '<option value="' + member.id + '">' + member.name + '</option>';
            });
            $('#userFilter').html(options);
          }
        });
      }

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

      // Reassign task
      $(document).on('click', '.reassign-task', function() {
        currentTaskId = $(this).data('id');
        
        // Get task details to load team members
        $.ajax({
          url: '/admin/travel/tasks/data',
          data: { id: currentTaskId },
          success: function(response) {
            if (response.data && response.data.length > 0) {
              currentTeamCode = response.data[0].assigned_to_team;
              loadReassignUsers(currentTeamCode);
            }
          }
        });
        
        $('#reassignTaskModal').modal('show');
      });

      function loadReassignUsers(teamCode) {
        if (!teamCode) return;

        $.ajax({
          url: '/admin/travel/tasks/team/' + teamCode + '/members',
          success: function(response) {
            let options = '<option value="">Select user...</option>';
            response.members.forEach(function(member) {
              options += '<option value="' + member.id + '">' + member.name + '</option>';
            });
            $('#reassignUser').html(options);
          }
        });
      }

      $('#confirmReassign').on('click', function() {
        const userId = $('#reassignUser').val();
        if (!userId) {
          toastr.error('Please select a user');
          return;
        }

        $.ajax({
          url: '/admin/travel/tasks/' + currentTaskId + '/reassign',
          method: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            assigned_to_user: userId
          },
          success: function(response) {
            $('#reassignTaskModal').modal('hide');
            table.ajax.reload();
            toastr.success(response.message);
          },
          error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Failed to reassign task');
          }
        });
      });

      // Initialize
      initTable();
      loadTeamMembers($('#teamFilter').val());
    });
  </script>
  @endpush
</x-layouts.admin>
