<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MORRA ERP')</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Travel Management Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('css/travel-responsive.css') }}">
    
    <!-- Travel Management Modal & Notification CSS -->
    <link rel="stylesheet" href="{{ asset('css/travel-modals.css') }}">
    
    <!-- Travel Management Validation CSS -->
    <link rel="stylesheet" href="{{ asset('css/travel-validation.css') }}">
    
    <style>
        .global-search-container {
            position: relative;
            max-width: 500px;
            margin: 0 auto;
        }
        .global-search-input {
            width: 100%;
            padding: 8px 40px 8px 15px;
            border: 1px solid #ced4da;
            border-radius: 20px;
            font-size: 14px;
        }
        .global-search-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .global-search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .autocomplete-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ced4da;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .autocomplete-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }
        .autocomplete-item:hover {
            background-color: #f8f9fa;
        }
        .autocomplete-item:last-child {
            border-bottom: none;
        }
    </style>
    
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Global Search -->
            <div class="navbar-nav mx-auto">
                <div class="global-search-container">
                    <input type="text" 
                           id="globalSearch" 
                           class="global-search-input" 
                           placeholder="Cari jamaah, nomor paspor, kode booking..."
                           autocomplete="off">
                    <i class="fas fa-search global-search-icon"></i>
                    <div id="autocompleteResults" class="autocomplete-results"></div>
                </div>
            </div>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge" id="notificationCount">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">Notifications</span>
                        <div class="dropdown-divider"></div>
                        <div id="notificationList">
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-info-circle mr-2"></i> No new notifications
                            </a>
                        </div>
                    </div>
                </li>
                
                <!-- User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <a href="{{ route('profile.show') }}" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <img src="{{ asset('img/logo-auto.png') }}" alt="Logo" class="brand-image img-circle elevation-3">
                <span class="brand-text font-weight-light">MORRA ERP</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                @include('partials.sidebar.main')
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} MORRA ERP.</strong>
            All rights reserved.
        </footer>
    </div>

    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <!-- Global Search Script -->
    <script>
        $(document).ready(function() {
            let searchTimeout;
            const searchInput = $('#globalSearch');
            const resultsContainer = $('#autocompleteResults');

            // Autocomplete on input
            searchInput.on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val().trim();

                if (query.length < 2) {
                    resultsContainer.hide().empty();
                    return;
                }

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: '{{ route("admin.inventaris.search.autocomplete") }}',
                        method: 'GET',
                        data: { q: query },
                        success: function(response) {
                            if (response.success && response.suggestions.length > 0) {
                                displayAutocomplete(response.suggestions);
                            } else {
                                resultsContainer.hide().empty();
                            }
                        },
                        error: function() {
                            resultsContainer.hide().empty();
                        }
                    });
                }, 300);
            });

            // Handle Enter key for full search
            searchInput.on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    const query = $(this).val().trim();
                    if (query.length >= 2) {
                        window.location.href = '{{ route("admin.inventaris.search") }}?q=' + encodeURIComponent(query);
                    }
                }
            });

            // Display autocomplete results
            function displayAutocomplete(suggestions) {
                resultsContainer.empty();
                
                suggestions.forEach(function(item) {
                    const itemHtml = `
                        <div class="autocomplete-item" data-value="${item.value}">
                            <i class="fas fa-${getIcon(item.type)} mr-2"></i>
                            ${item.label}
                        </div>
                    `;
                    resultsContainer.append(itemHtml);
                });

                resultsContainer.show();
            }

            // Click on autocomplete item
            $(document).on('click', '.autocomplete-item', function() {
                const value = $(this).data('value');
                searchInput.val(value);
                resultsContainer.hide();
                searchInput.trigger('keypress', { which: 13 });
            });

            // Hide autocomplete when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.global-search-container').length) {
                    resultsContainer.hide();
                }
            });

            // Get icon based on type
            function getIcon(type) {
                switch(type) {
                    case 'jamaah_name': return 'user';
                    case 'passport': return 'passport';
                    case 'booking': return 'ticket-alt';
                    default: return 'search';
                }
            }

            // Load notification count
            function loadNotificationCount() {
                $.ajax({
                    url: '{{ route("admin.inventaris.notifications.unread-count") }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#notificationCount').text(response.count);
                            if (response.count > 0) {
                                $('#notificationCount').show();
                            } else {
                                $('#notificationCount').hide();
                            }
                        }
                    }
                });
            }

            // Load notifications on page load
            loadNotificationCount();
            
            // Refresh notification count every 60 seconds
            setInterval(loadNotificationCount, 60000);
        });
    </script>
    
    <!-- Travel Management UI Helpers -->
    <script src="{{ asset('js/travel-ui-helpers.js') }}"></script>
    
    <!-- Travel Management Validation Helpers -->
    <script src="{{ asset('js/travel-validation-errors.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
