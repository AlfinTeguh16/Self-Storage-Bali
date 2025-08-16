<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Self Storage Bali</title>

    {{-- SEO basics --}}
    <meta name="description" content="Self Storage Bali — solusi penyimpanan aman dan fleksibel sejak 2023. Sewa ruang mulai dari ukuran kecil hingga besar, harian hingga bulanan.">
    <meta name="keywords" content="self storage bali, sewa gudang bali, penyimpanan barang bali">

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
        <h1 class="text-3xl md:text-4xl font-bold">Units & Pricing</h1>
        <p class="mt-2 text-gray-600">Choose the size that suits your needs. Daily, weekly, or monthly.</p>
    </div>

    <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-gray-200 p-6 bg-gray-50">
        <div class="text-sm text-gray-500">Small</div>
        <h3 class="text-xl font-semibold">1×1 m</h3>
        <div class="mt-3 text-2xl font-extrabold">Rp 100.000<span class="text-sm font-medium text-gray-500"> /day</span></div>
        <ul class="mt-4 text-sm text-gray-600 space-y-2">
            <li>Boxes, luggage</li>
            <li>Work-hour access</li>
        </ul>
        <a href="{{ url('/booking') }}" class="mt-6 inline-flex w-full justify-center px-4 py-2.5 rounded-xl bg-orange-600 text-white hover:bg-orange-700">Book Now</a>
        </div>

        <div class="rounded-2xl border-2 border-orange-500 p-6 bg-white shadow-lg">
        <div class="text-sm text-orange-600 font-semibold">Best Value</div>
        <h3 class="text-xl font-semibold">2×2 m</h3>
        <div class="mt-3 text-2xl font-extrabold">Rp 250.000<span class="text-sm font-medium text-gray-500"> /day</span></div>
        <ul class="mt-4 text-sm text-gray-600 space-y-2">
            <li>Small furniture, archive</li>
            <li>Flexible access</li>
        </ul>
        <a href="{{ url('/booking') }}" class="mt-6 inline-flex w-full justify-center px-4 py-2.5 rounded-xl bg-orange-600 text-white hover:bg-orange-700">Book Now</a>
        </div>

        <div class="rounded-2xl border border-gray-200 p-6 bg-gray-50">
        <div class="text-sm text-gray-500">Large</div>
        <h3 class="text-xl font-semibold">3×3 m</h3>
        <div class="mt-3 text-2xl font-extrabold">Rp 400.000<span class="text-sm font-medium text-gray-500"> /day</span></div>
        <ul class="mt-4 text-sm text-gray-600 space-y-2">
            <li>Moving house</li>
            <li>Great for business</li>
        </ul>
        <a href="{{ url('/booking') }}" class="mt-6 inline-flex w-full justify-center px-4 py-2.5 rounded-xl bg-orange-600 text-white hover:bg-orange-700">Book Now</a>
        </div>
    </div>

    <p class="mt-4 text-center text-xs text-gray-500">*Prices may change. For real-time availability and offers, check the booking page.</p>
    </section>

    {{-- FOOTER --}}
    <footer class="py-10">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <div class="text-lg font-semibold">Self Storage Bali</div>
            <p class="mt-2 text-sm text-gray-600">Solusi penyimpanan aman, fleksibel, dan terjangkau di Bali.</p>
          </div>
          <div>
            <div class="text-sm font-semibold text-gray-700">Alamat</div>
            <p class="mt-2 text-sm text-gray-600 flex items-start gap-2">
              <i class="ph-bold ph-map-pin-line mt-0.5"></i> Denpasar, Bali — Indonesia
            </p>
          </div>
          <div>
            <div class="text-sm font-semibold text-gray-700">Kontak</div>
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
