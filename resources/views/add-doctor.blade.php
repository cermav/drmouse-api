@extends('layouts.page')
@section('title', 'Přidat veterinární ordinaci -')
@section('page-class', 'addDoctor')

@section('hero')
<div class='heroContent'>
    <h1>Přidat veterinární ordinaci</h1>
    <p>TODO: ADD DESCRIPTION</p>
</div>
@endsection

@section('content')
<form method="POST" action="{{route('create-doctor')}}" class="form" id="doctorForm" aria-label="{{ __('New doctor') }}" enctype="multipart/form-data" novalidate="">
    @csrf
    <fieldset class="formSection">
        <div class="formSectionHeader">
            <h3 class="title">Vaše jméno a krátký popis vaší ordinace.</h3>
        </div>
        <div class="formSectionContent">
            <div class="doctorDescription">
                <div class="formRow avatarRow">
                    <div class="avatar empty openModal" data-modal="avatarModal"></div>
                    <input type="hidden" name="doc_profile_pic" id="doc_profile_pic" value="">
                    <input type="hidden" name="doc_profile_pic2" id="doc_profile_pic2" value="">
                </div>
                <div class="formRow">
                    <label for="name" class="formRowTitle required {{ $errors->has('name') ? ' errorLabel' : '' }}">Vaše jméno a příjmení / název kliniky:</label>
                    <input type="text" name="name" id="name" class="{{ $errors->has('name') ? 'error' : '' }}" required value="{{ old('name') }}" />
                    @if ($errors->has('name'))
                    <label class="error">{{ $errors->first('name') }}</label>
                    @endif
                </div>
                <div class="formRow">
                    <label for="description" class="formRowTitle required {{ $errors->has('description') ? ' errorLabel' : '' }}">Zde můžete několika větami popsat vaši praxi / kliniku:</label>
                    <textarea name="description" id="description" class="{{ $errors->has('description') ? 'error' : '' }}" required value="{{ old('description') }}" ></textarea>
                    @if ($errors->has('description'))
                    <label class="error">{{ $errors->first('description') }}</label>
                    @endif
                </div>
                <div class="formRow radioRow">
                    <h5 class="formRowTitle">Mluvíte anglicky?</h5>
                    <input type="radio" name="speaks_english" id="speaks-english-yes" value="1"/><label for="speaks-english-yes">Ano</label>
                    <input type="radio" name="speaks_english" id="speaks-english-no" value="0"/><label for="speaks-english-no">Ne</label>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset class="formSection">
        <div class="formSectionHeader">
            <h3 class="title">Vaše přihlašovací údaje</h3>
        </div>
        <div class="formSectionContent">            
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
        </div>
    </fieldset>
    <fieldset class="formSection">
        <div class="formSectionHeader">
            <h3 class="title">Zadejte, prosím, vaši adresu.</h3>
            <p>Čím přesnější údaje vyplníte, tím snáze vás klienti najdou.</p>
        </div>
        <div class="formSectionContent">
            <div class="formRow col col-8-of-12">
                <label for="street" class="formRowTitle required {{ $errors->has('street') ? ' errorLabel' : '' }}">Ulice a číslo popisné:</label>
                <input type="text" name="street" id="street" class="{{ $errors->has('street') ? 'error' : '' }}" required value="{{ old('street') }}" />
                @if ($errors->has('street'))
                <label class="error">{{ $errors->first('street') }}</label>
                @endif
            </div>
            <div class="formRow col col-4-of-12">
                <label for="post-code" class="formRowTitle required {{ $errors->has('post_code') ? ' errorLabel' : '' }}">PSČ:</label>
                <input type="text" name="post_code" id="post-code" class="{{ $errors->has('post_code') ? 'error' : '' }}" required value="{{ old('post_code') }}" />
                @if ($errors->has('post_code'))
                <label class="error">{{ $errors->first('post_code') }}</label>
                @endif
            </div>
            <div class="formRow col col-6-of-12">
                <label for="city" class="formRowTitle required {{ $errors->has('city') ? ' errorLabel' : '' }}">Město:</label>
                <input type="text" name="city" id="city" class="{{ $errors->has('city') ? 'error' : '' }}" required value="{{ old('city') }}" />
                @if ($errors->has('city'))
                <label class="error">{{ $errors->first('city') }}</label>
                @endif
            </div>
            <div class="formRow col col-6-of-12">
                <label for="country" class="formRowTitle">Stát:</label>
                <input type="text" name="country" id="country" value="Česká republika" disabled/>
            </div>
            <div class="formRow col col-6-of-12">
                <label for="phone" class="formRowTitle required {{ $errors->has('phone') ? ' errorLabel' : '' }}">Telefonní číslo:</label>
                <input type="text" name="phone" id="phone" class="{{ $errors->has('phone') ? 'error' : '' }} withPreValue" required value="{{ old('phone') }}" />
                <div class="preValue">+420</div>
                @if ($errors->has('phone'))
                <label class="error">{{ $errors->first('phone') }}</label>
                @endif
            </div>
            <div class="formRow col col-6-of-12">
                <label for="second-phone" class="formRowTitle {{ $errors->has('second_phone') ? ' errorLabel' : '' }}">Druhé telefonní číslo:</label>
                <input type="text" name="second_phone" id="second-phone" class="{{ $errors->has('second_phone') ? 'error' : '' }} withPreValue" value="{{ old('second_phone') }}" />
                <div class="preValue">+420</div>
                @if ($errors->has('second_phone'))
                <label class="error">{{ $errors->first('second_phone') }}</label>
                @endif
            </div>
            <div class="formRow">
                <label for="website" class="formRowTitle">Webová stránka:</label>
                <input type="text" name="website" id="website" value="{{ old('website') }}" />
            </div>
        </div> 
    </fieldset>
    <fieldset class="formSection">
        <div class="formSectionHeader">
            <h3 class="title">Zde můžete vyplnit otevírací hodiny.</h3>
            <p>V případě dělené směny, použijte tlačítko +.</p>
        </div>
        <div class="formSectionContent">
            @foreach ($weekdays as $weekday)
            <div class="formRow asTableRow" data-weekday="{{$weekday->id}}">
                <label for="weekday-{{$weekday->id}}-0" class="formRowTitle col col-2-of-12">{{$weekday->name}}</label>
                <select id="weekday-{{$weekday->id}}-0" name="weekdays[{{$weekday->id}}][0][state]" class="weekdaySelect col col-3-of-12">
                    @foreach ($openingHoursStates as $state)
                    <option value="{{$state->id}}">{{$state->name}}</option>
                    @endforeach
                </select>
                <input type="time" name="weekdays[{{$weekday->id}}][0][open_at]" class="col col-3-of-12" />
                <input type="time" name="weekdays[{{$weekday->id}}][0][close_at]" class="col col-3-of-12" />
                <button type="button" class="actionButton col col-1-of-12 addWeekdayRow">+</button>
            </div>
            @endforeach
        </div>
    </fieldset>
    @foreach ($propertyCategories as $category)
    @if (count($category->properties) > 0)
    <fieldset class="formSection">
        <div class="formSectionHeader">
            <h3 class="title">{{$category->form_section_title}}</h3>
            <p>{{$category->form_section_description}}</p>
        </div>
        <div class="formSectionContent checkboxes">
            @foreach ($category->properties->where('show_on_registration',1) as $property)
            <div class="formRow checkboxRow">
                <input type="checkbox" name="category_{{$category->id}}_properties[]" id="property-{{$property->id}}" value="{{$property->id}}" />
                <label for="property-{{$property->id}}">{{$property->name}}</label>
            </div>
            @endforeach
            <div class="formRow col col-3-of-12 mb0">
                <input type="text" name="custom_property_{{$category->id}}" class="searchOptions" data-category="{{$category->id}}" data-type="properties" autocomplete="off" placeholder="Jiné" />
                <div class="customOptions"></div>
            </div>
        </div>    
    </fieldset>
    @endif
    @endforeach
    <fieldset class="formSection">
        <div class="formSectionHeader">
            <h3 class="title">Kolik máte zaměstnanců?</h3>
            <p>Velikost vaší kliniky můžete přiblížit počtem zaměstnanců či si spolupracovníků. Pokud mate vice doktoru, uvedte prosim jejich jmena oddelena carkou.</p>
        </div>
        <div class="formSectionContent">
            <div class="formRow col col-4-of-12">
                <label for="working-doctors-count" class="formRowTitle {{ $errors->has('working_doctors_count') ? ' errorLabel' : '' }}">Doktoři:</label>
                <input type="number" name="working_doctors_count" id="working-doctors-count" class="{{ $errors->has('working_doctors_count') ? 'error' : '' }}" value="{{ old('working_doctors_count') }}" />
                @if ($errors->has('working_doctors_count'))
                <label class="error">{{ $errors->first('working_doctors_count') }}</label>
                @endif
            </div>
            <div class="formRow col col-4-of-12">
                <label for="nurses-count" class="formRowTitle {{ $errors->has('nurses_count') ? ' errorLabel' : '' }}">Sestry:</label>
                <input type="number" name="nurses_count" id="nurses-count" class="{{ $errors->has('nurses_count') ? 'error' : '' }}" value="{{ old('nurses_count') }}" />
                @if ($errors->has('nurses_count'))
                <label class="error">{{ $errors->first('nurses_count') }}</label>
                @endif
            </div>
            <div class="formRow col col-4-of-12">
                <label for="other-workers-count" class="formRowTitle {{ $errors->has('other_workers_count') ? ' errorLabel' : '' }}">Ostatní:</label>
                <input type="number" name="other_workers_count" id="other-workers-count" class="{{ $errors->has('other_workers_count') ? 'error' : '' }}" value="{{ old('other_workers_count') }}" />
                @if ($errors->has('other_workers_count'))
                <label class="error">{{ $errors->first('other_workers_count') }}</label>
                @endif
            </div>    
            <div class="formRow">                
                <label for="working-doctors-names" class="formRowTitle">Jména doktorů:</label>
                <textarea name="working_doctors_names" id="working-doctors-names" value="{{ old('working_doctors_names') }}" ></textarea>
            </div>
        </div>        
    </fieldset>
    <fieldset class="formSection">
        <div class="formSectionHeader">
            <h3 class="title">Můžete vyplnit ceny základních úkonů.</h3>
            <p>Ceny jsou pouze orientační a měly by majitele připravit na potřebný výdaj. Nižší cena neznamená vyšší kvalitu služeb.</p>
        </div>
        <div class="formSectionContent">
            @foreach ($services as $service)
            <div class="formRow col col-6-of-12">
                <label for="service-price-{{$service->id}}" class="formRowTitle {{ $errors->has('service_prices[' . $service->id . ']') ? ' errorLabel' : '' }}">{{$service->name}}:</label>
                <input type="number" name="service_prices[{{$service->id}}]" id="service-price-{{$service->id}}" class="{{ $errors->has('service_prices[' . $service->id . ']') ? 'error' : '' }}" value="{{ old('service_prices[' . $service->id . ']') }}" />
                <div class="preValue after">Kč</div>
                @if ($errors->has('service_prices[' . $service->id . ']'))
                <label class="error">{{ $errors->first('service_prices[' . $service->id . ']') }}</label>
                @endif
            </div>
            @endforeach
            <div class="formRow col col-6-of-12">
                <label for="custom_service" class="formRowTitle">Přidat vlastní úkon:</label>
                <input type="text" name="custom_service" class="searchOptions" data-service="{{$service->id}}" data-type="services" autocomplete="off" placeholder="Jiný úkon" />
                <div class="customOptions"></div>
            </div>
        </div>
    </fieldset>
    <fieldset class="formSection">
        <div class="formSectionHeader">
            <h3 class="title">Přidejte si fotky do galerie.</h3>
            <p>Do galerie můžete nahrát fotografie, které ještě lépe přiblíží klientům vaši ordinaci.</p>
        </div>
        <div class="formSectionContent photos">
            @for ($i=0;$i<8;$i++)
            <div class="photoInput empty">
                <div class="asInput"></div>
                <input type="file" name="photos[]" accept="image/*" />
                <button type="button" class="closeButton">X</button>
            </div>
            @endfor
        </div>
    </fieldset>
    <div class='formRow singleCheckbox'>
        <label for="gdpr_agreed">
            Souhlasím se zpracováním osobních údajů  
            <input type="checkbox" name="gdpr_agreed" id="gdpr_agreed" class="">
            <span class="checkmark"></span>
        </label>
    </div>
    <div class="formRow mt40">
        <input type="submit" class="button greenButton fullWidth" id="submit_form" value="Registrovat" disabled />
    </div>
