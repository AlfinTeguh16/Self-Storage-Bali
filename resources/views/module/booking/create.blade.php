@extends('layouts.master')
@section('title', 'Create Booking')
@section('content')

<section>
    <form action="{{ route('data-booking.store') }}" method="POST">
        @csrf
        <div class="flex md:flex-row md:justify-between">
            {{-- Kolom Kiri --}}
            <div class="p-3 w-full">
                {{-- Pilih Customer --}}
                <x-form type="select" name="customer_id" label="Customer" required="true">
                    <option value="">-- Pilih Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </x-form>

                {{-- Booking Ref --}}
                <x-form
                    name="booking_ref"
                    label="Booking Ref"
                    type="text"
                    placeholder="Masukkan Kode Booking"
                    value="{{ old('booking_ref') }}"
                    required="true"
                />

                {{-- Notes --}}
                <x-form
                    name="notes"
                    label="Catatan"
                    type="textarea"
                    placeholder="Tambahkan Catatan (opsional)"
                />
            </div>

            {{-- Kolom Kanan --}}
            <div class="p-3 w-full">
                {{-- Start Date --}}
                <x-form
                    name="start_date"
                    label="Tanggal Mulai"
                    type="date"
                    value="{{ old('start_date') }}"
                    required="true"
                />

                {{-- End Date --}}
                <x-form
                    name="end_date"
                    label="Tanggal Selesai"
                    type="date"
                    value="{{ old('end_date') }}"
                    required="true"
                />
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex flex-row justify-end mt-4">
            <x-button variant="neutral" onclick="window.history.back()" class="mx-3">Batal</x-button>
            <x-button variant="primary" type="submit">Simpan</x-button>
        </div>
    </form>
</section>

@endsection
