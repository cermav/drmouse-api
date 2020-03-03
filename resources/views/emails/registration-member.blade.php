@extends('emails.layout')

@section('title', 'Doctors suggestions')
@section('content')
    <p>Dobrý den,</p>
    <p>na webu drmouse.cz jsem provedli novou registraci. Prosím aktivujte váš účet kliknutím na aktivační link.</p>
    <p><a href="<?php echo $verify_link ?>"><?php echo $verify_link ?></a></p>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="400">
        <tbody>
        <tr>
            <th align="left">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <th class="align-left">Vaše jméno a příjmení</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th class="align-left">Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    </td>
    </tr>
    </table>

    <p>Pokud jste nežádali o registraci na stránkách Dr. Mouse, pak tento email prosím ignorujte.</p>
    <p>Pac a pusu,<br>DrMouse</p>
@endsection

<p>Dobrý den,</p>

</td>
</tr>

<!-- END MAIN CONTENT AREA -->
</table>
<!-- END CENTERED WHITE CONTAINER -->

<!-- START FOOTER -->
<div class="footer">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td class="content-block">
                <span class="apple-link">www.drmouse.cz</span>
            </td>
        </tr>
        <tr>
            <td class="content-block powered-by">
                Dr.Mouse
            </td>
        </tr>
    </table>
</div>
<!-- END FOOTER -->

</div>
</td>
<td>&nbsp;</td>
</tr>
</table>

</body>
</html>
