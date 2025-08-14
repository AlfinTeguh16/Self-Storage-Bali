@extends('layouts.master')
@section('title', 'Booking Detail')
@section('content')

@php
  $status = strtolower((string)($booking->status ?? 'pending'));
  $badge = [
    'success' => 'bg-green-100 text-green-700',
    'pending' => 'bg-yellow-100 text-yellow-700',
    'failed'  => 'bg-red-100 text-red-700',
][$status] ?? 'bg-gray-100 text-gray-700';

  $start = $booking->start_date
    ? \Illuminate\Support\Carbon::parse($booking->start_date)->format('d M Y') : '—';
  $end   = $booking->end_date
    ? \Illuminate\Support\Carbon::parse($booking->end_date)->format('d M Y')   : '—';
@endphp

<section class="mx-auto ">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-xl font-semibold">Booking Detail</h1>
      <p class="mt-1 text-sm text-gray-500">
        Reference: <span class="font-medium">{{ $booking->booking_ref ?? '—' }}</span>
      </p>
    </div>
    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badge }}">
      {{ ucfirst($status) }}
    </span>
  </div>

  <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
    <div class="space-y-3">
      <div>
        <div class="text-gray-500">Customer</div>
        <div class="font-medium">
          {{ $booking->customer->name ?? '—' }}
        </div>
        @if(!empty($booking->customer->email))
          <div class="text-xs text-gray-500">{{ $booking->customer->email }}</div>
        @endif
        @if(!empty($booking->customer->phone))
          <div class="text-xs text-gray-500">{{ $booking->customer->phone }}</div>
        @endif
      </div>

      <div>
        <div class="text-gray-500">Storage</div>
        <div class="font-medium">
          Size: {{ $booking->storage->size ?? '—' }}
        </div>
        {{-- If you later eager-load storage, show size/price:
        <div class="text-xs text-gray-500">
          Size: {{ $booking->storage->size ?? '—' }} · Price: Rp {{ isset($booking->storage->price) ? number_format($booking->storage->price,0,',','.') : '—' }}
        </div>
        --}}
      </div>

      <div>
        <div class="text-gray-500">Period</div>
        <div class="font-medium">{{ $start }} — {{ $end }}</div>
      </div>
    </div>

    <div class="space-y-3">
      <div>
        <div class="text-gray-500">Notes</div>
        <div class="font-medium whitespace-pre-line">
          {{ $booking->notes ?: '—' }}
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <div class="text-gray-500">Created At</div>
          <div class="font-medium">
            {{ $booking->created_at ? \Illuminate\Support\Carbon::parse($booking->created_at)->format('d M Y H:i') : '—' }}
          </div>
        </div>
        <div>
          <div class="text-gray-500">Updated At</div>
          <div class="font-medium">
            {{ $booking->updated_at ? \Illuminate\Support\Carbon::parse($booking->updated_at)->format('d M Y H:i') : '—' }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Optional CTA row --}}
  <div class="mt-8 flex items-center justify-between">
    <a href="{{ route('data-booking.index') }}" class="text-gray-600 hover:underline">← Back to Bookings</a>

    <div class="flex items-center gap-2">
      <a href="{{ route('data-booking.edit', $booking->id) }}"
         class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
        Edit
      </a>
      {{-- If you want a quick delete (soft delete as in your controller) --}}
      <form action="{{ route('data-booking.destroy', $booking->id) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this booking?');">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="inline-flex items-center px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
          Delete
        </button>
      </form>
    </div>
  </div>
</section>

@endsection
