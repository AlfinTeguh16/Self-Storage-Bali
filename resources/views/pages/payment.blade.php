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
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/regular/style.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/bold/style.css"/>

    {{-- Optional: smooth scroll --}}
    <style>
      html { scroll-behavior: smooth; }
    </style>
</head>
<body style="background-image: url('{{ asset('img/bg-payment.jpg') }}'); background-repeat: repeat; background-size: 150px;">
    

<div class="min-h-screen py-3 px-3 flex items-center justify-center">
    <div class="max-w-md w-[420px] mx-auto">
        <!-- Main Card with fixed height -->
        <div class="bg-white rounded-sm shadow-xl overflow-hidden h-[640px] flex flex-col">
            <!-- Header - Dark Blue -->
            <div class="bg-[#1e3a5f] text-white px-6 py-4 flex-shrink-0">
                <h1 class="text-lg font-medium">Self Storage Bali</h1>
            </div>
            
            <!-- Amount Section -->
            <div class="px-6 py-5 flex-shrink-0">
                <div class="flex justify-between items-end">
                    <div class="flex flex-col gap-[3px]">
                        <p class="text-[21px] font-bold text-gray-900">
                            Rp{{ number_format($booking->total_price, 0, ',', '.') }} 
                            <i class="ph ph-copy text-gray-400 text-lg cursor-pointer hover:text-gray-600"></i>
                        </p>
                        <p class="text-[10px] text-gray-500 mt-2">
                            Order ID #{{ $booking->booking_ref }} 
                            <i class="ph ph-copy text-gray-400 cursor-pointer hover:text-gray-600"></i>
                        </p>
                    </div>
                    <button class="text-blue-500 text-[12px] font-medium hover:underline flex items-center gap-1" onclick="toggleDetails()">
                        Rincian <i id="rincianIcon" class="ph ph-caret-down text-xs transition-transform"></i>
                    </button>
                </div>
                
                <!-- Details (hidden by default) -->
                <div id="orderDetails" class="hidden mt-4 pt-4 border-t border-gray-200">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Customer</span>
                            <span class="text-gray-800">{{ $booking->customer->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Storage</span>
                            <span class="text-gray-800">{{ $booking->storage->size }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Periode</span>
                            <span class="text-gray-800">{{ $booking->start_date->format('d/m/Y') }} - {{ $booking->end_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Durasi</span>
                            <span class="text-gray-800">{{ $booking->total_date }} hari</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Timer -->
            <div class="px-6 py-1 bg-gray-200 text-center border-b border-gray-100 flex-shrink-0">
                <p class="text-xs text-gray-600 font-semibold">
                    Pilih dalam  <span id="timer" class="font-semibold text-gray-800">23:59:59</span>
                </p>
            </div>
            
            <!-- Scrollable Payment Methods Container -->
            <div class="flex-1 overflow-y-auto">
                <!-- Metode Pembayaran Terakhir Section -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-3">
                        <p class="text-gray-400 text-sm">Last payment method</p>
                    </div>
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center" onclick="selectPayment('creditcard')">
                        <div class="flex items-center gap-3">
                            <div class="border border-gray-200 rounded px-1 py-0.5">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="VISA" width="30" height="20" loading="lazy">
                            </div>
                            <span class="text-gray-800 font-medium">Credit/debit card</span>
                        </div>
                        <i class="ph ph-caret-right text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Semua Metode Pembayaran Section -->
                <div>
                    <div class="px-6 py-3 border-b border-gray-100">
                        <p class="text-gray-400 text-sm">All payment methods</p>
                    </div>
                
                <!-- GoPay/GoPay Later (DISABLED) -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center opacity-50" onclick="showDisabled()">
                        <div>
                            <p class="font-medium text-gray-800 mb-1">GoPay/GoPay Later</p>
                            <p class="text-xs text-gray-400 mb-2">This method is currently unavailable</p>
                            <div class="flex gap-1 items-center">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/gopay_text-dc3792bc8e707693e71dad3d2215258e7595f2143a7bba74070537d2eef1cdfe.svg" alt="GoPay" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/gopaylater_text-80d6ec859d3dbcf9494fa23646a91fedae0a9ecb98625da40e0e9ad364a89809.svg" alt="GoPayLater" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/qris-5ab65ea8ea12e00daee664042ed976a75c574fcd2fb1acd04e6cfc773d9bda54.svg" alt="QRIS" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                            </div>
                        </div>
                        <i class="ph ph-caret-right text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Virtual Account (DISABLED) -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center opacity-50" onclick="showDisabled()">
                        <div>
                            <p class="font-medium text-gray-800 mb-1">Virtual account</p>
                            <p class="text-xs text-gray-400 mb-2">This method is currently unavailable</p>
                            <div class="flex gap-1 items-center flex-wrap">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/bca-906e4db60303060666c5a10498c5a749962311037cf45e4f73866e9138dd9805.svg" alt="BCA" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/mandiri-23c931af42c624b4533ed48ac3020f2b820f20c7ad08fb9cf764140e5edbe496.svg" alt="Mandiri" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/bni-163d98085f5fe9df4068b91d64c50f5e5b347ca2ee306d27954e37b424ec4863.svg" alt="BNI" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/bri-39f5d44b1c42e70ad089fc52b909ef410d708d563119eb0da3a6abd49c4a595c.svg" alt="BRI" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/permata-b9fb2fe16efa8dab34e60b85e07c9b18e72c5dc97178351ec3c0c4f4af926102.svg" alt="Permata" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5 text-xs text-gray-400">+4</div>
                            </div>
                        </div>
                        <i class="ph ph-caret-down text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Kartu Kredit/Debit (ENABLED) -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center" onclick="selectPayment('creditcard')">
                        <div>
                            <p class="font-medium text-gray-800 mb-2">Credit/debit card</p>
                            <div class="flex gap-1 items-center">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="VISA" width="30" height="20" loading="lazy">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" width="30" height="20" loading="lazy">
                                </div>
                            </div>
                        </div>
                        <i class="ph ph-caret-right text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Google Pay (DISABLED) -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center opacity-50" onclick="showDisabled()">
                        <div>
                            <p class="font-medium text-gray-800 mb-1">Google Pay</p>
                            <p class="text-xs text-gray-400 mb-2">This method is currently unavailable</p>
                            <div class="flex gap-1 items-center">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/google-pay-9ddde73a0e3e8b16e7c518f00380c542c96dbec8b0f80363d5037d905f0bba9d.svg" alt="GooglePay" width="40" height="20" loading="lazy" class="grayscale">
                                </div>
                            </div>
                        </div>
                        <i class="ph ph-caret-down text-gray-400"></i>
                    </div>
                </div>
                
                <!-- ShopeePay/SPayLater (DISABLED) -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center opacity-50" onclick="showDisabled()">
                        <div>
                            <p class="font-medium text-gray-800 mb-1">ShopeePay/SPayLater</p>
                            <p class="text-xs text-gray-400 mb-2">This method is currently unavailable</p>
                            <div class="flex gap-1 items-center">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/shopeepay-befa05d168fe30229a3a68f8520595ceee165df888500c15502eb6f6ff26301c.svg" alt="ShopeePay" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/shopeepay-later-page-21428cdc82b5302587fd994912c8ead43c53c92efb325275279131bf42dea426.svg" alt="ShopeePayLater" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/qris-5ab65ea8ea12e00daee664042ed976a75c574fcd2fb1acd04e6cfc773d9bda54.svg" alt="QRIS" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                            </div>
                        </div>
                        <i class="ph ph-caret-right text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Dana (DISABLED) -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center opacity-50" onclick="showDisabled()">
                        <div>
                            <p class="font-medium text-gray-800 mb-1">Dana</p>
                            <p class="text-xs text-gray-400 mb-2">This method is currently unavailable</p>
                            <div class="flex gap-1 items-center">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/dana-6abe773519fb933a350cf29b9221feb814e25618d7be02d290e8ff69505cac46.svg" alt="Dana" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                            </div>
                        </div>
                        <i class="ph ph-caret-down text-gray-400"></i>
                    </div>
                </div>
                
                <!-- QRIS (DISABLED) -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center opacity-50" onclick="showDisabled()">
                        <div>
                            <p class="font-medium text-gray-800 mb-1">QRIS</p>
                            <p class="text-xs text-gray-400 mb-2">This method is currently unavailable</p>
                            <div class="flex gap-1 items-center">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/qris-5ab65ea8ea12e00daee664042ed976a75c574fcd2fb1acd04e6cfc773d9bda54.svg" alt="QRIS" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                            </div>
                        </div>
                        <i class="ph ph-caret-right text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Alfa Group (DISABLED) -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center opacity-50" onclick="showDisabled()">
                        <div>
                            <p class="font-medium text-gray-800 mb-1">Alfa Group</p>
                            <p class="text-xs text-gray-400 mb-2">This method is currently unavailable</p>
                            <div class="flex gap-1 items-center">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/alfamart-a15e4dc83b99087021fc9098678aaa53d3bcfee4908866a119f70bf6941fe46c.svg" alt="Alfamart" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/alfamidi-3eb6c42aa71fce705f0854419fcec660409915f3e38e8b76625c20d38eb20a6f.svg" alt="Alfamidi" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/dandan-5f43efcad13396b344cff0fc7455b9f69bf211f52692cd8278fffb60665581d2.svg" alt="Dandan" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                            </div>
                        </div>
                        <i class="ph ph-caret-right text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Indomaret (DISABLED) -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center opacity-50" onclick="showDisabled()">
                        <div>
                            <p class="font-medium text-gray-800 mb-1">Indomaret</p>
                            <p class="text-xs text-gray-400 mb-2">This method is currently unavailable</p>
                            <div class="flex gap-1 items-center">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/indomaret-28365c7161a49a7430984206540ac2027d84c15420435d6b3dc9ce07434ceeb8.svg" alt="Indomaret" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/isaku-cea6782927dfa62ad9c02e13fd87a1ac93b96dc5beda0621249b973856c0c5b6.svg" alt="i.saku" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                            </div>
                        </div>
                        <i class="ph ph-caret-right text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Akulaku PayLater (DISABLED) -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center opacity-50" onclick="showDisabled()">
                        <div>
                            <p class="font-medium text-gray-800 mb-1">Akulaku PayLater</p>
                            <p class="text-xs text-gray-400 mb-2">This method is currently unavailable</p>
                            <div class="flex gap-1 items-center">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/akulakupaylater-8d7474bde9eced1fcc3a6d393a2eb4572dc33b39afcfa146db4b12f537c13f2b.svg" alt="Akulaku" width="30" height="20" loading="lazy" class="grayscale">
                                </div>
                            </div>
                        </div>
                        <i class="ph ph-caret-down text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Kredivo -->
                <div class="border-b border-gray-100">
                    <div class="px-6 py-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center" onclick="selectPayment('kredivo')">
                        <div>
                            <p class="font-medium text-gray-800 mb-2">Kredivo</p>
                            <div class="flex gap-1 items-center">
                                <div class="border border-gray-200 rounded px-1 py-0.5">
                                    <img src="https://snap-assets.al-pc-id-b.cdn.gtflabs.io/snap/v4/assets/kredivo-dbfa5a001945fecd377bc895ebf148b4e06288e7ffe84aa00157a5b2fe2e0f65.svg" alt="Kredivo" width="30" height="20" loading="lazy">
                                </div>
                            </div>
                        </div>
                        <i class="ph ph-caret-down text-gray-400"></i>
                    </div>
                </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center text-xs text-gray-400 flex-shrink-0 bg-white">
                <div class="flex items-center gap-2">
                    <i class="ph ph-globe"></i>
                    <span>ID</span>
                </div>
                <div>
                    Powered by <span class="font-semibold text-gray-600">Self Storage Bali</span>
                </div>
            </div>
        </div>
        
        <!-- Back Button -->
        <div class="mt-4 text-center">
            <a href="{{ route('show.storage') }}" class="text-sm text-white/80 hover:text-white">
                ← Kembali ke halaman booking
            </a>
        </div>
    </div>
</div>

<!-- Payment Processing Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-sm w-full mx-4 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mx-auto mb-4"></div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Memproses Pembayaran</h3>
        <p class="text-sm text-gray-500" id="paymentMethodText">Menghubungkan ke layanan pembayaran...</p>
    </div>
</div>

<!-- Credit Card Form Modal -->
<div id="creditCardModal" class="fixed inset-0 hidden items-center justify-center z-50" style="background-image: url('{{ asset('img/bg-payment.jpg') }}'); background-repeat: repeat; background-size: 150px;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-[420px] mx-4 overflow-hidden h-[640px] flex flex-col">
        <!-- Header -->
        <div class="bg-[#1e3a5f] text-white px-6 py-4 flex justify-between items-center flex-shrink-0">
            <h1 class="text-lg font-medium">Self Storage Bali</h1>
            <button onclick="closeCreditCardModal()" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        
        <!-- Amount Section -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="text-2xl font-bold text-gray-900">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">Order ID #{{ $booking->booking_ref }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Pay within <span id="ccTimer" class="text-blue-500 font-semibold">23:59:30</span></p>
                    <i class="ph ph-caret-down text-gray-400 mt-2"></i>
                </div>
            </div>
        </div>
        
        <!-- Card Form -->
        <div class="flex-1 overflow-y-auto px-6 py-6">
            <h2 class="font-semibold text-gray-800 text-lg mb-6">Credit/debit card</h2>
            
            <form id="creditCardForm" method="POST" action="{{ route('payment.process', ['bookingId' => $booking->id]) }}" class="space-y-5">
                @csrf
                <!-- Card Number -->
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Card number</label>
                    <div class="relative">
                        <input type="text" 
                               id="cardNumber" 
                               name="card_number" 
                               placeholder="4811 1111 1111 1114" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-800"
                               maxlength="19"
                               oninput="formatCardNumber(this)"
                               required>
                        <img id="cardBrandIcon" src="{{ asset('img/logo_visa.png') }}" alt="VISA" class="absolute right-4 top-1/2 -translate-y-1/2 h-5 w-auto">
                    </div>
                </div>
                
                <!-- Expiration & CVV -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">Expiration date</label>
                        <input type="text" 
                               id="expiryDate" 
                               name="expiry_date" 
                               placeholder="12/24" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-800"
                               maxlength="5"
                               oninput="formatExpiry(this)"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">CVV</label>
                        <input type="password" 
                               id="cvv" 
                               name="cvv" 
                               placeholder="•••" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-800"
                               maxlength="4"
                               required>
                    </div>
                </div>
                
                <!-- Divider -->
                <div class="border-t border-gray-200 my-4"></div>
                
                <!-- Installment -->
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Choose installment</label>
                    <div class="relative">
                        <select id="installment" 
                                name="installment" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-gray-800 appearance-none bg-white">
                            <option value="0">Full payment</option>
                            <option value="3">3 months</option>
                            <option value="6" selected>6 months</option>
                            <option value="12">12 months</option>
                        </select>
                        <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex-shrink-0 bg-white">
            <!-- Payment Logos -->
            <div class="flex justify-center items-center gap-4 mb-3">
                <img src="{{ asset('img/logo_visa.png') }}" alt="Visa" class="h-4">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-6">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/40/JCB_logo.svg" alt="JCB" class="h-5">
                <div class="text-[10px] text-gray-400 border border-gray-300 rounded px-1">SafeKey</div>
            </div>
            <p class="text-xs text-gray-400 text-center mb-4">Secure payments by Self Storage Bali</p>
            
            <!-- Pay Button -->
            <button type="submit" 
                    form="creditCardForm"
                    onclick="processPayment(event)"
                    class="w-full bg-[#2d2e34] text-white py-4 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                Pay now
            </button>
        </div>
    </div>
</div>

<script>
    // Toggle order details
    function toggleDetails() {
        const details = document.getElementById('orderDetails');
        details.classList.toggle('hidden');
    }
    
    // Toggle VA options
    function toggleVA() {
        const options = document.getElementById('vaOptions');
        const icon = document.getElementById('vaIcon');
        options.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }
    
    // Toggle retail options
    function toggleRetail() {
        const options = document.getElementById('retailOptions');
        const icon = document.getElementById('retailIcon');
        options.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }
    
    // Select payment method
    function selectPayment(method) {
        // If credit card is selected, show the credit card form modal
        if (method === 'creditcard') {
            const ccModal = document.getElementById('creditCardModal');
            ccModal.classList.remove('hidden');
            ccModal.classList.add('flex');
            return;
        }
        
        const modal = document.getElementById('paymentModal');
        const methodText = document.getElementById('paymentMethodText');
        
        const methodNames = {
            'creditcard': 'Credit/Debit Card',
            'gopay': 'GoPay',
            'bca': 'BCA Virtual Account',
            'mandiri': 'Mandiri Virtual Account',
            'bni': 'BNI Virtual Account',
            'bri': 'BRI Virtual Account',
            'qris': 'QRIS',
            'shopeepay': 'ShopeePay',
            'dana': 'DANA',
            'alfamart': 'Alfamart',
            'indomaret': 'Indomaret'
        };
        
        methodText.textContent = `Menghubungkan ke ${methodNames[method] || method}...`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Simulate payment processing then redirect
        setTimeout(() => {
            // In real implementation, this would send to actual payment gateway
            // For now, simulate success and redirect to booking success page
            window.location.href = "{{ route('booking.success', ['bookingId' => $booking->id]) }}";
        }, 2000);
    }
    
    // Close credit card modal
    function closeCreditCardModal() {
        const ccModal = document.getElementById('creditCardModal');
        ccModal.classList.remove('flex');
        ccModal.classList.add('hidden');
    }
    
    // Format card number with spaces
    function formatCardNumber(input) {
        let value = input.value.replace(/\s/g, '').replace(/\D/g, '');
        let formatted = value.match(/.{1,4}/g)?.join(' ') || '';
        input.value = formatted;
        
        // Detect card brand and update icon
        const cardBrandIcon = document.getElementById('cardBrandIcon');
        if (value.startsWith('4')) {
            cardBrandIcon.src = "{{ asset('img/logo_visa.png') }}";
            cardBrandIcon.alt = 'VISA';
        } else if (value.startsWith('5') || value.startsWith('2')) {
            cardBrandIcon.src = "{{ asset('img/logo_mastercard.png') }}";
            cardBrandIcon.alt = 'Mastercard';
        } else if (value.startsWith('35')) {
            cardBrandIcon.src = "{{ asset('img/logo_jcb.png') }}";
            cardBrandIcon.alt = 'JCB';
        } else {
            cardBrandIcon.src = "{{ asset('img/logo_visa.png') }}";
            cardBrandIcon.alt = 'VISA';
        }
    }
    
    // Format expiry date MM/YY
    function formatExpiry(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        input.value = value;
    }
    
    // Process payment from credit card form
    function processPayment(event) {
        event.preventDefault();
        
        const cardNumber = document.getElementById('cardNumber').value;
        const expiryDate = document.getElementById('expiryDate').value;
        const cvv = document.getElementById('cvv').value;
        
        // Validate inputs
        if (!cardNumber || cardNumber.replace(/\s/g, '').length < 16) {
            alert('Please enter a valid card number');
            return;
        }
        if (!expiryDate || expiryDate.length < 5) {
            alert('Please enter a valid expiration date');
            return;
        }
        if (!cvv || cvv.length < 3) {
            alert('Please enter a valid CVV');
            return;
        }
        
        // Submit the form directly to server
        document.getElementById('creditCardForm').submit();
    }
    
    // Show disabled payment method alert
    function showDisabled() {
        alert('This payment method is currently unavailable.');
    }
    
    // Countdown timer
    function startTimer() {
        let hours = 23, minutes = 59, seconds = 59;
        const timerEl = document.getElementById('timer');
        
        setInterval(() => {
            if (seconds > 0) {
                seconds--;
            } else if (minutes > 0) {
                minutes--;
                seconds = 59;
            } else if (hours > 0) {
                hours--;
                minutes = 59;
                seconds = 59;
            }
            
            timerEl.textContent = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
        }, 1000);
    }
    
    startTimer();
</script>

</body>
</html>