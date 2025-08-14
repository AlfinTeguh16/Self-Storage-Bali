@extends('layouts.master')
@section('title', 'Data Payment')
@section('content')

<section>
  <div class="overflow-x-auto w-full">
    <table class="min-w-full bg-white">
      <thead>
        <tr class="bg-gray-100 text-gray-700">
          <th class="px-4 py-3 text-left">No</th>
          <th class="px-4 py-3 text-left">Customer</th>
          <th class="px-4 py-3 text-left">Booking Ref</th>
          <th class="px-4 py-3 text-left">Period</th>
          <th class="px-4 py-3 text-left">Method</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-left">Created</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
      </thead>

      <tbody>
        @forelse($payments as $payment)
          @php
            $status = strtolower((string)($payment->status ?? ''));
            $badge = match ($status) {
              'success' => 'bg-green-100 text-green-700',
              'pending' => 'bg-yellow-100 text-yellow-700',
              'failed'  => 'bg-red-100 text-red-700',
              default   => 'bg-gray-100 text-gray-700'
            };
            $start = $payment->start_date ? \Illuminate\Support\Carbon::parse($payment->start_date)->format('d M Y') : '—';
            $end   = $payment->end_date   ? \Illuminate\Support\Carbon::parse($payment->end_date)->format('d M Y')   : '—';
          @endphp

          <tr class="hover:bg-orange-50 border-b last:border-0">
            <td class="px-4 py-3">{{ $loop->iteration }}</td>

            <td class="px-4 py-3">
              <div class="font-medium">{{ $payment->customer_name ?? '-' }}</div>
              @if(!empty($payment->customer_email))
                <div class="text-xs text-gray-500">{{ $payment->customer_email }}</div>
              @endif
            </td>

            <td class="px-4 py-3">
              {{ $payment->booking_ref ?? '—' }}
            </td>

            <td class="px-4 py-3">
              {{ $start }} &ndash; {{ $end }}
            </td>

            <td class="px-4 py-3 capitalize">
              {{ $payment->method ?? '-' }}
            </td>

            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                {{ $status ? ucfirst($status) : '—' }}
              </span>
            </td>

            <td class="px-4 py-3 text-sm text-gray-600">
              {{ $payment->created_at ? \Illuminate\Support\Carbon::parse($payment->created_at)->format('d M Y H:i') : '—' }}
            </td>

            <td class="px-4 py-3">
              <div class="flex items-center justify-center gap-2">
                {{-- Detail --}}
                <a href="{{ route('data-payment.show', $payment->id) }}"
                   class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm">
                  Detail
                </a>

                {{-- Refresh status (pakai Payment ID, bukan collection) --}}
                <form action="{{ route('data-payment.refresh-status', $payment->id) }}" method="POST" class="inline">
                  @csrf
                  <button type="submit"
                          class="inline-flex items-center px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700 text-sm">
                    Refresh
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
              Belum ada data payment.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

@endsection
