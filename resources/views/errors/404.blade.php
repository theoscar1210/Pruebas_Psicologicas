@extends('errors.layout')

@section('title', 'Página no encontrada')
@section('code', '404')

@section('icon')
<div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center bg-blue-50 border-2 border-blue-200">
    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="#3B82F6" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0015.803 15.803z"/>
    </svg>
</div>
@endsection

@section('heading', 'Página no encontrada')

@section('description')
    La página que buscas no existe o fue movida a otra dirección.
    Verifica la URL o regresa al inicio.
@endsection
