@extends('admin.layouts.app')

@section('title', 'Nova Entidade')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Nova Entidade</h6>
      </div>
      <div class="card-body pt-3">
        <form action="{{ route('admin.entities.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @include('admin.entities._form', ['entity' => $entity])
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
