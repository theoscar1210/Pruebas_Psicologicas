@extends('errors.layout')

@section('title', 'Error del servidor')
@section('code', '500')

@section('icon')
<div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center"
     style="background: #FEF2F2; border: 2px solid #FECACA">
    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="#EF4444" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
    </svg>
</div>
@endsection

@section('heading', 'Algo salió mal')

@section('description')
    Ocurrió un error inesperado en el servidor. Nuestro equipo ya fue notificado.
    Intenta de nuevo en unos momentos.
@endsection
