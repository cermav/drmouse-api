@extends('layouts.page')
@section('title', $doctor->user->name . ' -')
@section('page-class', 'doctor')

@section('hero')
<div class='heroContent'>
    <h1>{{$doctor->user->name}}</h1>
    <p>TODO: ADD DESCRIPTION</p>
</div>
@endsection

@section('content')
@php $rating = 50; @endphp
<div>
    <div class="avatar" style="background-image:url('{{asset('storage/'.$doctor->user->avatar)}}')"></div>
    <div>
        <h2>{{$doctor->user->name}}</h2>
        <p>{{$doctor->description}}</p>
        <hr class="divider" />
        <p>{{$doctor->street}} {{$doctor->post_code}}, {{$doctor->city}}, {{$doctor->country}}</p>
        <p>Telefon: 
            <a href="tel:{{str_replace(' ', '', $doctor->phone)}}">{{$doctor->phone}}</a>
            @if ($doctor->second_phone)
            ; <a href="tel:{{str_replace(' ', '', $doctor->second_phone)}}">{{$doctor->second_phone}}</a>
            @endif
        </p>
        <p><a href="mailto:{{$doctor->user->email}}">{{$doctor->user->email}}</a></p>
        <p><a href="{{$doctor->website}}">{{$doctor->website}}</a></p>
        
        <div class="rating">
            <div class='filling' style='width:{{$rating}}%'></div>
        </div>
        <div id="map" data-lat="{{$doctor->latitude}}" data-lng="{{$doctor->longitude}}"></div>
        <a href="https://www.google.com/maps/dir/?api=1&destination={{$doctor->latitude}},{{$doctor->longitude}}">Zobrazit trasu na mapě</a>
        <div>
            <h3>Otevírací hodiny</h3>
            @foreach ($weekdays as $weekday)
            @php $weekdayOpeningHours = $doctor->user->openingHours->where('weekday_id', $weekday->id); @endphp
            <div>
                <h4>{{$weekday->name}}</h4>
                @foreach ($weekdayOpeningHours as $oh)
                @if ($oh->opening_hours_state_id === 1)
                {{(new \DateTime($oh->open_at))->format("H:i")}} - {{(new \DateTime($oh->close_at))->format("H:i")}}
                @else
                {{$oh->openingHoursState->name}}
                @endif   
                @endforeach
            </div>
            @endforeach
        </div>
        <div>
            @foreach ($propertyCategories as $category)
            @php $properties = $doctor->user->properties->where('property_category_id', $category->id)->where('is_approved', 1); @endphp
            @if (count($properties) > 0)
            <div>
                <h3>{{$category->name}}</h3>
                <div>
                    @foreach ($properties as $property)
                        {{$property->name}}
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
        </div>
        <div>
            <h3>Počet zaměstnanců</h3>
            <p>Doktorů: {{$doctor->working_doctors_count}}</p>    
            <p>Sester: {{$doctor->nurses_count}}</p>    
            <p>Ostatní: {{$doctor->other_workers_count}}</p>    
        </div>
        <div>
            <h3>Orientační ceny</h3>
            @foreach ($doctor->user->services as $service)
                @if ($service->is_approved)
                    <p>{{$service->name}}: {{$service->pivot->price}}Kč</p>
                @endif
            @endforeach
        </div>
        <div>
            <div class="row">
                @foreach ($doctor->user->photos as $photo)
                <div class="col col-4-of-12">
                    <img src="{{asset('storage/'.$photo->path)}}" />
                </div>    
                @endforeach
            </div>
        </div>
        <div>
            <h3>Vaše hodnocení:</h3>
            @if ($showScore)
            <p class="successMsg hidden">Děkujeme, Váše hodnocení bylo uloženo.</p>
            <form action="{{route('save-rating')}}" method="POST" class='form' id='rateDoctorForm'>
                @foreach ($scoreItems as $item)
                <h4>{{$item->title}}</h4>
                <ul class='ratingForm' data-item-id='{{$item->id}}' data-score='0'>
                    <li class='star' title='Velmi špatné' data-value='1'>
                        <i class='fa fa-star fa-fw'></i>
                    </li>
                    <li class='star' title='Špatné' data-value='2'>
                        <i class='fa fa-star fa-fw'></i>
                    </li>
                    <li class='star' title='Přijatelné' data-value='3'>
                        <i class='fa fa-star fa-fw'></i>
                    </li>
                    <li class='star' title='Dobré' data-value='4'>
                        <i class='fa fa-star fa-fw'></i>
                    </li>
                    <li class='star' title='Velmi dobré' data-value='5'>
                        <i class='fa fa-star fa-fw'></i>
                    </li>
                </ul>
                @endforeach
                <div class='formRow'>
                    <textarea name='comment' id='comment'></textarea>
                </div>
                <input type="hidden" id="userId" value="{{$doctor->user->id}}" />
                <input type='submit' class='button' value='Ohodnotit' />
                <p>Pro úspěšné odeslání hlasu vyhodnoťte všechny položky.</p>
                <p class='bold'>5 hvězdiček je maximum.</p>
            </form>
            @endif
            @foreach ($doctor->user->scores->where('is_approved', 1) as $score)
            @php $sum = 0; @endphp
            @foreach ($score->details as $detail)
            @php $sum += $detail->points; @endphp
            @endforeach
            @php $rating = $sum / (count($scoreItems) * 5) * 100; @endphp
            <div class="score">
                <div class="date">
                    {{(new DateTime($score->score_date))->format('d.m.Y')}}
                </div>
                <div class="rating">
                    <div class='filling' style='width:{{$rating}}%'></div>
                </div>
                @if ($score->comment)
                <span>{{$score->comment}}</span>
                @else
                <span class="italic">Žádný komentář</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection