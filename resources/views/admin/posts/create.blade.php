@extends('admin.layouts.app')

@section('title', 'Novo Post')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Novo Post</h6>
      </div>
      <div class="card-body pt-3">
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @include('admin.posts._form', ['post' => $post])
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
