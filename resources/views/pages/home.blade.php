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

    {{-- HERO --}}
    <section id="home"
      class="relative w-full min-h-[90vh] flex items-center bg-cover bg-center"
      style="background-image: url('{{ asset('/img/header.jpg') }}');">
      <div class="absolute inset-0 bg-black/55"></div>
      <div class="relative container mx-auto px-4">
        <div class="max-w-3xl text-center mx-auto">
          <span class="inline-flex items-center gap-2 text-xs tracking-widest uppercase text-white/80">
            <i class="ph-bold ph-seal-check"></i> Since 2023
          </span>
          <h1 class="mt-3 text-3xl md:text-6xl font-extrabold text-white leading-tight">
            Welcome to <span class="text-orange-400">Self Storage Bali</span>
          </h1>
          <p class="mt-4 md:text-xl text-white/90">
            Secure, flexible, and affordable storage solutions. Choose your size, rent daily or monthly.
          </p>

          <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/contact') }}"
               class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-orange-600 text-white hover:bg-orange-700 transition shadow-lg shadow-orange-600/20">
              Start Booking
              <i class="ph-bold ph-arrow-right ml-2"></i>
            </a>
            <a href="#sizes"
               class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white/10 backdrop-blur text-white hover:bg-white/20 border border-white/20 transition">
              View Sizes & Prices
            </a>
          </div>

          {{-- Small stats --}}
          <div class="mt-10 grid grid-cols-3 gap-4 max-w-md mx-auto text-white/90">
            <div class="p-3 rounded-lg bg-white/10 backdrop-blur">
              <div class="text-2xl font-bold">99%</div>
              <div class="text-xs">Satisfaction</div>
            </div>
            <div class="p-3 rounded-lg bg-white/10 backdrop-blur">
              <div class="text-2xl font-bold">24/7</div>
              <div class="text-xs">Security</div>
            </div>
            <div class="p-3 rounded-lg bg-white/10 backdrop-blur">
              <div class="text-2xl font-bold">+500</div>
              <div class="text-xs">Units Available</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- FEATURES --}}
    <section id="features" class="py-16">
      <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="text-2xl md:text-3xl font-bold">Why Choose Us?</h2>
          <p class="mt-2 text-gray-600">Security, convenience, and flexibility for all your storage needs.</p>
        </div>

        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="p-6 bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div class="h-12 w-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
              <i class="ph-bold ph-shield-check text-2xl"></i>
            </div>
            <h3 class="mt-4 font-semibold">24/7 Security</h3>
            <p class="mt-1 text-sm text-gray-600">CCTV, access control, and alarms throughout the facility.</p>
          </div>

          <div class="p-6 bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div class="h-12 w-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
              <i class="ph-bold ph-clock text-2xl"></i>
            </div>
            <h3 class="mt-4 font-semibold">Flexible Access</h3>
            <p class="mt-1 text-sm text-gray-600">Rent daily, weekly, or monthly as needed.</p>
          </div>

          <div class="p-6 bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div class="h-12 w-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
              <i class="ph-bold ph-cube text-2xl"></i>
            </div>
            <h3 class="mt-4 font-semibold">Various Sizes</h3>
            <p class="mt-1 text-sm text-gray-600">From 1×1 m to large units for business and logistics.</p>
          </div>

          <div class="p-6 bg-white rounded-2xl border border-gray-200 shadow-sm">
            <div class="h-12 w-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
              <i class="ph-bold ph-credit-card text-2xl"></i>
            </div>
            <h3 class="mt-4 font-semibold">Easy Payment</h3>
            <p class="mt-1 text-sm text-gray-600">Support for various online payment methods.</p>
          </div>
        </div>
      </div>
    </section>

    {{-- SIZES & PRICING --}}
    <section id="sizes" class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="text-2xl md:text-3xl font-bold">Popular Sizes & Prices</h2>
          <p class="mt-2 text-gray-600">Sample prices. Contact us for the best offers and latest availability.</p>
        </div>

        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
          {{-- Small --}}
          <div class="rounded-2xl border border-gray-200 p-6 bg-gray-50">
            <div class="text-sm text-gray-500">Small</div>
            <h3 class="text-xl font-semibold">1×1 m</h3>
            <div class="mt-3 text-2xl font-extrabold text-gray-900">Rp 100,000<span class="text-sm font-medium text-gray-500"> /day</span></div>
            <ul class="mt-4 text-sm text-gray-600 space-y-2">
              <li class="flex items-center gap-2"><i class="ph-bold ph-check text-green-600"></i> Suitable for boxes & suitcases</li>
              <li class="flex items-center gap-2"><i class="ph-bold ph-check text-green-600"></i> Business hours access</li>
            </ul>
            <a href="{{ url('/booking') }}" class="mt-6 inline-flex w-full justify-center px-4 py-2.5 rounded-xl bg-orange-600 text-white hover:bg-orange-700 transition">Book Now</a>
          </div>

          {{-- Medium --}}
          <div class="rounded-2xl border-2 border-orange-500 p-6 bg-white shadow-lg">
            <div class="text-sm text-orange-600 font-semibold">Best Value</div>
            <h3 class="text-xl font-semibold">2×2 m</h3>
            <div class="mt-3 text-2xl font-extrabold text-gray-900">Rp 250,000<span class="text-sm font-medium text-gray-500"> /day</span></div>
            <ul class="mt-4 text-sm text-gray-600 space-y-2">
              <li class="flex items-center gap-2"><i class="ph-bold ph-check text-green-600"></i> Small furniture & archives</li>
              <li class="flex items-center gap-2"><i class="ph-bold ph-check text-green-600"></i> Flexible access</li>
            </ul>
            <a href="{{ url('/booking') }}" class="mt-6 inline-flex w-full justify-center px-4 py-2.5 rounded-xl bg-orange-600 text-white hover:bg-orange-700 transition">Book Now</a>
          </div>

          {{-- Large --}}
          <div class="rounded-2xl border border-gray-200 p-6 bg-gray-50">
            <div class="text-sm text-gray-500">Large</div>
            <h3 class="text-xl font-semibold">3×3 m</h3>
            <div class="mt-3 text-2xl font-extrabold text-gray-900">Rp 400,000<span class="text-sm font-medium text-gray-500"> /day</span></div>
            <ul class="mt-4 text-sm text-gray-600 space-y-2">
              <li class="flex items-center gap-2"><i class="ph-bold ph-check text-green-600"></i> Moving house</li>
              <li class="flex items-center gap-2"><i class="ph-bold ph-check text-green-600"></i> Ideal for business</li>
            </ul>
            <a href="{{ url('/booking') }}" class="mt-6 inline-flex w-full justify-center px-4 py-2.5 rounded-xl bg-orange-600 text-white hover:bg-orange-700 transition">Book Now</a>
          </div>
        </div>

        <p class="mt-4 text-center text-xs text-gray-500">
          *Prices may change. See real-time availability on the booking page.
        </p>
      </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section id="how" class="py-16">
      <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="text-2xl md:text-3xl font-bold">How It Works</h2>
          <p class="mt-2 text-gray-600">It's very easy to start storing your items.</p>
        </div>

        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center gap-3">
              <span class="h-10 w-10 rounded-full bg-orange-600 text-white flex items-center justify-center font-bold">1</span>
              <h3 class="font-semibold">Choose Size</h3>
            </div>
            <p class="mt-3 text-sm text-gray-600">Pick the storage size you need: small, medium, or large.</p>
          </div>

          <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center gap-3">
              <span class="h-10 w-10 rounded-full bg-orange-600 text-white flex items-center justify-center font-bold">2</span>
              <h3 class="font-semibold">Book & Pay</h3>
            </div>
            <p class="mt-3 text-sm text-gray-600">Book online and complete your payment securely.</p>
          </div>

          <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center gap-3">
              <span class="h-10 w-10 rounded-full bg-orange-600 text-white flex items-center justify-center font-bold">3</span>
              <h3 class="font-semibold">Store Your Items</h3>
            </div>
            <p class="mt-3 text-sm text-gray-600">Come to our facility and store your items with peace of mind.</p>
          </div>
        </div>
      </div>
    </section>

    {{-- TESTIMONIALS --}}
    <section id="testimonials" class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="text-2xl md:text-3xl font-bold">Customer Testimonials</h2>
          <p class="mt-2 text-gray-600">Stories from those who trust us with their belongings.</p>
        </div>

        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
          @foreach([['Dewi','“Clean and secure units. Fast booking process!”'],
                     ['Made','“Reasonable prices, friendly staff, easy access.”'],
                     ['Budi','“Very helpful during my home renovation. Recommended!”']] as [$name, $text])
          <div class="p-6 bg-gray-50 border border-gray-200 rounded-2xl">
            <div class="flex items-center gap-3">
              <div class="h-10 w-10 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold">
                {{ strtoupper(substr($name,0,1)) }}
              </div>
              <div class="font-semibold">{{ $name }}</div>
            </div>
            <p class="mt-3 text-sm text-gray-700">{{ $text }}</p>
          </div>
          @endforeach
        </div>
      </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="py-16">
      <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="text-2xl md:text-3xl font-bold">Frequently Asked Questions</h2>
          <p class="mt-2 text-gray-600">Still unsure? Find your answers here.</p>
        </div>

        <div class="mt-8 max-w-3xl mx-auto space-y-3">
          <details class="group bg-white border border-gray-200 rounded-xl p-4">
            <summary class="flex cursor-pointer list-none items-center justify-between">
              <span class="font-semibold">Can I access my unit anytime?</span>
              <i class="ph-bold ph-caret-down group-open:rotate-180 transition"></i>
            </summary>
            <p class="mt-3 text-sm text-gray-600">Flexible access depending on your package—contact us for 24/7 options.</p>
          </details>

          <details class="group bg-white border border-gray-200 rounded-xl p-4">
            <summary class="flex cursor-pointer list-none items-center justify-between">
              <span class="font-semibold">What payment methods are available?</span>
              <i class="ph-bold ph-caret-down group-open:rotate-180 transition"></i>
            </summary>
            <p class="mt-3 text-sm text-gray-600">We support online payments, bank transfers, and cards.</p>
          </details>

          <details class="group bg-white border border-gray-200 rounded-xl p-4">
            <summary class="flex cursor-pointer list-none items-center justify-between">
              <span class="font-semibold">Are my items safe?</span>
              <i class="ph-bold ph-caret-down group-open:rotate-180 transition"></i>
            </summary>
            <p class="mt-3 text-sm text-gray-600">24/7 CCTV, access control, and standard security procedures.</p>
          </details>
        </div>
      </div>
    </section>

    {{-- FINAL CTA --}}
    <section class="py-16">
      <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-orange-600 to-orange-500 rounded-3xl p-8 md:p-10 text-white">
          <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
              <h3 class="text-2xl md:text-3xl font-bold">Ready to store your items securely?</h3>
              <p class="mt-1 text-white/90">Book now & enjoy the best deals this month.</p>
            </div>
            <div class="flex items-center gap-3">
              <a href="{{ url('/booking') }}"
                 class="inline-flex items-center px-6 py-3 rounded-xl bg-white text-orange-700 hover:bg-orange-50 font-semibold shadow">
                Book Now
                <i class="ph-bold ph-arrow-right ml-2"></i>
              </a>
              <a href="{{ url('/contact') }}"
                 class="inline-flex items-center px-6 py-3 rounded-xl bg-white/10 backdrop-blur border border-white/30 text-white hover:bg-white/20">
                Contact Us
              </a>
            </div>
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
