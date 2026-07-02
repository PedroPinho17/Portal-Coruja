@extends('layout.app')

@section('title', 'Corujinha - Centro de Apoio Escolar')

@push('styles')
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
@endpush

@section('content')


@include('components.hero')
@include('components.about')
@include('components.services')
@include('components.training', ['formacoes' => $formacoes ?? []])
@include('components.photo', ['previewImages' => $previewImages ?? []])
@include('components.team', ['teams' => $teams ?? []])
@include('components.post')
@include('components.protocols', ['protocols' => $protocols ?? []])
@include('components.partner')
@include('components.contact')

