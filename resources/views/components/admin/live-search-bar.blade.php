@props([
  'newRoute' => null, // route for + Novo button
])
<div class="idiomas-actions d-flex align-items-center" style="gap:.5rem;">
  @if($newRoute)
    <a href="{{ $newRoute }}" class="btn btn-new mb-0">+ Novo</a>
  @endif
</div>
