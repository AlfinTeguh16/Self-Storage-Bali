@extends('layouts.master')
@section('title', 'Create Payment')
@section('content')

<section>
    <form action="{{ route('data-payment.store') }}" method="POST" enctype="multipart/form-data">
        @method('POST')
        @csrf
        <div class="flex md:flex-row md:justify-between">
            <div class="p-3 w-full">
                <x-form type="select" name="customer_id" label="Customer" required="true">
                    <option value=""> Pilih Customer </option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </x-form>

                <x-form type="select" name="method" label="Metode Pembayaran" required="true">
                    <option value="transfer">Transfer</option>
                    <option value="qris">QRIS</option>
                    <option value="cash">Cash</option>
                </x-form>

                <x-form name="transaction_file" label="Upload Bukti Transaksi" type="file" />
            </div>
        </div>

        <div class="flex flex-row justify-end mt-4">
            <x-button variant="neutral" onclick="window.history.back()" class="mx-3">Batal</x-button>
            <x-button variant="primary" type="submit">Simpan</x-button>
        </div>
    </form>
</section>

@endsection
