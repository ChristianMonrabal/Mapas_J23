@extends('layout.layout')

@section('title', 'Gimcana')

@section('content')

<div id="map"></div>

@endsection

@section('scripts')
<script src="{{ asset('js/gimcana.js') }}"></script>
@endsection