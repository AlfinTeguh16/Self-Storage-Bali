<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Payment Successful</title>
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
    .h2 { font-size:16px; line-height:24px; font-weight:600; color:#0f172a; }
    .p  { font-size:16px; line-height:24px; color:#334155; }

    /* Card */
    .card {
      max-width:600px; width:100%;
      background:#ffffff;
      border-radius:12px;
      border:1px solid #e2e8f0;
      box-shadow:0 1px 2px rgba(16,24,40,.06);
    }

    /* Success badge */
    .success-badge {
      display:inline-block;
      padding:8px 16px;
      border-radius:8px;
      background:#dcfce7;
      color:#166534;
      font-weight:600;
      font-size:14px;
    }

    /* Receipt table */
    .receipt-row {
      border-bottom: 1px solid #e2e8f0;
    }
    .receipt-label {
      color:#64748b;
      font-size:14px;
      padding:12px 0;
    }
    .receipt-value {
      color:#0f172a;
      font-size:14px;
      font-weight:500;
      padding:12px 0;
      text-align:right;
    }
    .receipt-total {
      color:#0f172a;
      font-size:18px;
      font-weight:700;
    }

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
    Your payment was successful! Here's your receipt.
  </div>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
    <tr>
      <td align="center">
        <!-- Header -->
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin-bottom:12px;">
          <tr>
            <td align="left" class="px" style="padding:8px 0; font-weight:700; font-size:18px; color:#0f172a; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
              <span>Self Storage Bali</span>
            </td>
          </tr>
        </table>

        <!-- Card -->
        <table role="presentation" class="card" cellpadding="0" cellspacing="0">
          <tr>
            <td class="px" style="padding:28px;">
              <!-- Success Icon & Title -->
              <div style="text-align:center; margin-bottom:24px;">
                <div style="width:64px; height:64px; background:#dcfce7; border-radius:50%; margin:0 auto 16px; display:flex; align-items:center; justify-content:center;">
                  <span style="font-size:32px;">✓</span>
                </div>
                <div class="h1" style="margin:0 0 8px;">Payment Successful! 🎉</div>
                <span class="success-badge">PAID</span>
              </div>

              <p class="p" style="margin:0 0 24px; text-align:center;">
                Thank you for your payment. Your booking has been confirmed.
              </p>

              <!-- Receipt Section -->
              <div style="background:#f8fafc; border-radius:8px; padding:20px; margin-bottom:24px;">
                <div class="h2" style="margin:0 0 16px;">Payment Receipt</div>
                
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                  <tr class="receipt-row">
                    <td class="receipt-label">Order ID</td>
                    <td class="receipt-value">{{ $booking->booking_ref }}</td>
                  </tr>
                  <tr class="receipt-row">
                    <td class="receipt-label">Payment Date</td>
                    <td class="receipt-value">{{ now()->format('d M Y, H:i') }}</td>
                  </tr>
                  <tr class="receipt-row">
                    <td class="receipt-label">Payment Method</td>
                    <td class="receipt-value">Credit/Debit Card</td>
                  </tr>
                  <tr class="receipt-row">
                    <td class="receipt-label">Card Number</td>
                    <td class="receipt-value">**** **** **** {{ $lastFourDigits ?? '****' }}</td>
                  </tr>
                  <tr class="receipt-row">
                    <td class="receipt-label">Storage Unit</td>
                    <td class="receipt-value">{{ $booking->storage->name ?? 'Storage Unit' }}</td>
                  </tr>
                  <tr class="receipt-row">
                    <td class="receipt-label">Duration</td>
                    <td class="receipt-value">{{ $booking->duration }} month(s)</td>
                  </tr>
                  <tr>
                    <td class="receipt-label receipt-total">Total Amount</td>
                    <td class="receipt-value receipt-total">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                  </tr>
                </table>
              </div>

              <!-- Customer Info -->
              <div style="background:#eff6ff; border-radius:8px; padding:16px; margin-bottom:24px;">
                <div class="h2" style="margin:0 0 8px;">Customer Details</div>
                <p class="p" style="margin:0;">
                  <strong>{{ $booking->customer->name ?? 'Customer' }}</strong><br>
                  {{ $booking->customer->email ?? '-' }}<br>
                  {{ $booking->customer->phone ?? '-' }}
                </p>
              </div>

              <hr style="border:none; border-top:1px solid #e2e8f0; margin:24px 0;">

              <p class="muted" style="margin:0; text-align:center;">
                This is your official payment receipt. Please keep it for your records.<br><br>
                Need help? Just reply to this email and we'll assist you.
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
