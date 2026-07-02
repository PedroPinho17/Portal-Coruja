<tr>
  <td>
    <div class="d-flex px-2 py-1">
      <div class="d-flex flex-column justify-content-center">
        <h6 class="mb-0 text-sm">{{ $row['name'] }}</h6>
      </div>
    </div>
  </td>
  <td>
    <p class="text-xs font-weight-bold mb-0">{{ $row['budget'] }}</p>
  </td>
  <td class="align-middle text-center text-sm">
    <span class="badge badge-sm bg-gradient-{{ $row['status'] === 'done' ? 'success' : ($row['status'] === 'working' ? 'info' : 'secondary') }}">{{ $row['status'] }}</span>
  </td>
  <td class="align-middle">
    <div class="progress-wrapper w-75 mx-auto">
      <div class="progress">
        <div class="progress-bar bg-gradient-info" role="progressbar" aria-valuenow="{{ $row['progress'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $row['progress'] }}%;"></div>
      </div>
    </div>
  </td>
  <td class="align-middle">
    <a href="#" class="text-secondary font-weight-bold text-xs">Edit</a>
  </td>
</tr>
