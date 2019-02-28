@extends('layouts.page')
@section('title', 'Veterináři -')
@section('page-class', 'doctors')

@section('hero')
<div class='heroContent'>
    <h1>Veterináři</h1>
    <p>TODO: ADD DESCRIPTION</p>
</div>
@endsection

@section('content')
    <div>
        
    </div>
    <div class="row">
    @foreach ($doctors as $doctor)
        @php $rating = 50; @endphp
        <a href="{{route('doctor', ['slug' => $doctor->slug])}}" class="col col-4-of-12 doctorCard">
            <div class="avatar sm" style="background-image:url('{{asset('storage/' . $doctor->user->avatar)}}')"></div>
            <div class="info">
                <h2>{{ $doctor->user->name }}</h2>
                <p>{{$doctor->street}} {{$doctor->post_code}}, {{$doctor->city}}, {{$doctor->country}}</p>
                <div class="rating">
                    <div class='filling' style='width:{{$rating}}%'></div>
                </div>
            </div>
        </a>
    @endforeach
    </div>
    {{ $doctors->links() }}
@endsection