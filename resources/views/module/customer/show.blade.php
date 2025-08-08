@extends('layouts.master')
@section('title', 'Detail Booking (Customer)')
@section('content')

<h1 class="text-3xl font-bold mb-6 text-gray-800">Booking Details</h1>

@php
    $startHuman = $booking->start_date
        ? \Illuminate\Support\Carbon::parse($booking->start_date)->format('d-m-Y')
        : '-';
    $endHuman = $booking->end_date
        ? \Illuminate\Support\Carbon::parse($booking->end_date)->format('d-m-Y')
        : '-';
@endphp

<x-card>
    <div class="grid md:grid-cols-2 gap-6">
        <div>
            <p class="mb-2"><span class="font-semibold">Booking Ref:</span> {{ $booking->booking_ref ?? '-' }}</p>
            <p class="mb-2"><span class="font-semibold">Customer:</span> {{ $booking->customer->name ?? '-' }}</p>
            <p class="mb-2"><span class="font-semibold">Email:</span> {{ $booking->customer->email ?? '-' }}</p>
            <p class="mb-2"><span class="font-semibold">Phone:</span> {{ $booking->customer->phone ?? '-' }}</p>
            <p class="mb-2"><span class="font-semibold">Notes:</span> {{ $booking->notes ?? '-' }}</p>
        </div>
        <div>
            <p class="mb-2"><span class="font-semibold">Storage Unit:</span> {{ $booking->storageUnit->unit_code ?? 'N/A' }}</p>
            <p class="mb-2"><span class="font-semibold">Size:</span> {{ $booking->storageUnit->size ?? 'N/A' }}</p>
            <p class="mb-2"><span class="font-semibold">Tanggal Mulai:</span> {{ $startHuman }}</p>
            <p class="mb-2"><span class="font-semibold">Tanggal Selesai:</span> {{ $endHuman }}</p>
            @if(!empty($booking->duration))
                <p class="mb-2"><span class="font-semibold">Durasi:</span> {{ $booking->duration }} hari</p>
            @endif
            @if(!empty($booking->total_amount))
                <p class="mb-2"><span class="font-semibold">Total:</span> Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
            @endif
        </div>
    </div>
</x-card>

<div class="flex justify-end mt-6 gap-3">
    <x-button variant="neutral" type="button" onclick="window.history.back()">Kembali</x-button>
    <x-button variant="primary" onclick="window.location='{{ route('data-booking.edit', $booking->id) }}'">Edit</x-button>
</div>

@endsection
