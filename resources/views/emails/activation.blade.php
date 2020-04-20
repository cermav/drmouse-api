@extends('emails.layout')

@section('title', 'Aktivace účtu')
@section('content')
    <p>Dobrý den,</p>
    <p>na základě Vaší žádosti vám zasílám heslo k vašemu účtu. Prosím, po příhlášení si heslo změňte.</p>
    <p><strong><?php echo $password ?></strong></p>
    <p>Děkujeme, že používáte naši aplikaci!</p>
    <p>Pac a pusu,<br>Dr.Mouse</p>
@endsection