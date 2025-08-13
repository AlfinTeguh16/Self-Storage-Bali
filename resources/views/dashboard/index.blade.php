@extends('layouts.master')
@section('content')
    <h1>Dashboard</h1>
    <p>Welcome to the dashboard!</p>

    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
            <h2 class="font-bold text-2xl mb-3">Available Storages</h2>

            <ul class="space-y-2">
                @forelse($storageAvailable as $storage)
                    <li class="border border-gray-200 py-2 hover:bg-gray-100 rounded-lg p-2">
                        <div class="flex items-center justify-between">
                            <span>
                                Storage ID: <span class="font-semibold">{{ $storage->storage_id }}</span>
                            </span>
                            <span class="text-sm text-gray-600">
                                Last Clean:
                                {{ $storage->last_clean
                                    ? \Illuminate\Support\Carbon::parse($storage->last_clean)->format('d M Y')
                                    : '-' }}
                            </span>
                        </div>
                    </li>
                @empty
                    <li class="text-gray-500">Tidak ada storage yang available.</li>
                @endforelse
            </ul>
        </div>

        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 mt-6">
            <h2 class="font-bold text-2xl mb-3">Ended Bookings</h2>
            <ul class="space-y-2">
                @forelse($endBooking as $booking)
                    <li class="border border-gray-200 py-2 hover:bg-gray-100 rounded-lg p-2">
                        <div class="flex items-center justify-between">
                            <span>Ref: <span class="font-semibold">{{ $booking->booking_ref }}</span></span>
                            <span class="text-sm text-gray-600">
                                Ended: {{ \Illuminate\Support\Carbon::parse($booking->end_date)->format('d M Y') }}
                            </span>
                        </div>
                    </li>
                @empty
                    <li class="text-gray-500">Belum ada booking yang berakhir.</li>
                @endforelse
            </ul>
        </div>

        <div class="p-4 border-solid border-1 border-gray-200 rounded-lg bg-gray-50">
            <h2 class="font-bold text-2xl">Expired Bookings</h2>
            <ul class="space-y-2">
                @foreach($endBooking as $booking)
                <li class="border border-gray-200 py-2 hover:bg-gray-100 rounded-lg p-2 ">{{ $booking->name }} - {{ $booking->end_date->format('d M Y') }}</li>
                @endforeach
            </ul>
        </div>

    </section>
@endsection
