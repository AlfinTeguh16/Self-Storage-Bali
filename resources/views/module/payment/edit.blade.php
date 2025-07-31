@extends('layouts.master')
@section('title', 'Edit Payment')
@section('content')

<section>
    <form action="{{ route('data-payment.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="flex md:flex-row md:justify-between">
            <div class="p-3 w-full">
                <x-form type="select" name="customer_id" label="Customer" required="true">
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $payment->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </x-form>

                <x-form type="select" name="method" label="Metode Pembayaran" required="true">
                    <option value="transfer" {{ $payment->method == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="qris" {{ $payment->method == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="cash" {{ $payment->method == 'cash' ? 'selected' : '' }}>Cash</option>
                </x-form>

                <label class="block mt-3">Bukti Transaksi (Opsional)</label>
                <div>
                    <img src="{{ asset($payment->transaction_file) }}" alt="" class="w-32 mb-2">
                </div>

                <x-form type="file" name="transaction_file" class="mt-2"></x-form>
            </div>
        </div>

        <div class="flex flex-row justify-end mt-4">
            <x-button variant="neutral" onclick="window.history.back()" class="mx-3">Batal</x-button>
            <x-button variant="primary" type="submit">Update</x-button>
        </div>
    </form>
</section>

@endsection
