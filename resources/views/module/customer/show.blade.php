@extends('layouts.master')
@section('title', 'Detail Customer')
@section('content')

<h1 class="text-3xl font-bold mb-6 text-gray-800">Customer Details</h1>

<div>
    <div class="grid md:grid-cols-2 gap-6">
        <div>
            <p class="mb-2"><span class="font-semibold">Name:</span> {{ $customer->name ?? '-' }}</p>
            <p class="mb-2"><span class="font-semibold">Email:</span> {{ $customer->email ?? '-' }}</p>
            <p class="mb-2"><span class="font-semibold">Phone:</span> {{ $customer->phone ?? '-' }}</p>
            <p class="mb-2"><span class="font-semibold">Address:</span> {{ $customer->address ?? '-' }}</p>
            <p class="mb-2"><span class="font-semibold">Notes:</span> {{ $customer->notes ?? '-' }}</p>
        </div>
        <div>

            <p class="mb-2"><span class="font-semibold">Total Bookings:</span> {{ $customer->bookings->count() }}</p>
            <p class="mb-2"><span class="font-semibold">Total Payments:</span> {{ $customer->payments->count() }}</p>
            <p class="mb-2"><span class="font-semibold">Total Amount Paid:</span> {{ $customer->payments->sum('amount') }}</p>
            <p class="mb-2"><span class="font-semibold">Credential:</span>
                @if($customer->credential)
                    <a href="{{ asset('storage/'.$customer->credential) }}" target="_blank" class="text-blue-600 hover:underline">View Credential</a>
                @else
                    Not Provided
                @endif
            </p>
            <p class="mb-2"><span class="font-semibold">Booking Status:</span> {{ $customer->bookings->isEmpty() ? 'No Bookings' : 'Has Bookings' }}</p>
            <p class="mb-2"><span class="font-semibold">Payment Status:</span> {{ $customer->payments->isEmpty() ? 'No Payments' : 'Has Payments' }}</p>
            <p class="mb-2"><span class="font-semibold">Booking Status:</span> @json($customer->bookings->pluck('booking_ref'))</p>

        </div>
    </div>
</div>

<div class="flex justify-end mt-6 gap-3">
    <x-button variant="neutral" type="button" onclick="window.history.back()">Kembali</x-button>
    <x-button variant="primary" onclick="window.location='{{ route('data-customer.edit', $customer->id) }}'">Edit</x-button>
</div>

@endsection
