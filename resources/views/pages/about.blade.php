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


    <section class="relative bg-white">
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-3xl">
            <h1 class="text-3xl md:text-4xl font-bold">About Self Storage Bali</h1>
            <p class="mt-4 text-gray-600">
                Founded in 2023, Self Storage Bali provides secure, flexible, and affordable storage solutions for individuals and businesses.
                From short-term to long-term needs, our facility is designed to safeguard your belongings with comfort and ease.
            </p>
            </div>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-gray-50 border border-gray-200 rounded-2xl">
                <div class="text-sm text-gray-500">Mission</div>
                <h3 class="mt-1 font-semibold">Make space simple</h3>
                <p class="mt-2 text-sm text-gray-600">
                We help customers declutter life and operate smarter with seamless storage experiences.
                </p>
            </div>
            <div class="p-6 bg-gray-50 border border-gray-200 rounded-2xl">
                <div class="text-sm text-gray-500">Vision</div>
                <h3 class="mt-1 font-semibold">Trusted, modern storage</h3>
                <p class="mt-2 text-sm text-gray-600">
                To be Bali’s most reliable and modern self-storage partner.
                </p>
            </div>
            <div class="p-6 bg-gray-50 border border-gray-200 rounded-2xl">
                <div class="text-sm text-gray-500">Values</div>
                <h3 class="mt-1 font-semibold">Security & Service</h3>
                <p class="mt-2 text-sm text-gray-600">
                We prioritize safety, transparency, and excellent customer support.
                </p>
            </div>
            </div>
        </div>
        </section>

        <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <img src="{{ asset('img/cctv.jpg') }}" class="w-full h-72 object-cover rounded-2xl border border-gray-200" alt="Facility">
            <div>
                <h2 class="text-2xl font-semibold">Secure facility, friendly team</h2>
                <p class="mt-3 text-gray-600">
                We use CCTV, controlled access, and standardized procedures to protect your items.
                Our team is ready to assist—from choosing the right unit to move-in support.
                </p>
                <a href="{{ route('contact.index') }}" class="inline-flex mt-6 px-5 py-2.5 rounded-xl bg-orange-600 text-white hover:bg-orange-700">Contact Us</a>
            </div>
            </div>
        </div>
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
