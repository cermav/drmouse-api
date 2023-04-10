@extends('emails.layout')

@section('title', 'Upozornění na veterinární vyšetření')
@section('content')
    <p>Dobrý den,</p>
    <p>Brzy vás čeká návštěva veterináře.</p>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <th align="left">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <th class="align-left">Název termínu</th>
                        <td>{{ $title }}</td>
                    </tr>
                    <tr>
                        <th class="align-left">Začátek vyšetření</th>
                        <td>{{ $start }}</td>
                    </tr>
                    <tr>
                        <th class="align-left">Konec vyšetření</th>
                        <td>{{ $end }}</td>
                    </tr>
                    <tr>
                        <th class="align-left">Veterina</th>
                        <td>{{ $vet }}</td>
                    </tr>
                    <tr>
                        <th class="align-left">Adresa</th>
                        <td>{{ $city }}</td>
                        <td>{{ $street }}</td>
                        <td>{{ $PSC }}</td>
                        <td>{{ $phone }}</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
@endsection
