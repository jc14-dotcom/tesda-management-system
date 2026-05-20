@props(['url'])
{{-- Logo is attached as a CID inline image by the MessageSending listener in AppServiceProvider --}}
<tr>
    <td class="header" align="center">
        <table class="email-card" align="center" width="600" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td style="background: #2B2D7E; padding: 24px 34px; border-radius: 14px 14px 0 0;">
                    <a href="{{ $url }}" style="text-decoration: none; display: inline-block;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td style="vertical-align: middle; padding-right: 16px;">
                                    <img src="cid:alcatt-logo" alt="ALCATT" width="56" height="56" style="display: block; width: 56px; height: 56px; border: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;">
                                </td>
                                <td style="vertical-align: middle; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                                    <div style="color: #FFFFFF; font-size: 22px; line-height: 1.15; font-weight: 700; letter-spacing: -0.2px;">Alcatt Portal</div>
                                    <div style="color: #C7D0F5; font-size: 12px; line-height: 1.5; font-weight: 500; letter-spacing: 0.1px; margin-top: 2px;">Association of Laguna Competency Assessors and TVET Trainers</div>
                                </td>
                            </tr>
                        </table>
                    </a>
                </td>
            </tr>
            <tr>
                <td style="height: 3px; line-height: 3px; font-size: 1px; background: #F4B400;">&nbsp;</td>
            </tr>
        </table>
    </td>
</tr>
