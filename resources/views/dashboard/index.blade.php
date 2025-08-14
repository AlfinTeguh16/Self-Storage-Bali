@extends('layouts.master')
@section('title', 'Dashboard')
@section('content')

{{-- ======= KPIs / Cards ======= --}}
<section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
  {{-- Occupancy --}}
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-sm text-gray-500">Occupancy Rate</div>
    <div class="mt-1 flex items-end justify-between">
      <div class="text-2xl font-semibold">{{ number_format($occupancyRate, 1) }}%</div>
      <div class="text-xs text-gray-500">
        Booked: <span class="font-medium">{{ $bookedStorages }}</span> /
        Total: <span class="font-medium">{{ $totalStorages }}</span>
      </div>
    </div>
    <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden">
      <div class="h-full bg-orange-500" style="width: {{ (float) $occupancyRate }}%;"></div>
    </div>
  </div>

  {{-- Active Today --}}
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-sm text-gray-500">Active Bookings Today</div>
    <div class="mt-1 text-2xl font-semibold">{{ (int) $activeToday }}</div>
    <div class="text-xs text-gray-500 mt-1">Ongoing today</div>
  </div>

  {{-- New 7d --}}
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-sm text-gray-500">New Bookings (Last 7 Days)</div>
    <div class="mt-1 text-2xl font-semibold">{{ (int) $new7d }}</div>
    <div class="text-xs text-gray-500 mt-1">Orders in the last 7 days</div>
  </div>

  {{-- Revenue (month) --}}
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-sm text-gray-500">Revenue (This Month)</div>
    <div class="mt-1 text-2xl font-semibold">Rp {{ number_format((int) $revenueMonth, 0, ',', '.') }}</div>
    <div class="text-xs text-gray-500 mt-1">From <span class="font-medium">successful</span> bookings</div>
  </div>
</section>

{{-- ======= Secondary KPIs ======= --}}
<section class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-sm text-gray-500">Available Storages</div>
    <div class="mt-1 text-2xl font-semibold">{{ (int) $availableStorages }}</div>
    <div class="text-xs text-gray-500 mt-1">Ready to use (status <span class="font-medium">available</span>)</div>
  </div>

  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-sm text-gray-500">Ending Soon (≤ 3 days)</div>
    <div class="mt-1 text-2xl font-semibold">{{ (int) $endingSoonCount }}</div>
    <div class="text-xs text-gray-500 mt-1">Ending within the next 3 days</div>
  </div>
</section>

{{-- ======= Charts ======= --}}
<section class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-8">
  {{-- Trend Bookings (line) --}}
  <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-semibold text-gray-700">Bookings — Last 30 Days</h3>
    </div>
    <div>
      <canvas id="chartTrend" height="120"></canvas>
    </div>
  </div>

  {{-- Payment Snapshot (doughnut) --}}
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-semibold text-gray-700">Payment Snapshot</h3>
    </div>
    <div class="flex items-center justify-center">
      <canvas id="chartPayment" width="240" height="240"></canvas>
    </div>
    <div class="mt-4 grid grid-cols-3 text-xs text-gray-600">
      <div>Success: <span class="font-medium">{{ $paymentSnapshot['success'] ?? 0 }}</span></div>
      <div>Pending: <span class="font-medium">{{ $paymentSnapshot['pending'] ?? 0 }}</span></div>
      <div>Failed: <span class="font-medium">{{ $paymentSnapshot['failed'] ?? 0 }}</span></div>
    </div>
  </div>
</section>

