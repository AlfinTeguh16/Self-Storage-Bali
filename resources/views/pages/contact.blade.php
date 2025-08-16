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
            <h1 class="text-3xl md:text-4xl font-bold">Contact Us</h1>
            <p class="mt-2 text-gray-600">Questions, quotes, or custom plans—we’re here to help.</p>
        </div>

        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="font-semibold">Send a Message</h3>

            @if(session('success'))
                <div class="mt-4 p-3 rounded-md bg-green-50 text-green-700 text-sm">
                {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                <label class="text-sm text-gray-600">Name</label>
                <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border-gray-300" required>
                @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-lg border-gray-300" required>
                    @error('email')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600">Phone (optional)</label>
                    <input name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border-gray-300">
                    @error('phone')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
                </div>

                <div>
                <label class="text-sm text-gray-600">Message</label>
                <textarea name="message" rows="5" class="mt-1 w-full rounded-lg border-gray-300" required>{{ old('message') }}</textarea>
                @error('message')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <button class="inline-flex items-center px-5 py-2.5 rounded-xl bg-orange-600 text-white hover:bg-orange-700">
                Send Message
                </button>
            </form>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="font-semibold">Our Office</h3>
            <p class="mt-2 text-sm text-gray-600">Denpasar, Bali — Indonesia</p>
            <p class="mt-1 text-sm text-gray-600">Email: info@selfstoragebali.com</p>
            <p class="mt-1 text-sm text-gray-600">Phone: +62 812 3456 7890</p>

            <div class="mt-6">
                <img src="{{ asset('img/map.jpg') }}" alt="Map" class="w-full h-56 object-cover rounded-xl border border-gray-200">
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
