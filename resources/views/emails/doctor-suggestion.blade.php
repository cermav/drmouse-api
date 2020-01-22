@extends('emails.layout')

@section('title', 'Doctors suggestions')
@section('content')
    <p>Dobrý den,</p>
    <p>na webu drmouse.cz jsem byl doporučen nový doktor</p>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <th align="left">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <th class="align-left">Název kliniky / veterináře</th>
                        <td>{{ $name }}</td>
                    </tr>
                    <tr>
                        <th class="align-left">Adresa</th>
                        <td>{{ $address }}</td>
                    </tr>
                    <tr>
                        <th class="align-left">Něco jiného, podle čeho můžeme ordinaci dohledat?</th>
                        <td>{{ $description }}</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
@endsection
