<tr>
  <td class="text-xs fw-bold text-center" data-order="{{ $row->id }}" data-id="{{ $row->id }}">#{{ $row->id }}</td>
  <td>
    <div class="d-flex px-2 py-1">
      <div class="d-flex flex-column justify-content-center">
        <h6 class="mb-0 text-sm">{{ $row->nome }}</h6>
        <p class="text-xs text-secondary mb-0">{{ $row->email }}</p>
      </div>
    </div>
  </td>
  <td>
    <p class="text-xs font-weight-bold mb-0">{{ $row->permission->description ?? 'N/A' }}</p>
  </td>
  <td class="align-middle text-center text-sm">
    <span class="text-xs text-secondary font-weight-bold">{{ $row->creator ?? 'N/A' }}</span>
  </td>
  <td class="align-middle text-center">
    <span class="text-secondary text-xs font-weight-bold">{{ $row->timestamp_criacao?->format('d/m/Y H:i') ?? 'N/A' }}</span>
  </td>
  <td class="align-middle text-center">
  @php
    $currentUser = auth()->user();
    $userPermissao = $currentUser->id_permissao ?? null;
    $isAdmin = $userPermissao == 1; // ID 1 = Administrador
    $isImperador = $userPermissao == 1;
    $targetIsImperador = $row->id_permissao == 1;
    
    // Regras de autorização:
    // - Apenas administradores (ID 1) podem ver o botão de eliminar
    // - Se o utilizador a eliminar é administrador, apenas administradores podem eliminá-lo
    // - Não há múltiplos níveis de admin
    $canDelete = $isAdmin && ($isImperador || !$targetIsImperador);
  @endphp
  @if(auth()->check() && $canDelete)
    <form action="{{ route('admin.users.destroy', $row->id) }}" method="POST" class="js-delete-confirm" data-confirm="Eliminar utilizador?" data-check-relations="true" data-entity="users" data-id="{{ $row->id }}">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs p-0">Eliminar</button>
    </form>
  @elseif(auth()->check() && $isAdmin && $targetIsImperador && !$isImperador)
    <span class="text-muted text-xs" title="Apenas administradores podem eliminar outros administradores">—</span>
  @endif
  </td>
</tr>
