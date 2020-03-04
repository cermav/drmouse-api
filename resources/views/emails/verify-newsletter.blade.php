@extends('emails.layout')

@section('title', 'Doctors suggestions')
@section('content')
<p>Dobrý den,</p>
<p>prosím, potvrďte níže uvedeným linkem vaši emailovou adresu</p>
<p><a href="<?php echo $verify_link ?>"><?php echo $verify_link ?></a></p>
<p>Pokud jste se neregistrovali k odběru novinek na našich stránkách, ignorujte prosím tento email.</p>
<p>Pac a pusu,<br>Dr.Mouse</p>
@endsection