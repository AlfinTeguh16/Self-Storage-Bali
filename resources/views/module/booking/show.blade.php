@extends('layouts.master')
@section('title', 'Show Booking')
@section('content')

<section>
    <div class="p-3">
        <h2 class="text-xl font-semibold mb-4">Booking Details</h2>
        <p><strong>Booking Reference:</strong> {{ $booking->booking_ref }}</p>
        <p><strong>Customer Name:</strong> {{ $booking->customer_name }}</p>
        <p><strong>Address:</strong> {{ $booking->address }}</p>
        <p><strong>Email:</strong> {{ $booking->email }}</p>
        <p><strong>Phone:</strong> {{ $booking->phone }}</p>
        <p><strong>Start Date:</strong> {{ $booking->start_date->format('d-m-Y') }}</p>
        <p><strong>End Date:</strong> {{ $booking->end_date->format('d-m-Y') }}</p>
        <p><strong>Duration:</strong> {{ $booking->duration }} days</p>
        <p><strong>Total Amount:</strong> {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
        <p><strong>Storage Unit:</strong> {{ $booking->storageUnit->unit_code ?? 'N/A' }}</p>
        <p><strong>Notes:</strong> {{ $booking->notes ?? 'N/A' }}</p>
    </div>
    <div class="flex justify-end mt-6">
        <x-button variant="neutral" onclick="window.history.back()">Back</x-button>
    </div>
</section>

@endsection
