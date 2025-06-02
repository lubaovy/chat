<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link text-center">
        <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Admin Logo" class="brand-image">
        <span class="brand-text font-weight-bold">Admin Panel</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @if(auth()->check() && auth()->user()->role === 'admin')
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Quản lý Users</p>
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a href="{{ route('admin.posts.index') }}" class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-newspaper"></i>
                        <p>Quản lý Bài viết</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Quản lý Trang</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.files.index') }}" class="nav-link {{ request()->routeIs('admin.files.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-folder"></i>
                        <p>Quản lý File</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('chatbot.settings') }}" class="nav-link {{ request()->routeIs('chatbot.settings') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-robot"></i>
                        <p>Cấu hình Chatbot</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.website_settings.edit') }}" class="nav-link {{ request()->routeIs('admin.website_settings.edit') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-globe"></i>
                        <p>Cấu hình Website</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
