<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 fixed-start bg-white" id="sidenav-main">
    <div class="sidenav-header d-flex flex-column align-items-center justify-content-start px-3 py-4">
        <a class="navbar-brand m-0 d-flex align-items-center justify-content-center brand-logo"
            href="{{ route('admin.dashboard') }}">
            <img src="{{ asset(config('branding.branding_logo')) }}" alt="Backoffice" class="navbar-brand-img" />
        </a>
    </div>

    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <hr class="horizontal dark mt-0">
        <ul class="navbar-nav">
            <li class="nav-item first-nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.teams.index') ? 'active' : '' }}"
                    href="{{ route('admin.teams.index') }}">
                    <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-users text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Equipa</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.posts.index') ? 'active' : '' }}"
                    href="{{ route('admin.posts.index') }}">
                    <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-newspaper text-info text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Notícias</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.entities.index') ? 'active' : '' }}"
                    href="{{ route('admin.entities.index') }}">
                    <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-building text-danger text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Entidades</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.formations.index') ? 'active' : '' }}"
                    href="{{ route('admin.formations.index') }}">
                    <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-graduation-cap text-success text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Formações</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.protocols.index') ? 'active' : '' }}"
                    href="{{ route('admin.protocols.index') }}">
                    <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-school text-info text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Protocolos</span>
                </a>
            </li>
            
            <!-- <hr class="horizontal dark mt-0">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.perfil.*') ? 'active' : '' }}" href="{{ route('admin.perfil.edit') }}">
                    <div class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-circle text-success text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Perfil</span>
                </a>
            </li> -->
        </ul>
    </div>
</aside>
