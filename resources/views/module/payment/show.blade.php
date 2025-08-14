@extends('layouts.master')
@section('title', 'Detail Payment')
@section('content')

@php
  $status = strtolower((string)($payment->status ?? ''));
  $badgeClass = [
    'success' => 'bg-green-100 text-green-700',
    'pending' => 'bg-yellow-100 text-yellow-700',
    'failed'  => 'bg-red-100 text-red-700',
  ][$status] ?? 'bg-gray-100 text-gray-700';

  $tx = $midtrans ?? null;

  $start = $payment->start_date ? \Illuminate\Support\Carbon::parse($payment->start_date)->format('d M Y') : '—';
  $end   = $payment->end_date   ? \Illuminate\Support\Carbon::parse($payment->end_date)->format('d M Y')   : '—';

  $gross = (int)($tx->gross_amount ?? 0);
  $receipt = $receiptUrl ?? ($tx->pdf_url ?? null);
@endphp

<section>
  <div class="flex items-center justify-between mb-4">
    <div>
      <h2 class="text-xl font-semibold">Payment Detail</h2>
      <p class="text-sm text-gray-500">
        Booking Ref: <span class="font-medium">{{ $payment->booking_ref ?? '—' }}</span>
      </p>
    </div>
    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
      {{ $status ? ucfirst($status) : '—' }}
    </span>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
    <div class="space-y-3">
      <div>
        <div class="text-gray-500">Customer</div>
        <div class="font-medium">{{ $payment->customer_name ?? '-' }}</div>
        @if(!empty($payment->customer_email))
          <div class="text-xs text-gray-500">{{ $payment->customer_email }}</div>
        @endif
      </div>

      <div>
        <div class="text-gray-500">Period</div>
        <div class="font-medium">{{ $start }} — {{ $end }}</div>
      </div>

      <div>
        <div class="text-gray-500">Method</div>
        <div class="font-medium capitalize">{{ $payment->method ?? '-' }}</div>
      </div>

      <div>
        <div class="text-gray-500">Created</div>
        <div class="font-medium">
          {{ $payment->created_at ? \Illuminate\Support\Carbon::parse($payment->created_at)->format('d M Y H:i') : '—' }}
        </div>
      </div>
    </div>

    <div class="space-y-3">
      <div>
        <div class="text-gray-500">Amount</div>
        <div class="font-medium">
          {{ $gross > 0 ? ('Rp '.number_format($gross, 0, ',', '.')) : '—' }}
        </div>
      </div>

      <div>
        <div class="text-gray-500">Payment Type</div>
        <div class="font-medium">{{ $tx->payment_type ?? '-' }}</div>
      </div>

      <div>
        <div class="text-gray-500">Transaction ID</div>
        <div class="font-medium break-all">{{ $tx->transaction_id ?? '—' }}</div>
      </div>

      <div>
        <div class="text-gray-500">Fraud Status</div>
        <div class="font-medium">{{ $tx->fraud_status ?? '—' }}</div>
      </div>
    </div>
  </div>

  @if($tx)
    <hr class="my-6">

    {{-- Detail metode khusus --}}
    @if(!empty($tx->va_numbers))
      <div class="mb-4">
        <div class="text-gray-500 text-sm">Virtual Account</div>
        @foreach($tx->va_numbers as $va)
          <div class="font-medium">
            {{ strtoupper($va->bank ?? '-') }}: {{ $va->va_number ?? '-' }}
          </div>
        @endforeach
      </div>
    @endif

    @if(!empty($tx->permata_va_number))
      <div class="mb-4">
        <div class="text-gray-500 text-sm">Permata VA</div>
        <div class="font-medium">{{ $tx->permata_va_number }}</div>
      </div>
    @endif

    @if(!empty($tx->bill_key) || !empty($tx->biller_code))
      <div class="mb-4">
        <div class="text-gray-500 text-sm">E-Channel</div>
        <div class="font-medium">Bill Key: {{ $tx->bill_key ?? '-' }} | Biller Code: {{ $tx->biller_code ?? '-' }}</div>
      </div>
    @endif

    @if(!empty($tx->acquirer) || !empty($tx->actions))
      <div class="mb-4">
        <div class="text-gray-500 text-sm">QRIS / Card</div>
        <div class="font-medium">{{ $tx->acquirer ?? '-' }}</div>
      </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
      <div>
        <div class="text-gray-500">Transaction Time</div>
        <div class="font-medium">{{ $tx->transaction_time ?? '—' }}</div>
      </div>
      <div>
        <div class="text-gray-500">Settlement Time</div>
        <div class="font-medium">{{ $tx->settlement_time ?? '—' }}</div>
      </div>
    </div>

    @if(!empty($receipt))
      <div class="mt-6">
        <a href="{{ $receipt }}" target="_blank"
           class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
          Download Receipt (PDF)
        </a>
      </div>
    @endif
  @endif

  <div class="mt-8 flex items-center justify-between">
    <a href="{{ route('data-payment.index') }}" class="text-gray-600 hover:underline">← Back</a>

    {{-- refresh status: pastikan route menerima Payment ID --}}
    <form action="{{ route('data-payment.refresh-status', $payment->payment_id ?? $payment->id) }}" method="POST">
      @csrf
      <button type="submit"
              class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
        Refresh Status
      </button>
    </form>
  </div>
</section>
@endsection
