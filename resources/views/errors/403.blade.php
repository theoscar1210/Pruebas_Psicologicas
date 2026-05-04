@extends('errors.layout')

@section('title', 'Acceso restringido')
@section('code', '403')

@section('icon')
<div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center"
     style="background: #FEF3C7; border: 2px solid #FDE68A">
    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="#D97706" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
    </svg>
</div>
@endsection

@section('heading', 'Sección restringida')

@section('description')
    Tu rol actual no tiene permisos para ver esta página.
    Si crees que esto es un error, contacta al administrador del sistema.
@endsection
