<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Complete Your Payment</title>
  <!--[if mso]>
  <xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
  <![endif]-->
  <style>
    /* Base resets */
    body, table, td { margin:0; padding:0; }
    img { border:0; height:auto; line-height:100%; outline:none; text-decoration:none; }
    table { border-collapse:collapse !important; }
    body { width:100% !important; height:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }

    /* Typography */
    .body-text { color:#334155; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
    .muted { color:#64748b; font-size:14px; line-height:20px; }
    .h1 { font-size:22px; line-height:30px; font-weight:700; color:#0f172a; }
    .p  { font-size:16px; line-height:24px; color:#334155; }

    /* Card */
    .card {
      max-width:600px; width:100%;
      background:#ffffff;
      border-radius:12px;
      border:1px solid #e2e8f0;
      box-shadow:0 1px 2px rgba(16,24,40,.06);
    }

    /* Button */
    .btn {
      display:inline-block;
      padding:14px 22px;
      border-radius:8px;
      background:#2563eb;
      color:#ffffff !important;
      text-decoration:none;
      font-weight:600;
      font-size:16px;
      line-height:16px;
    }
    .btn:hover { opacity:.95; }

    /* Footer link */
    .link { color:#2563eb; text-decoration:underline; }

    /* Dark mode hint (not all clients honor this) */
    @media (prefers-color-scheme: dark) {
      body { background:#0b1220 !important; }
      .card { background:#0f172a !important; border-color:#1f2937 !important; }
      .h1 { color:#e5e7eb !important; }
      .p, .body-text { color:#cbd5e1 !important; }
      .muted { color:#94a3b8 !important; }
    }

    /* Mobile spacing */
    @media screen and (max-width: 600px) {
      .px { padding-left:20px!important; padding-right:20px!important; }
    }
  </style>
</head>
<body class="body-text" style="background:#f1f5f9;">
  <!-- Preheader (hidden preview text) -->
  <div style="display:none; max-height:0; overflow:hidden; opacity:0; mso-hide:all;">
    Your booking is confirmed — complete your payment securely.
  </div>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
    <tr>
      <td align="center">
        <!-- Header -->
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin-bottom:12px;">
          <tr>
            <td align="left" class="px" style="padding:8px 0; font-weight:700; font-size:18px; color:#0f172a; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
              {{ config('app.name') }}
            </td>
          </tr>
        </table>

        <!-- Card -->
        <table role="presentation" class="card" cellpadding="0" cellspacing="0">
          <tr>
            <td class="px" style="padding:28px;">
              <div class="h1" style="margin:0 0 8px;">Your booking is confirmed 🎉</div>
              <p class="p" style="margin:0 0 16px;">
                Please complete your payment using the secure link below to finalize your reservation.
              </p>

              <!-- Bulletproof button (works in Outlook) -->
              <!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ $paymentUrl }}" style="height:48px;v-text-anchor:middle;width:240px;" arcsize="12%" stroke="f" fillcolor="#2563eb">
                <w:anchorlock/>
                <center style="color:#ffffff;font-family:Segoe UI, Arial, sans-serif;font-size:16px;font-weight:700;">
                  Pay Now
                </center>
              </v:roundrect>
              <![endif]-->
              <!--[if !mso]><!-- -->
              <a href="{{ $paymentUrl }}" class="btn" target="_blank" style="margin:8px 0 16px; display:inline-block;">
                Pay Now
              </a>
              <!--<![endif]-->

              <p class="muted" style="margin:16px 0 0;">
                If the button doesn’t work, copy and paste this link into your browser:
              </p>
              <p class="p" style="word-break:break-all; margin:4px 0 0;">
                <a href="{{ $paymentUrl }}" target="_blank" class="link">{{ $paymentUrl }}</a>
              </p>

              <hr style="border:none; border-top:1px solid #e2e8f0; margin:24px 0;">

              <p class="muted" style="margin:0;">
                Need help? Just reply to this email and we’ll assist you.
              </p>
            </td>
          </tr>
        </table>

        <!-- Footer -->
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin-top:12px;">
          <tr>
            <td align="center" class="muted" style="padding:12px 8px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
              © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>
</body>
</html>
