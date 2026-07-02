<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  {{-- Permissions-Policy é definido via SecurityHeaders middleware --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Backoffice')</title>
  <link rel="icon" href="{{ asset('favicons/favicon.ico') }}">
  
  <!-- PWA Manifest -->
  <link rel="manifest" href="{{ asset('site.webmanifest') }}">
  <meta name="theme-color" content="#ffffff">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="apple-mobile-web-app-title" content="{{ config('branding.branding_name', 'Heliotextil') }} - Admin">
  <link rel="apple-touch-icon" href="{{ asset('favicons/apple-touch-icon.png') }}">



  <!-- Local fonts -->
  <link rel="stylesheet" href="{{ asset('plugins/fonts/open-sans/open-sans.css') }}" />

  <!-- Nucleo & Soft UI -->
  <link href="{{ asset('admin-assets/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('admin-assets/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <link id="pagestyle" href="{{ asset('admin-assets/assets/css/soft-ui-dashboard.min.css') }}" rel="stylesheet" />

  <!-- DataTables CSS - ordem: base primeiro, depois tema Bootstrap 5 -->
  <link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin-css/datatables-custom.css') }}" rel="stylesheet">

  <!-- Bootstrap Icons (global) -->
  <link href="{{ asset('plugins/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">

  <!-- FontAwesome Icons -->
  <link href="{{ asset('plugins/fontawesome-free-7.1.0-web/css/all.min.css') }}" rel="stylesheet">

  <!-- Sidebar Custom CSS -->
  <link href="{{ asset('admin-css/sidebar-custom.css') }}" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free-7.1.0-web/css/all.min.css') }}">

  <!-- Bootstrap File Input 5.5.4 (global) CSS -->
  <link href="{{ asset('plugins/bootstrap-fileinput-5.5.4/css/fileinput.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin-css/fileinput-custom.css') }}" rel="stylesheet">

  <!-- Bootstrap5-toggle CSS (local) -->
  <link rel="stylesheet" href="{{ asset('plugins/bootstrap5-toggle-5.1.2/css/bootstrap5-toggle.min.css') }}">

  <!-- Custom admin styles (load AFTER plugins/theme to take precedence) -->
  <link href="{{ asset('admin-assets/assets/css/custom-admin.css') }}" rel="stylesheet" />
  <link href="{{ asset('admin-css/user-admin.css') }}" rel="stylesheet" />
  <link href="{{ asset('admin-css/idiomas.css') }}" rel="stylesheet" />

  <!-- Admin responsive rules (extracted) -->
  <link href="{{ asset('admin-css/responsive-admin.css') }}" rel="stylesheet" />
  @stack('styles')
</head>

<body class="g-sidenav-show bg-gray-100">
  @include('admin.partials.sidebar')
  {{-- Backdrop for mobile sidebar --}}
  <div class="sidenav-backdrop d-lg-none" aria-hidden="true"></div>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    @include('admin.partials.navbar')

    <div class="container-fluid py-4">
      @if(session('status'))
      <div id="status-alert" class="alert alert-success text-sm" style="font-size:.75rem; transition: opacity 0.3s ease-out;">
        {{ session('status') }}
      </div>
      @endif

      @if($errors->has('delete_error'))
      <div id="error-alert" class="alert alert-danger text-sm" style="font-size:.75rem; transition: opacity 0.3s ease-out;">
        {{ $errors->first('delete_error') }}
      </div>
      @endif

      @yield('content')
    </div>
  </main>

  
  <!-- <script src="{{ asset('admin-assets/assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('admin-assets/assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('admin-assets/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin-assets/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script src="{{ asset('admin-assets/assets/js/plugins/chartjs.min.js') }}"></script>
  <script src="{{ asset('admin-assets/assets/js/soft-ui-dashboard.min.js') }}"></script> -->

  <!-- jQuery  -->
  <script src="{{ asset('plugins/jquery/jquery-3.7.1.min.js') }}"></script>

  <!-- Bootstrap JS (bundle includes Popper) -->
  <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- SweetAlert2 (local) -->
  <script src="{{ asset('plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
  <script src="{{ asset('admin-js/delete-confirm.js') }}"></script>
  
  @if(session('status'))
  <script nonce="{{ $cspNonce ?? '' }}">
    Swal.fire({
      icon: 'success',
      title: 'Sucesso!',
      text: '{{ session('status') }}',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    });
  </script>
  @endif
  
  @if(session('info'))
  <script nonce="{{ $cspNonce ?? '' }}">
    Swal.fire({
      icon: 'info',
      title: 'Informação',
      text: '{{ session('info') }}',
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    });
  </script>
  @endif
  <script src="{{ asset('admin-js/delete-relations-check.js') }}"></script>

  <!-- DataTables (jQuery version) - ordem correta: base primeiro, depois tema Bootstrap 5 -->
  <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>

  <script src="{{ asset('admin-js/datatable-init.js') }}"></script>

  <!-- Bootstrap File Input 5.5.4 (global) JS-->
  <script src="{{ asset('plugins/bootstrap-fileinput-5.5.4/js/fileinput.min.js') }}"></script>
  <script src="{{ asset('plugins/bootstrap-fileinput-5.5.4/themes/bs5/theme.min.js') }}"></script>
  <script src="{{ asset('plugins/bootstrap-fileinput-5.5.4/js/locales/pt.js') }}"></script>

  <!-- Auto init (serve para inicializar o fileinput. ) -->
  <script src="{{ asset('admin-js/fileinput-init.js') }}"></script>

  <!-- Bootstrap5-toggle JS (local) -->
  <script src="{{ asset('plugins/bootstrap5-toggle-5.1.2/js/bootstrap5-toggle.jquery.min.js') }}"></script>

  <!-- Inicialização do toggle -->
  <script src="{{ asset('admin-js/toggle-init.js') }}"></script>

  <!-- Sidebar toggle -->
  <script src="{{ asset('admin-js/sidebar-toggle.js') }}"></script>

  <!-- Auto-hide status alerts -->
  <script src="{{ asset('admin-js/alert-auto-hide.js') }}"></script>

  <!-- Multi-tab logout detection -->
  <script src="{{ asset('js/logout-handler.js') }}"></script>
  <script src="{{ asset('js/multi-tab-logout.js') }}"></script>
  
  <!-- PWA Service Worker Registration -->
  <script src="{{ asset('js/pwa-register.js') }}"></script>

  @stack('scripts')
</body>

</html>