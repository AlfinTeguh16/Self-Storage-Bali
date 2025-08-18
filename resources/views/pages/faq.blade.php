<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Self Storage Bali</title>

    {{-- SEO basics --}}
    <meta name="description" content="Self Storage Bali — secure and flexible storage solutions since 2023. Rent spaces from small to large, daily to monthly.">
    <meta name="keywords" content="self storage bali, warehouse rental bali, storage bali">

    @vite('resources/css/app.css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/bold/style.css"/>

    {{-- Optional: smooth scroll --}}
    <style>
      html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    @include('components.navbar')


    <section class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto text-center">
        <h1 class="text-3xl md:text-4xl font-bold">Frequently Asked Questions</h1>
        <p class="mt-2 text-gray-600">Find answers to common questions below.</p>
    </div>

    <div class="mt-8 max-w-3xl mx-auto space-y-3">
        @php
        $faqs = [
            ['Can I access 24/7?', 'Yes, contact us for 24/7 access options depending on your plan.'],
            ['What payment methods are supported?', 'Online payments, bank transfers, and cards are supported.'],
            ['Is my stuff secure?', 'We employ CCTV, access control, and standardized security procedures.'],
            ['Can I upgrade/downgrade size later?', 'Absolutely—subject to availability. Our team will assist you.'],
            ['What items are prohibited?', 'Perishable, illegal, hazardous, or flammable items are not allowed.'],
        ];
        @endphp

        @foreach($faqs as [$q,$a])
        <details class="group bg-white border border-gray-200 rounded-xl p-4">
            <summary class="flex cursor-pointer list-none items-center justify-between">
            <span class="font-semibold">{{ $q }}</span>
            <svg class="h-5 w-5 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.17l3.71-2.94a.75.75 0 01.94 1.16l-4.24 3.35a.75.75 0 01-.94 0L5.21 8.39a.75.75 0 01.02-1.18z"/></svg>
            </summary>
            <p class="mt-3 text-sm text-gray-600">{{ $a }}</p>
        </details>
        @endforeach
    </div>

    <div class="mt-10 text-center">
        <a href="{{ route('contact.index') }}" class="inline-flex items-center px-6 py-3 rounded-xl bg-orange-600 text-white hover:bg-orange-700">
        Still need help? Contact us
        </a>
    </div>
    </section>

    {{-- FOOTER --}}
    <footer class="py-10">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <div class="text-lg font-semibold">Self Storage Bali</div>
            <p class="mt-2 text-sm text-gray-600">Secure, flexible, and affordable storage solutions in Bali.</p>
          </div>
          <div>
            <div class="text-sm font-semibold text-gray-700">Address</div>
            <p class="mt-2 text-sm text-gray-600 flex items-start gap-2">
              <i class="ph-bold ph-map-pin-line mt-0.5"></i> Denpasar, Bali — Indonesia
            </p>
          </div>
          <div>
            <div class="text-sm font-semibold text-gray-700">Contact</div>
            <p class="mt-2 text-sm text-gray-600 flex items-center gap-2">
              <i class="ph-bold ph-envelope"></i> info@selfstoragebali.com
            </p>
            <p class="mt-1 text-sm text-gray-600 flex items-center gap-2">
              <i class="ph-bold ph-phone"></i> +62 812 3456 7890
            </p>
          </div>
        </div>
        <div class="mt-8 text-center text-xs text-gray-500">
          © {{ date('Y') }} Self Storage Bali. All rights reserved.
        </div>
      </div>
    </footer>
</body>
</html>
