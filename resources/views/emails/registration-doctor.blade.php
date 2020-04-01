@extends('emails.layout')

@section('title', 'Doctors suggestions')
@section('content')
<p>Dobrý den,</p>
<p>na webu drmouse.cz jsem provedli novou registraci. Prosím aktivujte váš účet kliknutím na aktivační link.</p>

<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td style="word-break:break-all;">
                <a href="<?php echo $verify_link ?>" style="word-break:break-all;"><?php echo $verify_link ?></a>
            </td>
        </tr>
    </tbody>
</table>


<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="400">
    <tbody>
        <tr>
            <th align="left">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <th class="align-left">Vaše jméno a příjmení (název kliniky)</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th class="align-left">Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th class="align-left">Ulice a číslo popisné</th>
                            <td>{{ $doctor->street }}</td>
                        </tr>
                        <tr>
                            <th class="align-left">Město</th>
                            <td>{{ $doctor->city }}</td>
                        </tr>
                        <tr>
                            <th class="align-left">PSČ</th>
                            <td>{{ $doctor->post_code }}</td>
                        </tr>
                        <tr>
                            <th class="align-left">Telefonní číslo</th>
                            <td>{{ $doctor->phone }}</td>
                        </tr>
                    </tbody>
                </table>
                </td>
        </tr>
    </tbody>
</table>

<br>
<p>Pokud jste nežádali o registraci v databazi veterinářů Dr. Mouse, pak tento email prosím ignorujte.</p>
<p>Pac a pusu,<br>Dr.Mouse</p>
@endsection