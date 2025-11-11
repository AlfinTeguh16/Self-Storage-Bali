<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Booking Berhasil — Self Storage Bali</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/duotone/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1e40af; --success: #10b981; }
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .card-shadow { box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-gray-50">
    @include('components.navbar')

    <main class="pb-20">
        <section class="bg-gradient-to-br from-green-500 to-emerald-600 text-white pt-16 pb-20">
            <div class="container mx-auto px-4 text-center max-w-2xl">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ph-duotone ph-check-circle text-3xl"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold mb-3">Booking Berhasil!</h1>
                <p class="text-lg opacity-90">
                    Terima kasih, <strong>{{ $booking->customer->name }}</strong>.  
                    Kami telah mengirim detail ke WhatsApp Anda.
                </p>
            </div>
        </section>

        <section class="py-8 -mt-12 px-4">
            <div class="container mx-auto max-w-3xl">
                <div class="bg-white rounded-2xl card-shadow overflow-hidden">
                    <div class="p-6 md:p-8">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center gap-2 bg-green-100 text-green-800 px-4 py-2 rounded-full">
                                <i class="ph-duotone ph-star text-lg"></i>
                                <span class="font-medium">Kode Booking: <span class="font-bold">{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div class="bg-blue-50 rounded-xl p-5">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-3">
                                    <i class="ph-duotone ph-user-circle text-blue-600"></i>
                                    Data Pelanggan
                                </h3>
                                <p class="font-medium">{{ $booking->customer->name }}</p>
                                <p class="text-gray-600">{{ $booking->customer->phone }}</p>
                                @if($booking->customer->address)
                                    <p class="text-gray-600 text-sm mt-1">{{ $booking->customer->address }}</p>
                                @endif
                            </div>

                            <div class="bg-amber-50 rounded-xl p-5">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-3">
                                    <i class="ph-duotone ph-cube text-amber-600"></i>
                                    Detail Storage
                                </h3>
                                <p class="font-medium">{{ $booking->storage->size }}</p>
                                <p class="text-gray-600">Rp{{ number_format($booking->storage->price, 0, ',', '.') }}/hari</p>
                                <p class="text-sm text-gray-500 mt-1">{{ $booking->storage->description ?? '-' }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-5 md:col-span-2">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-3">
                                    <i class="ph-duotone ph-calendar text-gray-600"></i>
                                    Jadwal & Pembayaran
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Mulai</p>
                                        <p class="font-medium">{{ $booking->start_date->translatedFormat('d F Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Selesai</p>
                                        <p class="font-medium">{{ $booking->end_date->translatedFormat('d F Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Durasi</p>
                                        <p class="font-medium">{{ $booking->total_date }} hari</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Total</p>
                                        <p class="font-bold text-xl text-green-700">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-xl p-5 mb-8">
                            <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="ph-duotone ph-whatsapp-logo text-green-600"></i>
                                Langkah Selanjutnya
                            </h3>
                            <ul class="list-disc pl-5 space-y-2 text-gray-700">
                                <li>Petugas akan menghubungi Anda via <strong>WhatsApp</strong> dalam 15 menit.</li>
                                <li>Silakan tunjukkan <strong>Kode Booking</strong> saat tiba di lokasi.</li>
                                <li>Akses storage 24/7 dengan kode yang dikirim.</li>
                            </ul>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('online.booking.form') }}" 
                               class="flex-1 px-5 py-3 bg-white border border-gray-300 text-gray-800 rounded-xl font-medium hover:bg-gray-50 transition text-center">
                                Booking Lagi
                            </a>
                            <a href="https://wa.me/6281234567890?text=Halo%20SSB,%20saya%20booking%20dengan%20kode%20{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}"
                               class="flex-1 px-5 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition text-center flex items-center justify-center gap-2">
                                <i class="ph-duotone ph-whatsapp-logo"></i>
                                Hubungi via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-gray-900 text-gray-400 py-8">
        <div class="container mx-auto px-4 text-center">
            <p>© {{ date('Y') }} Self Storage Bali. Terima kasih telah mempercayai kami.</p>
        </div>
    </footer>

    <script>
        // Auto-hide navbar on scroll (opsional)
        document.addEventListener('DOMContentLoaded', () => {
            let lastScroll = 0;
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                window.addEventListener('scroll', () => {
                    const currentScroll = window.pageYOffset;
                    if (currentScroll <= 0) {
                        navbar.classList.remove('translate-y-[-100%]');
                        return;
                    }
                    if (currentScroll > lastScroll && currentScroll > 50) {
                        navbar.classList.add('translate-y-[-100%]');
                    } else {
                        navbar.classList.remove('translate-y-[-100%]');
                    }
                    lastScroll = currentScroll;
                });
            }
        });
    </script>
</body>
</html>