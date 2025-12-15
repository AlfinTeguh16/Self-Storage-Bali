@extends('layouts.master')

@section('title', 'Data Booking')

@section('content')
<section>
    <div>
        <h1 class="text-2xl font-semibold mb-4">Data Booking</h1>
        <x-button onclick="window.location='{{ route('data-booking.create') }}'" class="gap-1 flex flex-row items-center align-middle justify-center">Create New Booking <i class="ph-bold ph-plus-square"></i></x-button>
        <p class="text-gray-600 mb-6">Manage your bookings here.</p>
    </div>
    <div class="overflow-x-auto w-full rounded-xl border border-gray-200 ">
        <table class="min-w-full bg-white ">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Booking Ref</th>
                    <th class="px-4 py-2 text-left">Customer Name</th>
                    <th class="px-4 py-2 text-left">Start Date</th>
                    <th class="px-4 py-2 text-left">End Date</th>
                    <th class="px-4 py-2 text-left">Notes</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr class="hover:bg-orange-50">
                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="px-4 py-2">{{ $booking->booking_ref }}</td>
                    <td class="px-4 py-2">{{ $booking->customer->name }}</td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->start_date)->format('d-m-Y') }}</td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->end_date)->format('d-m-Y') }}</td>
                    <td class="px-4 py-2">{{ $booking->notes }}</td>
                    <td class="px-4 py-2 flex justify-center ">
                        <form action="{{ route('data-booking.end', $booking->id) }}" method="POST" class="inline">
                            @csrf
                            <x-button type="submit" variant="neutral" onclick="return confirm('Are you sure you want to end this booking? The storage will be returned to available status.')">
                                End Booking
                            </x-button>
                        </form>
                        <x-button variant="neutral" onclick="window.location='{{ route('data-booking.show', $booking->id) }}'" class="gap-1 flex flex-row items-center align-middle justify-center mx-1 "> <i class="ph-bold ph-eye"></i></x-button>
                        <x-button variant="secondary" onclick="window.location='{{ route('data-booking.edit', $booking->id) }}'" class="gap-1 flex flex-row items-center align-middle justify-center mx-1 "> <i class="ph-bold ph-pencil-simple"></i></x-button>
                        <form action="{{ route('data-booking.destroy', $booking->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <x-button variant="delete" onclick="return confirm('You are about to delete this booking. Are you sure?')"> <i class="ph-bold ph-trash"></i></x-button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@endsection
