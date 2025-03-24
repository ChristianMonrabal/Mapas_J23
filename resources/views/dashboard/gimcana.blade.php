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

<!-- Agregar indicador de carga -->
<div id="loading" class="loading-indicator" style="display: none;">
    <i class="fas fa-spinner fa-spin"></i> Cargando lugares...
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

<!-- Panel inferior para detalles del lugar -->
<div id="place-details" class="bottom-panel">
    <div class="panel-header">
        <h2 id="place-name"></h2>
        <button class="close-panel-btn">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="panel-content">
        <p id="place-address"></p>
        <p id="place-description"></p>
        <div id="place-tags" class="tags-container"></div>
    </div>
</div>

<div id="map"></div>

@endsection

@section('scripts')
<script src="{{ asset('js/mapa.js') }}"></script>
@endsection