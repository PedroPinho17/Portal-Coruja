<!-- Ícone de perfil e menu dropdown para utilizador autenticado -->
@auth
<li class="nav-item dropdown d-flex align-items-center" style="height: 40px;">
  <a class="nav-link dropdown-toggle user-dropdown-trigger d-flex align-items-center gap-2 p-0" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-height: 32px; pointer-events: auto;">
    <img src="{{ asset('img/perfil.png') }}" alt="Perfil" width="32" height="32" class="rounded-circle border border-2 user-avatar" style="margin-right: 0.5rem; pointer-events: none;">
    <span class="fw-semibold user-name" style="line-height: 32px; pointer-events: none;">{{ auth()->user()->nome ?? auth()->user()->email ?? 'Utilizador' }}</span>
  </a>
  <div class="dropdown-menu dropdown-menu-end user-dropdown-menu" aria-labelledby="userDropdown" style="margin-top: 0.2rem; min-width: 180px;">
    <div class="dropdown-arrow"></div>
    <ul class="list-unstyled mb-0">
      <li>
        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.perfil.edit') }}">
          <i class="ni ni-single-02 text-success"></i>
          <span>Perfil</span>
        </a>
      </li>
      <li><hr class="dropdown-divider my-1"></li>
      <li>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
          @csrf
          <button class="dropdown-item text-danger py-2" type="submit">Sair</button>
        </form>
      </li>
    </ul>
  </div>
</li>
@endauth
