@extends('emails.layout')

@section('title', 'Pet Notification')
@section('content')
    <p>Dobrý den,</p>
    <p>Brzy vám vyprší očkování pejska</p>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <th align="left">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <th class="align-left">Název kliniky / veterináře</th>
                        <td>{{ $test }}</td>
                    </tr>
                    <tr>
                        <th class="align-left">Adresa</th>
                    </tr>
                    <tr>
                        <th class="align-left">Něco jiného, podle čeho můžeme ordinaci dohledat?</th>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
@endsection
