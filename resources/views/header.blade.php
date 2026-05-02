<header class="main-header">
    <!-- Logo -->
    <a href="index2.html" class="logo">
        @php
            $words = explode(' ', "MORRA ERP");
            $word  = '';
            foreach ($words as $w) {
                $word .= $w[0];
            }
        @endphp
        <span class="logo-mini"><b>M</b>ERP</span>
        <span class="logo-lg"><b>MORRA</b>ERP</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button -->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Notifications Dropdown -->
                <li class="dropdown notifications-menu" id="notifications-dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning" id="notification-count" style="display: none;">0</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header" id="notification-header">Anda memiliki 0 notifikasi</li>
                        <li>
                            <ul class="menu" id="notification-list">
                                <li class="text-center" style="padding: 10px;">
                                    <small class="text-muted">Tidak ada notifikasi</small>
                                </li>
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="{{ route('notifications.index') }}">Lihat Semua Notifikasi</a>
                        </li>
                    </ul>
                </li>
                
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ url(auth()->user()->foto ?? '') }}" class="user-image img-profil" 
                            alt="User Image">
                        <span class="hidden-xs"> {{ auth()->user()->name ?? 'User' }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="{{ url(auth()->user()->foto ?? '') }}" class="img-circle img-profil" 
                                alt="User Image">

                            <p>
                                {{ auth()->user()->name ?? 'User' }} - {{ auth()->user()->role ?? 'POSHAN Crew' }}
                                <small>{{ auth()->user()->email ?? 'email' }}</small>
                            </p>
                        </li>
                        
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('admin.user.profil') }}" class="btn btn-default btn-flat">Profil</a>
                            </div>
                            <div class="pull-right">
                                <a href="#" class="btn btn-default btn-flat" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Keluar</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

<form action="{{ route('logout') }}" method="POST" style="display: none;" id="logout-form" class="d-none">
    @csrf
</form>

<script>
// Notification functionality
(function() {
    let notificationCheckInterval;
    
    function loadNotifications() {
        fetch('{{ route("notifications.unread") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationUI(data.notifications, data.count);
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
    }
    
    function updateNotificationUI(notifications, count) {
        const countBadge = document.getElementById('notification-count');
        const header = document.getElementById('notification-header');
        const list = document.getElementById('notification-list');
        
        // Update count badge
        if (count > 0) {
            countBadge.textContent = count > 99 ? '99+' : count;
            countBadge.style.display = 'inline';
        } else {
            countBadge.style.display = 'none';
        }
        
        // Update header
        header.textContent = `Anda memiliki ${count} notifikasi`;
        
        // Update list
        if (notifications.length === 0) {
            list.innerHTML = '<li class="text-center" style="padding: 10px;"><small class="text-muted">Tidak ada notifikasi</small></li>';
        } else {
            list.innerHTML = notifications.map(notification => {
                const icon = notification.icon || 'fa-bell';
                const color = notification.color || 'default';
                const timeAgo = formatTimeAgo(notification.created_at);
                
                return `
                    <li>
                        <a href="#" onclick="markAsRead(${notification.id}); return false;">
                            <i class="fa ${icon} text-${color}"></i> ${notification.title}
                            <br><small class="text-muted">${timeAgo}</small>
                        </a>
                    </li>
                `;
            }).join('');
        }
    }
    
    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        if (seconds < 60) return 'Baru saja';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' menit yang lalu';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' jam yang lalu';
        if (seconds < 604800) return Math.floor(seconds / 86400) + ' hari yang lalu';
        return date.toLocaleDateString('id-ID');
    }
    
    window.markAsRead = function(notificationId) {
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
                loadNotifications();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    };
    
    window.markAllAsRead = function() {
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
                loadNotifications();
            }
        })
        .catch(error => console.error('Error marking all as read:', error));
    };
    
    // Load notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadNotifications();
        
        // Refresh notifications every 30 seconds
        notificationCheckInterval = setInterval(loadNotifications, 30000);
    });
})();
</script>
