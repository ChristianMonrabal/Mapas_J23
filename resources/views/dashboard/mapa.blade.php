@extends('layout.layout')

@section('title', 'Mapa')

@section('content')
<div id="map"></div>

@endsection

@section('scripts')
<script src="{{ asset('js/mapa.js') }}"></script>
@endsection
