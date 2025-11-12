<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Booking — Self Storage Bali</title>

    {{-- SEO --}}
    <meta name="description" content="Fast & secure online storage booking. Flexible daily/monthly storage solutions in Bali.">
    <meta name="keywords" content="storage booking bali, rent warehouse online, self storage denpasar">

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/duotone/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #1e40af; /* dark blue — professional & trustworthy */
            --primary-light: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --gray-100: #f9fafb;
            --gray-200: #e5e7eb;
        }
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            scroll-behavior: smooth;
        }
        .card-shadow {
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05), 0 0 1px rgba(0,0,0,0.08);
        }
        .input-focus:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #F64900);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #F64900, #d75035);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px -2px rgba(30, 64, 175, 0.3);
        }
        .storage-card {
            transition: all 0.25s ease;
            border-left: 4px solid transparent;
        }
        .storage-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px -4px rgba(0,0,0,0.08);
        }
        .storage-available { border-left-color: var(--success); }
        .storage-unavailable { border-left-color: #ef4444; opacity: 0.8; }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success { background-color: rgba(16, 185, 129, 0.15); color: #059669; }
        .badge-danger { background-color: rgba(239, 68, 68, 0.15); color: #b91c1c; }
        @media (max-width: 768px) {
            .step-number { font-size: 1.1rem; width: 28px; height: 28px; }
        }
    </style>
</head>
<body>
    @include('components.navbar')

    <main class="pb-20">
        <!-- Hero -->
        <section class="bg-[#F64900] text-white pt-16 pb-20">
            <div class="container mx-auto px-4 text-center max-w-3xl">
                <div class="inline-flex items-center gap-2 bg-[#b43803] backdrop-blur-sm px-4 py-1.5 rounded-full mb-6">
                    <i class="ph-duotone ph-box text-lg"></i>
                    <span class="font-medium">Self Storage Bali</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold mb-4">Online Storage Booking</h1>
                <p class="text-lg md:text-xl opacity-90 max-w-2xl mx-auto">
                    Fast, secure, and transparent process. Store your items in 2 minutes — hassle-free.
                </p>
                <div class="mt-8 flex justify-center">
                    <div class="flex items-center gap-6 text-sm md:text-base">
                        <div class="flex items-center gap-2">
                            <i class="ph-duotone ph-check-circle text-green-300 text-lg"></i>
                            <span>Instant Confirmation</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="ph-duotone ph-lock text-blue-200 text-lg"></i>
                            <span>24/7 Security</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="ph-duotone ph-clock-counter-clockwise text-amber-200 text-lg"></i>
                            <span>Flexible Access</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Booking Form -->
        <section class="py-8 md:py-12 mt-12 px-4">
            <div class="container mx-auto max-w-5xl">
                <div class="bg-white rounded-2xl card-shadow overflow-hidden">
                    <div class="p-6 md:p-8">
                        <form action="{{ route('online.booking') }}" method="POST" id="booking-form">
                            @csrf

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Left Column: Personal Information -->
                                <div>
                                    <div class="mb-8">
                                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                            <i class="ph-duotone ph-user-circle text-[#F64900]"></i>
                                            Your Information
                                        </h2>
                                        <p class="text-gray-600 text-sm mt-1">For confirmation and access purposes.</p>
                                    </div>

                                    <div class="space-y-5">
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                            <input 
                                                type="text" 
                                                name="name" 
                                                id="name"
                                                value="{{ old('name') }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition"
                                                placeholder="e.g., John Doe"
                                                required
                                            >
                                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        </div>

                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                            <input 
                                                type="email" 
                                                name="email" 
                                                id="email"
                                                value="{{ old('email') }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition"
                                                placeholder="user@example.com"
                                                required
                                            >
                                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        </div>

                                        <div>
                                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number <span class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">+62</span>
                                                <input 
                                                    type="tel" 
                                                    name="phone" 
                                                    id="phone"
                                                    value="{{ old('phone') }}"
                                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl input-focus transition"
                                                    placeholder="81234567890"
                                                    required
                                                >
                                            </div>
                                            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                            <p class="mt-1 text-xs text-gray-500">We’ll send your confirmation and access code via WhatsApp.</p>
                                        </div>

                                        <div>
                                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address (Optional)</label>
                                            <textarea 
                                                name="address" 
                                                id="address"
                                                rows="2"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition"
                                                placeholder="Jl. Merdeka No. 123, Denpasar"
                                            >{{ old('address') }}</textarea>
                                            @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Storage & Schedule -->
                                <div>
                                    <div class="mb-6">
                                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                            <i class="ph-duotone ph-cube text-[#F64900]"></i>
                                            Select Storage & Schedule
                                        </h2>
                                        <p class="text-gray-600 text-sm mt-1">Check availability and price estimate.</p>
                                    </div>

                                    <!-- Storage List -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Available Units</label>
                                        <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                                            @php $safeStorages = isset($storages) && is_iterable($storages) ? $storages : []; @endphp
                                            @forelse($safeStorages as $storage)
                                                @php
                                                    $id = $storage['id'] ?? ($storage->id ?? null);
                                                    $size = $storage['size'] ?? ($storage->size ?? '–');
                                                    $price = $storage['price'] ?? ($storage->price ?? 0);
                                                    $isAvailable = $storage['is_available'] ?? ($storage->is_available ?? false);
                                                    $desc = $storage['description'] ?? ($storage->description ?? '');
                                                @endphp
                                                @if($id)
                                                    <label class="block storage-card p-4 rounded-xl border {{ $isAvailable ? 'border-gray-200 storage-available' : 'border-gray-100 bg-gray-50 storage-unavailable' }}">
                                                        <div class="flex items-start justify-between">
                                                            <div>
                                                                <div class="font-medium text-gray-900">{{ $size }}</div>
                                                                <div class="text-sm text-gray-600 mt-1 line-clamp-1">{{ $desc }}</div>
                                                                <div class="mt-2 flex items-center gap-2">
                                                                    <span class="font-bold text-blue-700">Rp{{ number_format($price, 0, ',', '.') }}/day</span>
                                                                    <span class="badge {{ $isAvailable ? 'badge-success' : 'badge-danger' }}">
                                                                        {{ $isAvailable ? 'Available' : 'Full' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <input 
                                                                type="radio" 
                                                                name="storage_id" 
                                                                value="{{ $id }}"
                                                                {{ old('storage_id') == $id ? 'checked' : '' }}
                                                                {{ !$isAvailable ? 'disabled' : '' }}
                                                                class="w-5 h-5 text-[#F64900] focus:ring-blue-500"
                                                                data-price="{{ $price }}"
                                                            >
                                                        </div>
                                                    </label>
                                                @endif
                                            @empty
                                                <div class="text-center py-6 bg-gray-50 rounded-xl">
                                                    <i class="ph-duotone ph-cube text-3xl text-gray-400 mb-2"></i>
                                                    <p class="text-gray-500">No units available for the selected period.</p>
                                                    <button type="button" onclick="document.getElementById('start_date').focus()" class="mt-3 text-sm text-[#F64900] hover:underline">
                                                        Change dates?
                                                    </button>
                                                </div>
                                            @endforelse
                                        </div>
                                        @error('storage_id')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Dates -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                        <div>
                                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                                            <input 
                                                type="date" 
                                                name="start_date" 
                                                id="start_date"
                                                value="{{ old('start_date', request('start_date', now()->toDateString())) }}"
                                                min="{{ now()->toDateString() }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition"
                                                required
                                            >
                                            @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date <span class="text-red-500">*</span></label>
                                            <input 
                                                type="date" 
                                                name="end_date" 
                                                id="end_date"
                                                value="{{ old('end_date', request('end_date', now()->addDays(7)->toDateString())) }}"
                                                min="{{ old('start_date', request('start_date', now()->toDateString())) }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition"
                                                required
                                            >
                                            @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    <!-- Price Estimate -->
                                    <div id="price-preview" class="bg-blue-50 rounded-xl p-4 mb-6 hidden">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="font-medium text-gray-800">Estimated Total Cost</p>
                                                <p class="text-sm text-gray-600 mt-1">Price excludes tax (if applicable).</p>
                                            </div>
                                            <div class="text-right">
                                                <p id="price-amount" class="text-xl font-bold text-blue-700">Rp0</p>
                                                <p id="price-detail" class="text-sm text-gray-600 mt-0.5"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="mb-6">
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Special Notes (Optional)</label>
                                        <textarea 
                                            name="notes" 
                                            id="notes"
                                            rows="2"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition"
                                            placeholder="e.g., Cleaning only on weekdays, electronic items, etc."
                                        >{{ old('notes') }}</textarea>
                                        @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-6 border-t mt-8 flex flex-col sm:flex-row justify-end gap-3">
                                <button 
                                    type="submit"
                                    class="btn-primary px-6 py-3 text-white font-semibold rounded-xl shadow-md text-center flex items-center justify-center gap-2 w-full sm:w-auto"
                                >
                                    <i class="ph-duotone ph-check-circle"></i>
                                    Book Now
                                </button>
                            </div>

                            <!-- Trust Badges -->
                            <div class="mt-10 pt-6 border-t border-gray-100 text-center">
                                <div class="flex flex-wrap justify-center gap-6 text-gray-600 text-sm">
                                    <div class="flex items-center gap-1.5">
                                        <i class="ph-duotone ph-shield-check text-green-500"></i>
                                        <span>Data secure & private</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="ph-duotone ph-battery-charging text-blue-500"></i>
                                        <span>CCTV & 24/7 security</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="ph-duotone ph-globe-hemisphere-west text-amber-500"></i>
                                        <span>Trusted since 2023</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Minimal Footer -->
    <footer class="bg-gray-900 text-gray-400 py-10">
        <div class="container mx-auto px-4 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-10 h-10 rounded-full bg-[#F64900] flex items-center justify-center">
                    <i class="ph-duotone ph-box text-white text-lg"></i>
                </div>
            </div>
            <p class="text-gray-300">© {{ date('Y') }} Self Storage Bali. The best storage solution on the Island of the Gods.</p>
            <p class="mt-2 text-sm">Denpasar, Bali — Indonesia | info@selfstoragebali.com</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const storageRadios = document.querySelectorAll('input[name="storage_id"]');
            const pricePreview = document.getElementById('price-preview');
            const priceAmount = document.getElementById('price-amount');
            const priceDetail = document.getElementById('price-detail');

            function updatePricePreview() {
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;
                const selectedStorage = document.querySelector('input[name="storage_id"]:checked');

                if (!startDate || !endDate || !selectedStorage) {
                    pricePreview.classList.add('hidden');
                    return;
                }

                // Date validation
                if (new Date(endDate) < new Date(startDate)) {
                    pricePreview.classList.add('hidden');
                    return;
                }

                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1; // inclusive
                const pricePerDay = parseFloat(selectedStorage.dataset.price) || 0;
                const total = diffDays * pricePerDay;

                priceAmount.textContent = `Rp${total.toLocaleString('id-ID')}`;
                priceDetail.textContent = `${diffDays} day(s) × Rp${pricePerDay.toLocaleString('id-ID')}/day`;
                pricePreview.classList.remove('hidden');
            }

            // Event listeners
            startDateInput.addEventListener('change', function () {
                endDateInput.min = this.value;
                if (new Date(endDateInput.value) < new Date(this.value)) {
                    endDateInput.value = this.value;
                }
                updatePricePreview();
            });
            endDateInput.addEventListener('change', updatePricePreview);
            storageRadios.forEach(radio => {
                radio.addEventListener('change', updatePricePreview);
            });

            // Initialize
            updatePricePreview();

            // Auto-focus name field on load
            document.getElementById('name').focus();
        });
    </script>
</body>
</html>