<tr>
  <td data-order="{{ $row->id_categoria }}">
    <div class="d-flex px-2 py-1 align-items-center" style="gap:.75rem;">
      <div class="badge bg-secondary text-white rounded-pill" style="min-width:42px; text-align:center;">
        #{{ $row->id_categoria }}
      </div>
      <div class="d-flex flex-column justify-content-center">
        <h6 class="mb-0 text-sm">{{ $row->descricao }}</h6>
      </div>
    </div>
  </td>
  <td>
    <p class="text-xs font-weight-bold mb-0">{{ $row->idioma?->descricao ?? '------------' }}</p>
  </td>
  <td class="align-middle text-center text-sm">
    @if($row->imagem)
      <img src="{{ asset('img/orbs/'.$row->imagem) }}" alt="{{ $row->descricao }}" style="width:48px;height:48px;object-fit:contain;" />
    @else
      <span class="text-secondary text-xs">------------</span>
    @endif
  </td>
</tr>