</form>
<div class="watermark"></div>
<div class="modal" id="avatarModal">
    <div class="modalInner">
        <button class="close closeModal">X</button>
        <h2>Vyberte, prosím, fotografii ze svého zařízení.</h2>
        <div class="button fileButton halfWidth modalRow">
            <input type="file" accept="image/*" id='avatarInput'>
            <span>Nahrát fotografii</span>
        </div>
        <div class="croppie modalRow"></div>
        <ul class="avatarsList modalRow">
            <li class="selectAvatar" data-avatar="{{asset('/images/profileDoctor01.png')}}" style="background-image:url('{{asset('/images/profileDoctor01.png')}}')"></li>
            <li class="selectAvatar" data-avatar="{{asset('/images/profileDoctor02.png')}}" style="background-image:url('{{asset('/images/profileDoctor02.png')}}')"></li>
            <li class="selectAvatar" data-avatar="{{asset('/images/profileDoctor03.png')}}" style="background-image:url('{{asset('/images/profileDoctor03.png')}}')"></li>
            <li class="selectAvatar" data-avatar="{{asset('/images/profileDoctor04.png')}}" style="background-image:url('{{asset('/images/profileDoctor04.png')}}')"></li>
        </ul>
        <button class="button greenButton modalRow" id='saveAvatar'>Uložit</button>
    </div>
</div>
@include ('templates.weekdayRow');
@include ('templates.customOptions');
@include ('templates.propertyInput');
@include ('templates.serviceInput');
@endsection

