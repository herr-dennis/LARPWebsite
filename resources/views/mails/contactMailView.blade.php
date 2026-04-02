<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0; padding:0; background-color:#1a1a1a; font-family:Arial, sans-serif; color:#ffffff;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0"
                   style="background-color:#2a2a2a; border-radius:8px; padding:20px;">

                <tr>
                    <td style="text-align:center; font-size:22px; font-weight:bold; padding-bottom:20px;">
                        Neue Anfrage
                    </td>
                </tr>

                <tr>
                    <td style="padding:10px 0;">
                        <strong>Name:</strong><br>
                        {{ $name }}
                    </td>
                </tr>

                <tr>
                    <td style="padding:10px 0;">
                        <strong>Email:</strong><br>
                        {{ $email }}
                    </td>
                </tr>

                <tr>
                    <td style="padding:10px 0;">
                        <strong>Nachricht:</strong><br>
                        <div style="margin-top:5px; line-height:1.5;">
                            {!! $text !!}
                        </div>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
