@extends('emails.layout')

@section('title', 'Doctors suggestions')
@section('content')
<p>Dobrý den,</p>
<p>prosím, potvrďte níže uvedeným linkem vaši emailovou adresu</p>
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td style="word-break:break-all;">
                <a href="<?php echo $verify_link ?>" style="word-break:break-all;"><?php echo $verify_link ?></a>
            </td>
        </tr>
    </tbody>
</table>
<p>Pokud jste se neregistrovali k odběru novinek na našich stránkách, ignorujte prosím tento email.</p>
<p>Pac a pusu,<br>Dr.Mouse</p>
@endsection