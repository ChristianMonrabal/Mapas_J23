@extends('layout.layout')

@section('title', 'Mapa')

@section('content')

<div class="container">

</div>

<div class="search-bar">
    <button id="sidebar-btn" class="sidebar-btn">
        <i class="fas fa-bars"></i>
    </button>    
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Busca un sitio">
        <button id="searchButton" class="search-btn">
            <i class="fas fa-search"></i>
        </button>
    </div>
</div>

<!-- Añadir el contenedor de tags -->
<div class="tags-filter">
    <div class="tags-scroll" id="tagsContainer">
        <!-- Los tags se cargarán aquí dinámicamente -->
    </div>
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
        <div>
            <a href="/logout" class="btn btn-danger btn-sidebar">
                <i class="fa-solid fa-right-from-bracket"> Cerrar sesion</i></a>
        </div>
        <div>
            <a href="/groups" class="btn btn-primary btn-sidebar">
                <i class="fa-solid fa-user-group"> Grupos</i></a>
        </div>
    </div>

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
        <div class="place-image-container">
            <img id="place-image" src="" alt="">
        </div>
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
