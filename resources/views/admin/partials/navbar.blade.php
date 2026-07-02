<!-- Navbar principal -->
<nav class="navbar navbar-main px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <div class="d-flex align-items-center justify-content-between w-100">
            <div class="nav-left d-flex align-items-center">
                {{-- Botão menu (hamburger) --}}
                <button class="btn btn-hamburger me-2" id="sidebarToggle" type="button" aria-label="Abrir menu" aria-controls="sidenav-main" aria-expanded="false">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
            </div>
            <div class="nav-center flex-grow-1 text-center">
                <h6 class="font-weight-bolder mb-0 navbar-title">@yield('title', 'Dashboard')</h6>
            </div>
            <div class="nav-right d-flex align-items-center justify-content-end">

                <ul class="navbar-nav flex-row align-items-center mb-0" style="height: 40px;">
                    @include('admin.partials.user-dropdown')
                </ul>

            </div>
        </div>
    </div>
</nav>