@extends('admin.layouts.app')

@section('title', 'Editar Protocolo Escolar')

@section('content')
    <div class="row">
      <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Editar Protocolo Escolar #{{ $protocol->id }}</h6>
      </div>
      <div class="card-body pt-3">
        <form action="{{ route('admin.protocols.update', $protocol) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          @include('admin.protocols._form', ['protocol' => $protocol])
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
