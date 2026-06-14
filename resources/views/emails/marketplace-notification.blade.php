<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $subjectLine }}</title>
    </head>
    <body style="margin:0;background:#f7f1ec;color:#241913;font-family:Arial,sans-serif;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f7f1ec;padding:24px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border:1px solid #e8d9cc;border-radius:8px;overflow:hidden;">
                        <tr>
                            <td style="padding:24px 28px;background:#241913;color:#ffffff;">
                                <p style="margin:0;font-size:13px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;">ZassMarket</p>
                                <h1 style="margin:10px 0 0;font-size:24px;line-height:1.25;">{{ $headline }}</h1>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:26px 28px;">
                                @foreach ($lines as $line)
                                    <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#4b352a;">{{ $line }}</p>
                                @endforeach

                                @if ($facts)
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:18px 0;border-collapse:collapse;">
                                        @foreach ($facts as $label => $value)
                                            @if (filled($value))
                                                <tr>
                                                    <td style="width:38%;padding:10px 0;border-top:1px solid #efe4dc;font-size:13px;font-weight:700;color:#6b7f58;">{{ $label }}</td>
                                                    <td style="padding:10px 0;border-top:1px solid #efe4dc;font-size:14px;color:#241913;">{{ $value }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                @endif

                                @if ($actionLabel && $actionUrl)
                                    <p style="margin:24px 0 0;">
                                        <a href="{{ $actionUrl }}" style="display:inline-block;border-radius:6px;background:#241913;color:#ffffff;padding:12px 16px;text-decoration:none;font-size:14px;font-weight:700;">{{ $actionLabel }}</a>
                                    </p>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
