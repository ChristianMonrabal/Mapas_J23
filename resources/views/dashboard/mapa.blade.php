@extends('layout.layout')

@section('title', 'Mapa')

@section('content')

<div class="container">

</div>

<div class="search-bar">
    <button id="sidebar-btn" class="sidebar-btn">
        <i class="fas fa-bars"></i>
    </button>    
    <input type="text" id="searchInput" placeholder="Busca un sitio">
</div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-content">
        <div>
            <h1>OnlyMaps</h1>
            <img src="{{ asset('img/icon.png') }}" alt="OnlyMaps">
        </div>
    </div>
    <!-- Aquí puedes añadir el contenido del sidebar -->
</div>

<div id="map"></div>

@endsection

@section('scripts')
<script src="{{ asset('js/mapa.js') }}"></script>
@endsection
