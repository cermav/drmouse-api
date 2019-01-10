@extends('layouts.page')
@section('title', 'Přidat veterinární ordinaci -')

@section('content')
<form method="POST" action="{{route('create-doctor')}}" aria-label="{{ __('New doctor') }}" novalidate="">
    @csrf
    <fieldset>
        <h3>Vaše jméno, email a krátký popis vaší ordinace.</h3>
        <div class="formRow">
            <label for="name" class="formRowTitle required {{ $errors->has('name') ? ' errorLabel' : '' }}">Vaše jméno a příjmení / název kliniky:</label>
            <input type="text" name="name" id="name" class="{{ $errors->has('name') ? 'error' : '' }}" required value="{{ old('name') }}" />
            @if ($errors->has('name'))
            <label class="error">{{ $errors->first('name') }}</label>
            @endif
        </div>
        <div class="formRow">
            <label for="email" class="formRowTitle required {{ $errors->has('email') ? ' errorLabel' : '' }}">Váš email:</label>
            <input type="email" name="email" id="email" class="{{ $errors->has('email') ? 'error' : '' }}" required value="{{ old('email') }}" />
            @if ($errors->has('email'))
            <label class="error">{{ $errors->first('email') }}</label>
            @endif
        </div>
        <div class="formRow">
            <label for="password" class="formRowTitle required {{ $errors->has('password') ? ' errorLabel' : '' }}">Zadejte heslo:</label>
            <input type="password" name="password" id="password" class="{{ $errors->has('password') ? 'error' : '' }}" required />
            @if ($errors->has('password'))
            <label class="error">{{ $errors->first('password') }}</label>
            @endif
        </div>
        <div class="formRow">
            <label for="password-confirmation" class="formRowTitle required {{ $errors->has('password_confirmation') ? ' errorLabel' : '' }}">Zadejte heslo znovu:</label>
            <input type="password" name="password_confirmation" id="password-confirmation" class="{{ $errors->has('password_confirmation') ? 'error' : '' }}" required />
            @if ($errors->has('password_confirmation'))
            <label class="error">{{ $errors->first('password_confirmation') }}</label>
            @endif
        </div>
        <div class="formRow">
            <label for="description" class="formRowTitle required {{ $errors->has('description') ? ' errorLabel' : '' }}">Zde můžete několika větami popsat vaši praxi / kliniku:</label>
            <textarea name="description" id="description" class="{{ $errors->has('description') ? 'error' : '' }}" required value="{{ old('description') }}" ></textarea>
            @if ($errors->has('description'))
            <label class="error">{{ $errors->first('description') }}</label>
            @endif
        </div>
        <div class="formRow">
            <h5 class="formRowTitle">Mluvíte anglicky?</h5>
            <input type="radio" name="speaks_english" id="speaks-english-yes" value="1"/><label for="speaks-english-yes">Ano</label>
            <input type="radio" name="speaks_english" id="speaks-english-no" value="0"/><label for="speaks-english-no">Ne</label>
        </div>
    </fieldset>
    <fieldset>
        <h3>Zadejte, prosím, vaši adresu.</h3>
        <p>Čím přesnější údaje vyplníte, tím snáze vás klienti najdou.</p>
        <div class="formRow">
            <label for="street" class="formRowTitle required {{ $errors->has('street') ? ' errorLabel' : '' }}">Ulice a číslo popisné:</label>
            <input type="text" name="street" id="street" class="{{ $errors->has('street') ? 'error' : '' }}" required value="{{ old('street') }}" />
            @if ($errors->has('street'))
            <label class="error">{{ $errors->first('street') }}</label>
            @endif
        </div>
        <div class="formRow">
            <label for="post-code" class="formRowTitle required {{ $errors->has('post_code') ? ' errorLabel' : '' }}">PSČ:</label>
            <input type="text" name="post_code" id="post-code" class="{{ $errors->has('post_code') ? 'error' : '' }}" required value="{{ old('post_code') }}" />
            @if ($errors->has('post_code'))
            <label class="error">{{ $errors->first('post_code') }}</label>
            @endif
        </div>
        <div class="formRow">
            <label for="city" class="formRowTitle required {{ $errors->has('city') ? ' errorLabel' : '' }}">Město:</label>
            <input type="text" name="city" id="city" class="{{ $errors->has('city') ? 'error' : '' }}" required value="{{ old('city') }}" />
            @if ($errors->has('city'))
            <label class="error">{{ $errors->first('city') }}</label>
            @endif
        </div>
        <div class="formRow">
            <label for="country" class="formRowTitle">Stát:</label>
            <input type="text" name="country" id="country" value="Česká republika" disabled/>
        </div>
        <div class="formRow">
            <label for="phone" class="formRowTitle required {{ $errors->has('phone') ? ' errorLabel' : '' }}">Telefonní číslo:</label>
            <input type="text" name="phone" id="phone" class="{{ $errors->has('phone') ? 'error' : '' }}" required value="{{ old('phone') }}" />
            @if ($errors->has('phone'))
            <label class="error">{{ $errors->first('phone') }}</label>
            @endif
        </div>
        <div class="formRow">
            <label for="second-phone" class="formRowTitle required {{ $errors->has('second_phone') ? ' errorLabel' : '' }}">Druhé telefonní číslo:</label>
            <input type="text" name="second_phone" id="second-phone" class="{{ $errors->has('second_phone') ? 'error' : '' }}" required value="{{ old('second_phone') }}" />
            @if ($errors->has('second_phone'))
            <label class="error">{{ $errors->first('second_phone') }}</label>
            @endif
        </div>
        <div class="formRow">
            <label for="website" class="formRowTitle">Webová stránka:</label>
            <input type="text" name="website" id="website" value="{{ old('website') }}" />
        </div>
    </fieldset>
    <fieldset>
        <h3>Zde můžete vyplnit otevírací hodiny.</h3>
        <p>V případě dělené směny, použijte tlačítko +.</p>
        @foreach ($weekdays as $weekday)
        <div class="formRow">
            <label for="weekday-{{$weekday->id}}">{{$weekday->name}}</label>
            <select id="weekday-{{$weekday->id}}" name="weekdays[{{$weekday->id}}][state]">
                @foreach ($openingHoursStates as $state)
                 <option value="{{$state->id}}">{{$state->name}}</option>
                @endforeach
            </select>
            <input type="time" name="weekdays[{{$weekday->id}}][open_at]" />
            <input type="time" name="weekdays[{{$weekday->id}}][close_at]" />
        </div>
        @endforeach
    </fieldset>
    @foreach ($propertyCategories as $category)
    @if (count($category->properties) > 0)
    <fieldset>
        <h3>{{$category->form_section_title}}</h3>
        <p>{{$category->form_section_description}}</p>
        @foreach ($category->properties->where('show_on_registration',1) as $property)
        <input type="checkbox" name="category_{{$category->id}}_properties[]" id="property-{{$property->id}}" value="{{$property->id}}" />
        <label for="property-{{$property->id}}">{{$property->name}}</label>
        @endforeach
    </fieldset>
    @endif
    @endforeach
    <fieldset>
        <h3>Kolik máte zaměstnanců?</h3>
        <p>Velikost vaší kliniky můžete přiblížit počtem zaměstnanců či si spolupracovníků.</p>
        <label for="working-doctors-count" class="formRowTitle {{ $errors->has('working_doctors_count') ? ' errorLabel' : '' }}">Doktoři:</label>
        <input type="number" name="working_doctors_count" id="working-doctors-count" class="{{ $errors->has('working_doctors_count') ? 'error' : '' }}" value="{{ old('working_doctors_count') }}" />
        @if ($errors->has('working_doctors_count'))
        <label class="error">{{ $errors->first('working_doctors_count') }}</label>
        @endif
        <label for="nurses-count" class="formRowTitle {{ $errors->has('nurses_count') ? ' errorLabel' : '' }}">Sestry:</label>
        <input type="number" name="nurses_count" id="nurses-count" class="{{ $errors->has('nurses_count') ? 'error' : '' }}" value="{{ old('nurses_count') }}" />
        @if ($errors->has('nurses_count'))
        <label class="error">{{ $errors->first('nurses_count') }}</label>
        @endif
        <label for="other-workers-count" class="formRowTitle {{ $errors->has('other_workers_count') ? ' errorLabel' : '' }}">Ostatní:</label>
        <input type="number" name="other_workers_count" id="other-workers-count" class="{{ $errors->has('other_workers_count') ? 'error' : '' }}" value="{{ old('other_workers_count') }}" />
        @if ($errors->has('other_workers_count'))
        <label class="error">{{ $errors->first('other_workers_count') }}</label>
        @endif
        <p>Pokud mate vice doktoru, uvedte prosim jejich jmena oddelena carkou.</p>
        <label for="working-doctors-names" class="formRowTitle">Jmena doktoru:</label>
        <textarea name="working_doctors_names" id="working-doctors-names" value="{{ old('working_doctors_names') }}" ></textarea>
    </fieldset>
    <fieldset>
        <h3>Můžete vyplnit ceny základních úkonů.</h3>
        <p>Ceny jsou pouze orientační a měly by majitele připravit na potřebný výdaj. Nižší cena neznamená vyšší kvalitu služeb.</p>
        @foreach ($services->where('show_on_registration', 1) as $service)
            <div class="formRow">
                <label for="service-price-{{$service->id}}" class="formRowTitle {{ $errors->has('service_prices[' . $service->id . ']') ? ' errorLabel' : '' }}">{{$service->name}}:</label>
                <input type="number" name="service_prices[{{$service->id}}]" id="service-price-{{$service->id}}" class="{{ $errors->has('service_prices[' . $service->id . ']') ? 'error' : '' }}" value="{{ old('service_prices[' . $service->id . ']') }}" />
                <span>Kč</span>
                @if ($errors->has('service_prices[' . $service->id . ']'))
                <label class="error">{{ $errors->first('service_prices[' . $service->id . ']') }}</label>
                @endif
            </div>
        @endforeach
    </fieldset>
    <div class='formRow'>
        <label for="gdpr_agreed">
            Souhlasím se zpracováním osobních údajů  
            <input type="checkbox" name="gdpr_agreed" id="gdpr_agreed" class="">
            <span class="checkmark"></span>
        </label>
    </div>
    <div class="formRow">
        <input type="submit" class="submitButton" value="Save" />
    </div>
</form>
@endsection

