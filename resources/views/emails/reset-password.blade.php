@extends('emails.layout')

@section('title', 'Doctors suggestions')
@section('content')
<p>Dobrý den,</p>
<p>Pro obnovení hesla pokračujte níže uvedeným odkazem</p>
<p><a href="<?php echo $reset_link ?>"><?php echo $reset_link ?></a></p>
<p>Děkujeme, že používáte naši aplikaci!</p>
<p>Pac a pusu,<br>Dr.Mouse</p>
@endsection