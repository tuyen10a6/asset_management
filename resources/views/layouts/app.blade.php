<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản lý tài sản - Livespo')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .sidebar {
            min-height: 100vh;
            background: white;
        }
        .sidebar .nav-link {
            color: #808184;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #024ba1;
            background-color: #ebedf3;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
        }

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <img style="width: 50%" src="{{url('/images/logo/logo.png')}}" alt="logo"/>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}" href="{{ route('assets.index') }}">
                                <i class="fas fa-laptop me-2"></i>
                                Quản lý tài sản
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('assignments.*') ? 'active' : '' }}" href="{{ route('assignments.index') }}">
                                <i class="fas fa-handshake me-2"></i>
                                Cấp phát tài sản
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('incidents.*') ? 'active' : '' }}" href="{{ route('incidents.index') }}">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Báo cáo sự cố
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('qr.*') ? 'active' : '' }}" href="{{ route('qr.scan') }}">
                                <i class="fas fa-qrcode me-2"></i>
                                Quét QR Code
                            </a>
                        </li>
                        @if(auth()->user()->canManageAssets())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                                <i class="fas fa-users me-2"></i>
                                Nhân viên
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}" href="{{ route('departments.index') }}">
                                <i class="fas fa-building me-2"></i>
                                Bộ phận
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                <i class="fas fa-chart-bar me-2"></i>
                                Báo cáo
                            </a>
                        </li>
                        @endif
                        @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="fas fa-user-cog me-2"></i>
                                Quản lý người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                                <i class="fas fa-cog me-2"></i>
                                Cài đặt hệ thống
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top navbar -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('page-title', 'Dashboard')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <!-- Notification Bell -->
                        <div class="dropdown me-2">
                            <button class="btn btn-outline-primary position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count" style="display: none;">
                                    0
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notificationDropdown" style="width: 350px;">
                                <div class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span>Thông báo</span>
                                    <button class="btn btn-sm btn-link text-decoration-none" id="mark-all-read">
                                        Đánh dấu tất cả đã đọc
                                    </button>
                                </div>
                                <div class="dropdown-divider m-0"></div>
                                <div id="notification-list" style="max-height: 300px; overflow-y: auto;">
                                    <div class="text-center p-3">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Đang tải...</span>
                                    </div>
                                </div>
                                <div class="dropdown-divider m-0"></div>
                                <div class="dropdown-footer text-center p-2">
                                    <a href="{{ route('profile.notifications') }}" class="btn btn-sm btn-primary">
                                        Xem tất cả thông báo
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                {{ auth()->user()->employee->full_name ?? auth()->user()->username }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>Hồ sơ cá nhân</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.change-password') }}"><i class="fas fa-key me-2"></i>Đổi mật khẩu</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.notifications') }}">
                                    <i class="fas fa-bell me-2"></i>Thông báo
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <span class="badge bg-danger ms-1">{{ auth()->user()->unreadNotifications->count() }}</span>
                                    @endif
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.activity') }}"><i class="fas fa-history me-2"></i>Hoạt động</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Flash messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Notification Bell Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationList = document.getElementById('notification-list');
            const notificationCount = document.getElementById('notification-count');
            const markAllReadBtn = document.getElementById('mark-all-read');

            // Load unread count on page load
            loadUnreadCount();

            // Load notifications when dropdown is opened
            notificationDropdown.addEventListener('click', function() {
                loadRecentNotifications();
            });

            // Also load when dropdown is shown via Bootstrap event
            document.getElementById('notificationDropdown').addEventListener('shown.bs.dropdown', function () {
                loadRecentNotifications();
            });

            // Mark all as read
            markAllReadBtn.addEventListener('click', function() {
                fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadUnreadCount();
                        loadRecentNotifications();
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            function loadUnreadCount() {
                fetch('{{ route("notifications.unread-count") }}')
                    .then(response => response.json())
                    .then(data => {
                        updateNotificationCount(data.count);
                    })
                    .catch(error => {
                        console.error('Error loading unread count:', error);
                        // Fallback to server-side count
                        const count = {{ auth()->user()->unreadNotifications->count() ?? 0 }};
                        updateNotificationCount(count);
                    });
            }

            function loadRecentNotifications() {
                notificationList.innerHTML = `
                    <div class="text-center p-3">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-2">Đang tải...</span>
                    </div>
                `;

                fetch('{{ route("notifications.recent") }}')
                    .then(response => response.json())
                    .then(notifications => {
                        renderNotifications(notifications);
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        notificationList.innerHTML = `
                            <div class="text-center p-3 text-muted">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <p class="mb-0">Không thể tải thông báo</p>
                                <small>Vui lòng thử lại sau</small>
                            </div>
                        `;
                    });
            }

            function renderNotifications(notifications) {
                if (notifications.length === 0) {
                    notificationList.innerHTML = `
                        <div class="text-center p-3 text-muted">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <p class="mb-0">Không có thông báo nào</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                notifications.forEach(notification => {
                    const iconClass = getNotificationIcon(notification.type);
                    const bgClass = notification.is_read ? '' : 'bg-light';
                    const timeAgo = formatTimeAgo(notification.created_at);

                    html += `
                        <div class="dropdown-item-text ${bgClass} p-3 border-bottom notification-item" data-id="${notification.id}">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="${iconClass} text-${notification.type === 'error' ? 'danger' : notification.type}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">${notification.title}</h6>
                                    <p class="mb-1 small text-muted">${notification.message}</p>
                                    <small class="text-muted">${timeAgo}</small>
                                </div>
                                ${!notification.is_read ? '<div class="ms-2"><span class="badge bg-primary rounded-pill">&nbsp;</span></div>' : ''}
                            </div>
                        </div>
                    `;
                });

                notificationList.innerHTML = html;

                // Add click handlers for individual notifications
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const notificationId = this.dataset.id;
                        markNotificationAsRead(notificationId);
                    });
                });
            }

            function getNotificationIcon(type) {
                switch(type) {
                    case 'warning': return 'fas fa-exclamation-triangle';
                    case 'error': return 'fas fa-exclamation-circle';
                    case 'success': return 'fas fa-check-circle';
                    case 'info': return 'fas fa-info-circle';
                    case 'incident': return 'fas fa-bug';
                    default: return 'fas fa-bell';
                }
            }

            function updateNotificationCount(count) {
                if (count > 0) {
                    notificationCount.textContent = count > 99 ? '99+' : count;
                    notificationCount.style.display = 'block';
                } else {
                    notificationCount.style.display = 'none';
                }
            }

            function markNotificationAsRead(notificationId) {
                fetch(`{{ url('/notifications') }}/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadUnreadCount();
                        // Update the notification item to show as read
                        const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                        if (notificationItem) {
                            notificationItem.classList.remove('bg-light');
                            const badge = notificationItem.querySelector('.badge');
                            if (badge) {
                                badge.remove();
                            }
                        }
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
            }

            function formatTimeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);

                if (diffInSeconds < 60) {
                    return 'Vừa xong';
                } else if (diffInSeconds < 3600) {
                    const minutes = Math.floor(diffInSeconds / 60);
                    return `${minutes} phút trước`;
                } else if (diffInSeconds < 86400) {
                    const hours = Math.floor(diffInSeconds / 3600);
                    return `${hours} giờ trước`;
                } else if (diffInSeconds < 2592000) {
                    const days = Math.floor(diffInSeconds / 86400);
                    return `${days} ngày trước`;
                } else {
                    return date.toLocaleDateString('vi-VN');
                }
            }

            // Refresh notification count every 30 seconds
            setInterval(loadUnreadCount, 30000);
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