{{-- ======= Tables ======= --}}
<section class="grid grid-cols-1 xl:grid-cols-2 gap-4">
  {{-- Latest Payments --}}
  <div class="bg-white rounded-xl border border-gray-200">
    <div class="p-4 border-b border-gray-100">
      <h3 class="text-sm font-semibold text-gray-700">Latest Payments</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-50 text-gray-600 text-xs uppercase">
            <th class="px-4 py-2 text-left">Customer</th>
            <th class="px-4 py-2 text-left">Booking</th>
            <th class="px-4 py-2 text-left">Method</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Created</th>
            <th class="px-4 py-2 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($latestPayments as $p)
            @php
              $s = strtolower((string)($p->booking_status ?? ''));
              $badge = match ($s) {
                'success' => 'bg-green-100 text-green-700',
                'pending' => 'bg-yellow-100 text-yellow-700',
                'failed'  => 'bg-red-100 text-red-700',
                default   => 'bg-gray-100 text-gray-700'
              };
            @endphp
            <tr class="border-b last:border-0 hover:bg-orange-50">
              <td class="px-4 py-2">
                <div class="font-medium">{{ $p->customer_name ?? '-' }}</div>
                @if(!empty($p->customer_email))
                  <div class="text-xs text-gray-500">{{ $p->customer_email }}</div>
                @endif
              </td>
              <td class="px-4 py-2">
                {{ $p->booking_ref ?? '—' }}
              </td>
              <td class="px-4 py-2 capitalize">{{ $p->method ?? '-' }}</td>
              <td class="px-4 py-2">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                  {{ $s ? ucfirst($s) : '—' }}
                </span>
              </td>
              <td class="px-4 py-2 text-sm text-gray-600">
                {{ $p->created_at ? \Illuminate\Support\Carbon::parse($p->created_at)->format('d M Y H:i') : '—' }}
              </td>
              <td class="px-4 py-2">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('data-payment.show', $p->id) }}"
                     class="inline-flex items-center px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs">
                    Details
                  </a>
                  <form action="{{ route('data-payment.refresh-status', $p->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                      class="inline-flex items-center px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700 text-xs">
                      Refresh
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-gray-500">No payment transactions yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Current Stays (Active Today) --}}
  <div class="bg-white rounded-xl border border-gray-200">
    <div class="p-4 border-b border-gray-100">
      <h3 class="text-sm font-semibold text-gray-700">Current Stays (Today)</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-50 text-gray-600 text-xs uppercase">
            <th class="px-4 py-2 text-left">Booking Ref</th>
            <th class="px-4 py-2 text-left">Customer</th>
            <th class="px-4 py-2 text-left">Storage</th>
            <th class="px-4 py-2 text-left">Period</th>
            <th class="px-4 py-2 text-right">Price</th>
          </tr>
        </thead>
        <tbody>
          @forelse($currentStays as $b)
            <tr class="border-b last:border-0 hover:bg-orange-50">
              <td class="px-4 py-2">{{ $b->booking_ref }}</td>
              <td class="px-4 py-2">{{ $b->customer_name }}</td>
              <td class="px-4 py-2">{{ $b->storage_size }}</td>
              <td class="px-4 py-2 text-sm text-gray-600">
                {{ \Illuminate\Support\Carbon::parse($b->start_date)->format('d M Y') }}
                —
                {{ \Illuminate\Support\Carbon::parse($b->end_date)->format('d M Y') }}
              </td>
              <td class="px-4 py-2 text-right">Rp {{ number_format((int) $b->storage_price, 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-6 text-center text-gray-500">No active bookings today.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>

{{-- Ending Soon --}}
<section class="mt-8 bg-white rounded-xl border border-gray-200">
  <div class="p-4 border-b border-gray-100">
    <h3 class="text-sm font-semibold text-gray-700">Ending Soon (≤ 3 days)</h3>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full">
      <thead>
        <tr class="bg-gray-50 text-gray-600 text-xs uppercase">
          <th class="px-4 py-2 text-left">Booking Ref</th>
          <th class="px-4 py-2 text-left">Customer</th>
          <th class="px-4 py-2 text-left">Storage</th>
          <th class="px-4 py-2 text-left">Ends</th>
        </tr>
      </thead>
      <tbody>
        @forelse($endingSoonList as $e)
          <tr class="border-b last:border-0 hover:bg-orange-50">
            <td class="px-4 py-2">{{ $e->booking_ref }}</td>
            <td class="px-4 py-2">{{ $e->customer_name }}</td>
            <td class="px-4 py-2">{{ $e->storage_size }}</td>
            <td class="px-4 py-2 text-sm text-gray-600">
              {{ \Illuminate\Support\Carbon::parse($e->end_date)->format('d M Y') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Nothing ending soon.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

{{-- ======= Scripts: Chart.js ======= --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Data from controller
  const trendLabels = @json($trendLabels ?? []);
  const trendCounts = @json($trendCounts ?? []);

  const ps = @json($paymentSnapshot ?? ['success'=>0,'pending'=>0,'failed'=>0]);
  const paymentData = [ps.success || 0, ps.pending || 0, ps.failed || 0];

  // Trend bookings line
  const ctxTrend = document.getElementById('chartTrend').getContext('2d');
  new Chart(ctxTrend, {
    type: 'line',
    data: {
      labels: trendLabels,
      datasets: [{
        label: 'Bookings',
        data: trendCounts,
        tension: 0.35,
        fill: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, ticks: { precision:0 } }
      }
    }
  });

  // Payment snapshot doughnut
  const ctxPay = document.getElementById('chartPayment').getContext('2d');
  new Chart(ctxPay, {
    type: 'doughnut',
    data: {
      labels: ['Success', 'Pending', 'Failed'],
      datasets: [{
        data: paymentData,
      }]
    },
    options: {
      responsive: true,
      cutout: '65%',
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });
</script>
@endpush

@endsection
