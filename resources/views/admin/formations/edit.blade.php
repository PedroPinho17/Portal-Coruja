@extends('admin.layouts.app')

@section('title', 'Editar Formação')

@section('content')
    <div class="row">
      <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Editar Formação #{{ $formation->id }}</h6>
      </div>
      <div class="card-body pt-3">
        <form action="{{ route('admin.formations.update', $formation) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          @include('admin.formations._form', ['formation' => $formation, 'entities' => $entities])
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
