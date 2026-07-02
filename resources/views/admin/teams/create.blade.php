@extends('admin.layouts.app')

@section('title', 'Nova Equipa')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Nova Equipa</h6>
      </div>
      <div class="card-body pt-3">
        <form action="{{ route('admin.teams.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @include('admin.teams._form', ['team' => $team])
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
