@extends('layouts.master')
@section('title', 'Detail Payment')
@section('content')

<section>
    <div class="p-3">
        <h2 class="text-xl font-semibold mb-4">Payment Details</h2>
        <p><strong>Customer:</strong> {{ $payment->customer->name }}</p>
        <p><strong>Metode:</strong> {{ ucfirst($payment->method) }}</p>
        <p><strong>Bukti Transaksi:</strong>
            @if($payment->transaction_file)
                <a href="{{ asset($payment->transaction_file) }}" target="_blank" class="text-blue-600 underline">Lihat Bukti</a>
            @else
                Tidak ada bukti
            @endif
        </p>
    </div>

    <div class="flex justify-end mt-6">
        <x-button variant="neutral" onclick="window.history.back()">Kembali</x-button>
    </div>
</section>

@endsection
