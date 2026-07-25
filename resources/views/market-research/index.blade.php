@extends('layouts.app')

@section('title','Investigación de Mercados')

@section('content')
<div class="mb-5">
    <h1>Investigación de Mercados</h1>
    <p>En esta sección encontrarás proyectos, servicios y resultados relacionados con la investigación de mercados que realizamos en la Asociación Civil PROBIEN.</p>
</div>

<div class="mb-4">
    <h2>ENCUESTAS</h2>
    <p>Las encuestas son una de nuestras principales herramientas para conocer la opinión pública y recopilar datos fiables. Aquí puedes ver las encuestas disponibles y participar:</p>
    <a class="btn" href="{{ route('surveys.index') }}">Ver encuestas</a>
</div>

<div>
    <h3>Información sobre nuestras encuestas</h3>
    <ul class="list-group">
        <li class="list-group-item">Objetivo: Recopilar información para mejorar servicios y programas.</li>
        <li class="list-group-item">Confidencialidad: Tus respuestas son anónimas y tratadas de forma segura.</li>
        <li class="list-group-item">Duración: La mayoría de encuestas duran menos de 10 minutos.</li>
        <li class="list-group-item">Contacto: Si tienes dudas, escríbenos a contacto@probien.org (ficticio).</li>
    </ul>
</div>

@endsection
