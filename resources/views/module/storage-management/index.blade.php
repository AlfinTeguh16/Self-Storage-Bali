@extends('layouts.master')
@section('title', 'Storage Management')
@section('content')

<h1 class="text-3xl font-bold mb-6 text-gray-800">Storage Management</h1>

@foreach($storages as $size => $storageGroup)
    <h2 class="text-xl font-semibold mt-6 mb-4 text-slate-700 flex items-center gap-2">
        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/>
        </svg>
        Size: {{ $size }}
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-5">
        @foreach($storageGroup as $storage)
            @php
                $management = $storage->storageManagement->first();
                $booking = $management?->booking;

                $bookingId = $management?->booking_id;
                $endDate = $booking?->end_date; // ← Ambil dari booking, bukan storage_management
                $status = $management?->status ?? 'available';

                // Default (akan di-override oleh JS jika overdue)
                $statusIcon = $bookingId ? '📅' : '✔';
                $statusText = $bookingId ? 'Booked' : 'Available';
                $bgColor = $bookingId
                    ? 'bg-gradient-to-br from-red-300 to-red-400 text-white'
                    : 'bg-gradient-to-br from-green-200 to-green-300 text-gray-800';

                $lastCleanHuman = $management?->last_clean
                    ? \Illuminate\Support\Carbon::parse($management->last_clean)->toFormattedDateString()
                    : 'Never Cleaned';
                $lastCleanValue = $management?->last_clean
                    ? \Illuminate\Support\Carbon::parse($management->last_clean)->format('Y-m-d')
                    : null;
            @endphp

            <div x-data="{ open:false }" 
                 class="relative"
                 data-booking-id="{{ $bookingId }}"
                 data-end-date="{{ $endDate }}"
                 data-management-id="{{ $management?->id }}"
            >
                {{-- CARD --}}
                <div
                    role="button" tabindex="0"
                    class="storage-card p-4 rounded-2xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1
                           {{ $bgColor }} {{ $management ? 'cursor-pointer' : 'cursor-not-allowed opacity-80' }}"
                    @if($management)
                        @click="open = true; document.documentElement.classList.add('overflow-hidden')"
                        @keydown.enter.prevent="open = true; document.documentElement.classList.add('overflow-hidden')"
                    @else
                        title="No management data available"
                    @endif
                >
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-lg font-semibold status-icon">
                            {{ $statusIcon }} <span class="status-text">{{ $statusText }}</span>
                        </span>
                        <span class="text-sm bg-white/20 px-3 py-1 rounded-full">{{ $size }}</span>
                    </div>

                    <p class="text-sm line-clamp-2">
                        {{ $storage->description ?? 'Tidak ada deskripsi' }}
                    </p>

                    <p class="text-xs mt-3">
                        <strong>Last Clean:</strong> {{ $lastCleanHuman }}
                    </p>
                </div>

                {{-- MODAL --}}
                @if($management)
                <div
                    x-show="open"
                    x-transition.opacity
                    @keydown.escape.window="open = false; document.documentElement.classList.remove('overflow-hidden')"
                    @click.self="open = false; document.documentElement.classList.remove('overflow-hidden')"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    aria-modal="true"
                    role="dialog"
                >
                    <div
                        x-show="open"
                        x-transition.scale.origin.center
                        class="w-full max-w-md"
                    >
                        <x-card title="Update Last Clean" class="relative">
                            <button
                                @click="open = false; document.documentElement.classList.remove('overflow-hidden')"
                                class="absolute top-3 right-3 text-gray-500 hover:text-gray-800"
                                aria-label="Close"
                            >✖</button>

                            <form method="POST" action="{{ route('storage-management.last-clean', $management->id) }}" class="space-y-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label class="block text-sm font-medium text-gray-700" for="lastClean-{{ $management->id }}">
                                        Tanggal Last Clean <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="lastClean-{{ $management->id }}"
                                        type="date"
                                        name="last_clean"
                                        value="{{ $lastCleanValue }}"
                                        class="w-full border rounded-lg p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        required
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700" for="cost-{{ $management->id }}">
                                        Biaya Kebersihan (Optional)
                                    </label>
                                    <input
                                        id="cost-{{ $management->id }}"
                                        type="number"
                                        name="cost"
                                        placeholder="0"
                                        class="w-full border rounded-lg p-2 mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Isi jika ingin mencatat pengeluaran otomatis.</p>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button type="button"
                                            @click="open = false; document.documentElement.classList.remove('overflow-hidden')"
                                            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </x-card>
                    </div>
                </div>
                @endif
            </div>
        @endforeach
    </div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
    const now = new Date();
    const cards = document.querySelectorAll('.storage-card');

    cards.forEach(card => {
        const container = card.closest('div[data-booking-id]');
        const bookingId = container?.dataset.bookingId;
        const endDateStr = container?.dataset.endDate;

        // Jika tidak ada booking_id → Available
        if (!bookingId || bookingId === 'null' || bookingId === '') {
            card.className = card.className
                .replace(/from-(red|blue|green)-\d{3}/g, '')
                .replace(/to-(red|blue|green)-\d{3}/g, '');
            card.classList.add('from-green-200', 'to-green-300');
            card.querySelector('.status-icon').innerHTML = '✔ <span class="status-text">Available</span>';
            return;
        }

        // Jika ada booking_id dan ada end_date
        if (endDateStr && endDateStr !== 'null') {
            const endDate = new Date(endDateStr);
            const isOverdue = endDate < now;

            if (isOverdue) {
                // Overdue → Biru
                card.className = card.className
                    .replace(/from-(red|blue|green)-\d{3}/g, '')
                    .replace(/to-(red|blue|green)-\d{3}/g, '');
                card.classList.add('from-blue-300', 'to-blue-500');
                card.querySelector('.status-icon').innerHTML = '⚠️ <span class="status-text">Overdue</span>';
            }
           
        }
    });
});
</script>

@endsection