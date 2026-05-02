@props(['filterType', 'filters' => []])

<div class="card card-outline card-primary" x-data="filterPanel()">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-2"></i>
            Advanced Filters
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form id="filterForm" method="GET">
            @if($filterType === 'package')
                <!-- Package Filters -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Departure Date From</label>
                            <input type="date" 
                                   name="departure_date_from" 
                                   class="form-control" 
                                   value="{{ $filters['departure_date_from'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Departure Date To</label>
                            <input type="date" 
                                   name="departure_date_to" 
                                   class="form-control" 
                                   value="{{ $filters['departure_date_to'] ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Destination</label>
                            <input type="text" 
                                   name="destination" 
                                   class="form-control" 
                                   placeholder="e.g., Mecca, Medina"
                                   value="{{ $filters['destination'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Package Type</label>
                            <select name="package_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="hajj" {{ ($filters['package_type'] ?? '') === 'hajj' ? 'selected' : '' }}>Hajj</option>
                                <option value="umrah" {{ ($filters['package_type'] ?? '') === 'umrah' ? 'selected' : '' }}>Umrah</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="full" {{ ($filters['status'] ?? '') === 'full' ? 'selected' : '' }}>Full</option>
                                <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Workflow Stage</label>
                            <select name="workflow_stage" class="form-control">
                                <option value="">All Stages</option>
                                <option value="product_analysis">Product Analysis</option>
                                <option value="flight_tickets">Flight Tickets</option>
                                <option value="design_materials">Design Materials</option>
                                <option value="finance">Finance</option>
                                <option value="follow_up">Follow Up</option>
                                <option value="closing">Closing</option>
                                <option value="cs_all_divisions">CS All Divisions</option>
                                <option value="social_media">Social Media</option>
                                <option value="administration">Administration</option>
                                <option value="logistics">Logistics</option>
                                <option value="save_jamaah_data">Save Jamaah Data</option>
                                <option value="offer_package">Offer Package</option>
                            </select>
                        </div>
                    </div>
                </div>
            @elseif($filterType === 'jamaah')
                <!-- Jamaah Filters -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Keberangkatan</label>
                            <select name="keberangkatan_id" class="form-control select2">
                                <option value="">All Keberangkatan</option>
                                @foreach(\App\Models\Keberangkatan::all() as $keberangkatan)
                                    <option value="{{ $keberangkatan->id }}" 
                                            {{ ($filters['keberangkatan_id'] ?? '') == $keberangkatan->id ? 'selected' : '' }}>
                                        {{ $keberangkatan->keberangkatan_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Package</label>
                            <select name="package_id" class="form-control select2">
                                <option value="">All Packages</option>
                                @foreach(\App\Models\TravelPackage::all() as $package)
                                    <option value="{{ $package->id }}" 
                                            {{ ($filters['package_id'] ?? '') == $package->id ? 'selected' : '' }}>
                                        {{ $package->package_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Status</label>
                            <select name="payment_status" class="form-control">
                                <option value="">All Status</option>
                                <option value="unpaid" {{ ($filters['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="partial" {{ ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Document Status</label>
                            <select name="document_status" class="form-control">
                                <option value="">All Status</option>
                                <option value="complete" {{ ($filters['document_status'] ?? '') === 'complete' ? 'selected' : '' }}>Complete</option>
                                <option value="incomplete" {{ ($filters['document_status'] ?? '') === 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                            </select>
                        </div>
                    </div>
                </div>
            @elseif($filterType === 'task')
                <!-- Task Filters -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Team</label>
                            <select name="team" class="form-control">
                                <option value="">All Teams</option>
                                <option value="administration">Administration</option>
                                <option value="customer_service">Customer Service</option>
                                <option value="finance">Finance</option>
                                <option value="media">Media</option>
                                <option value="logistics">Logistics</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Due Date From</label>
                            <input type="date" 
                                   name="due_date_from" 
                                   class="form-control" 
                                   value="{{ $filters['due_date_from'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Due Date To</label>
                            <input type="date" 
                                   name="due_date_to" 
                                   class="form-control" 
                                   value="{{ $filters['due_date_to'] ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Assigned To</label>
                            <select name="assigned_to_user" class="form-control select2">
                                <option value="">All Users</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ ($filters['assigned_to_user'] ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Package</label>
                            <select name="package_id" class="form-control select2">
                                <option value="">All Packages</option>
                                @foreach(\App\Models\TravelPackage::all() as $package)
                                    <option value="{{ $package->id }}" 
                                            {{ ($filters['package_id'] ?? '') == $package->id ? 'selected' : '' }}>
                                        {{ $package->package_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group float-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i> Apply Filters
                        </button>
                        <button type="button" class="btn btn-secondary" @click="clearFilters()">
                            <i class="fas fa-times mr-1"></i> Clear
                        </button>
                        <button type="button" class="btn btn-info" @click="saveFilter()">
                            <i class="fas fa-save mr-1"></i> Save Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Saved Filters -->
        <div class="mt-3" x-show="savedFilters.length > 0">
            <hr>
            <h5>Saved Filters</h5>
            <div class="list-group">
                <template x-for="filter in savedFilters" :key="filter.id">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span x-text="filter.filter_name" @click="applyFilter(filter)" style="cursor: pointer;"></span>
                        <button type="button" class="btn btn-sm btn-danger" @click="deleteFilter(filter.id)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function filterPanel() {
    return {
        savedFilters: [],
        filterType: '{{ $filterType }}',
        
        init() {
            this.loadSavedFilters();
            $('.select2').select2();
        },
        
        loadSavedFilters() {
            fetch('{{ route("admin.search.filter.saved") }}?filter_type=' + this.filterType)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.savedFilters = data.filters;
                    }
                });
        },
        
        clearFilters() {
            document.getElementById('filterForm').reset();
            $('.select2').val(null).trigger('change');
        },
        
        saveFilter() {
            const filterName = prompt('Enter a name for this filter:');
            if (!filterName) return;
            
            const formData = new FormData(document.getElementById('filterForm'));
            const filterData = Object.fromEntries(formData.entries());
            
            fetch('{{ route("admin.search.filter.save") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    filter_name: filterName,
                    filter_type: this.filterType,
                    filter_data: filterData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Filter saved successfully!');
                    this.loadSavedFilters();
                } else {
                    alert('Failed to save filter');
                }
            });
        },
        
        applyFilter(filter) {
            const form = document.getElementById('filterForm');
            Object.keys(filter.filter_data).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = filter.filter_data[key];
                    if (input.classList.contains('select2')) {
                        $(input).val(filter.filter_data[key]).trigger('change');
                    }
                }
            });
        },
        
        deleteFilter(filterId) {
            if (!confirm('Are you sure you want to delete this filter?')) return;
            
            fetch(`{{ url('admin/search/filter') }}/${filterId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Filter deleted successfully!');
                    this.loadSavedFilters();
                } else {
                    alert('Failed to delete filter');
                }
            });
        }
    }
}
</script>
