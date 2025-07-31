@extends('layouts.master')
@section('title', 'Edit Booking')
@section('content')

<section>
    <form action="{{ route('data-booking.update', $booking->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="flex md:flex-row md:justify-between">
            <div class="p-3 w-full">
                <x-form name="booking_ref" label="Booking Reference" type="text" value="{{ old('booking_ref', $booking->booking_ref) }}" required="true" />
                <x-form name="customer_name" label="Customer Name" type="text" value="{{ old('customer_name', $booking->customer_name) }}" required="true" />
                <x-form name="address" label="Address" type="textarea" value="{{ old('address', $booking->address) }}" required="true" />
                <x-form name="email" label="Email" type="email" value="{{ old('email', $booking->email) }}" required="true" />
                <x-form name="phone" label="Phone" type="text" value="{{ old('phone', $booking->phone) }}" required="true" />
            </div>
            <div class="p-3 w-full">
                <x-form name="start_date" label="Start Date" type="date" value="{{ old('start_date', $booking->start_date->format('Y-m-d')) }}" required="true" />
                <x-form name="end_date" label="End Date" type="date" value="{{ old('end_date', $booking->end_date->format('Y-m-d')) }}" required="true" />
                <x-form name="duration" label="Duration (days)" type="number" value="{{ old('duration', $booking->duration) }}" required="true" />
                <x-form name="total_amount" label="Total Amount" type="number" value="{{ old('total_amount', $booking->total_amount) }}" required="true" />
            </div>
        </div>
        <div class="flex flex-row justify-between">
            <div class="p-3 w-full">
                <x-form name="storage_id" label="Storage Unit" type="select" :options="$storageUnits" selected="{{ old('storage_id', $booking->storage_id) }}" placeholder="Pilih Storage Unit" required="true" />
            </div>
        </div>
        <div class="flex flex-row justify-end">
            <x-button variant="neutral" onclick="window.history.back()" class="mx-3">Batal</x-button>
            <x-button variant="primary" type="submit">Simpan</x-button>
        </div>
    </form>
</section>

@endsection
