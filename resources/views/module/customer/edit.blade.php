@extends('layouts.master')
@section('title', 'Edit Booking (Customer)')
@section('content')

<h1 class="text-3xl font-bold mb-6 text-gray-800">Edit Booking</h1>

@php
    // Safe date formatting untuk input type="date"
    $startValue = $booking->start_date
        ? (\Illuminate\Support\Carbon::parse($booking->start_date)->format('Y-m-d'))
        : null;
    $endValue = $booking->end_date
        ? (\Illuminate\Support\Carbon::parse($booking->end_date)->format('Y-m-d'))
        : null;
@endphp

<form action="{{ route('data-booking.update', $booking->id) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <x-card title="Informasi Booking">
        <div class="grid md:grid-cols-2 gap-6">
            {{-- Customer --}}
            <x-form type="select" name="customer_id" label="Customer" required="true">
                <option value="">-- Pilih Customer --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}"
                        {{ old('customer_id', $booking->customer_id) == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </x-form>

            {{-- Storage Unit --}}
            <x-form type="select" name="storage_id" label="Storage Unit" required="true">
                <option value="">-- Pilih Storage Unit --</option>
                @foreach($storageUnits as $unit)
                    <option value="{{ $unit->id }}"
                        {{ old('storage_id', $booking->storage_id) == $unit->id ? 'selected' : '' }}>
                        {{ $unit->unit_code }} — Size: {{ $unit->size }}
                    </option>
                @endforeach
            </x-form>

            {{-- Booking Ref --}}
            <x-form
                name="booking_ref"
                label="Booking Ref"
                type="text"
                value="{{ old('booking_ref', $booking->booking_ref) }}"
            />

            {{-- Notes --}}
            <x-form
                name="notes"
                label="Catatan"
                type="textarea"
            >{{ old('notes', $booking->notes) }}</x-form>

            {{-- Start Date --}}
            <x-form
                name="start_date"
                label="Tanggal Mulai"
                type="date"
                value="{{ old('start_date', $startValue) }}"
                required="true"
            />

            {{-- End Date --}}
            <x-form
                name="end_date"
                label="Tanggal Selesai"
                type="date"
                value="{{ old('end_date', $endValue) }}"
                required="true"
            />
        </div>
    </x-card>

    <div class="flex justify-end gap-3">
        <x-button variant="neutral" type="button" onclick="window.history.back()">Batal</x-button>
        <x-button variant="primary" type="submit">Simpan</x-button>
    </div>
</form>

@endsection
