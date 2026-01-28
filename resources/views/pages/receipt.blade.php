<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Payment Receipt — Self Storage Bali</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/duotone/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1e40af; --success: #10b981; }
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .card-shadow { box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05); }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('components.navbar')

    <main class="pb-20">
        <!-- Success Banner -->
        @if(session('success'))
        <div class="bg-green-500 text-white py-4 text-center">
            <div class="container mx-auto px-4 flex items-center justify-center gap-3">
                <i class="ph-duotone ph-check-circle text-2xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <section class="bg-gradient-to-br from-green-500 to-emerald-600 text-white pt-12 pb-16">
            <div class="container mx-auto px-4 text-center max-w-2xl">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ph-duotone ph-receipt text-3xl"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold mb-2">Payment Receipt</h1>
                <p class="text-lg opacity-90">Order #{{ $booking->booking_ref }}</p>
            </div>
        </section>

        <section class="py-8 -mt-8 px-4">
            <div class="container mx-auto max-w-2xl">
                <div class="bg-white rounded-2xl card-shadow overflow-hidden">
                    <div class="p-6 md:p-8">
                        <!-- Status Badge -->
                        <div class="text-center mb-8">
                            <span class="inline-flex items-center gap-2 bg-green-100 text-green-800 px-4 py-2 rounded-full font-semibold">
                                <i class="ph-duotone ph-check-circle"></i>
                                PAID
                            </span>
                        </div>

                        <!-- Receipt Details -->
                        <div class="space-y-6">
                            <!-- Payment Info -->
                            <div class="bg-gray-50 rounded-xl p-5">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4">
                                    <i class="ph-duotone ph-credit-card text-blue-600"></i>
                                    Payment Details
                                </h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Payment Date</p>
                                        <p class="font-medium">{{ $booking->paid_at ? $booking->paid_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Payment Method</p>
                                        <p class="font-medium">Credit/Debit Card</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Order ID</p>
                                        <p class="font-medium">{{ $booking->booking_ref }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Status</p>
                                        <p class="font-medium text-green-600">Success</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Info -->
                            <div class="bg-blue-50 rounded-xl p-5">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4">
                                    <i class="ph-duotone ph-user-circle text-blue-600"></i>
                                    Customer Information
                                </h3>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-800">{{ $booking->customer->name ?? 'Customer' }}</p>
                                    <p class="text-gray-600">{{ $booking->customer->email ?? '-' }}</p>
                                    <p class="text-gray-600">{{ $booking->customer->phone ?? '-' }}</p>
                                </div>
                            </div>

                            <!-- Storage Info -->
                            <div class="bg-amber-50 rounded-xl p-5">
                                <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4">
                                    <i class="ph-duotone ph-cube text-amber-600"></i>
                                    Storage Details
                                </h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Unit Size</p>
                                        <p class="font-medium">{{ $booking->storage->size ?? 'Storage Unit' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Price/Day</p>
                                        <p class="font-medium">Rp{{ number_format($booking->storage->price ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Start Date</p>
                                        <p class="font-medium">{{ $booking->start_date ? $booking->start_date->format('d M Y') : '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">End Date</p>
                                        <p class="font-medium">{{ $booking->end_date ? $booking->end_date->format('d M Y') : '-' }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-gray-500">Duration</p>
                                        <p class="font-medium">{{ $booking->total_date ?? $booking->duration ?? '-' }} day(s)</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="border-t-2 border-dashed border-gray-200 pt-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-800">Total Paid</span>
                                    <span class="text-2xl font-bold text-green-600">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-8 no-print">
                            <button onclick="window.print()" 
                                class="flex-1 px-5 py-3 bg-gray-100 text-gray-800 rounded-xl font-medium hover:bg-gray-200 transition text-center flex items-center justify-center gap-2">
                                <i class="ph-duotone ph-printer"></i>
                                Print Receipt
                            </button>
                            <a href="{{ route('homepage') }}" 
                               class="flex-1 px-5 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition text-center flex items-center justify-center gap-2">
                                <i class="ph-duotone ph-house"></i>
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-white rounded-2xl card-shadow mt-6 p-6 no-print">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="ph-duotone ph-info text-blue-600"></i>
                        What's Next?
                    </h3>
                    <ul class="list-disc pl-5 space-y-2 text-gray-700 text-sm">
                        <li>A confirmation email with your receipt has been sent to your email address.</li>
                        <li>Our staff will contact you via <strong>WhatsApp</strong> within 15 minutes.</li>
                        <li>Please present your <strong>Order ID</strong> upon arrival at the facility.</li>
                        <li>24/7 storage access using the code we'll send you.</li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-gray-900 text-gray-400 py-8 no-print">
        <div class="container mx-auto px-4 text-center">
            <p>© {{ date('Y') }} Self Storage Bali. Thank you for trusting us.</p>
        </div>
    </footer>
</body>
</html>
