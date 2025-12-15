@extends('layouts.master')
@section('title', 'Create Booking')
@section('content')

<section>
    <x-button type="button" onclick="openCustomerModal()">New Customer</x-button>

    <form action="{{ route('data-booking.store') }}" method="POST">
        @csrf
        <div class="flex md:flex-row md:justify-between">
            {{-- Kolom Kiri --}}
            <div class="p-3 w-full">
                {{-- Pilih Customer --}}
                <x-form type="select" name="customer_id" label="Customer" required="true">
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </x-form>

                <x-form type="select" name="sm_id" label="Storage" required="true" id="storageSelect">
                    <option value="">-- Select Storage --</option>
                    @foreach($availableStorages as $item)
                        {{-- 🔑 Pastikan data-price adalah ANGKA MURNI (bukan string terformat) --}}
                        {{-- Controller HARUS mengirim $item->price sebagai integer/float --}}
                        <option value="{{ $item->sm_id }}"
                            data-price="{{ $item->price }}"       {{-- ✅ Tanpa number_format() --}}
                            data-size="{{ $item->size ?? '' }}"
                            {{ old('sm_id', $currentSm->id ?? '') == $item->sm_id ? 'selected' : '' }}>
                            {{ $item->size }} — Rp{{ number_format($item->price, 0, ',', '.') }}/hari 
                            ({{ ucfirst($item->status ?? 'available') }})
                        </option>
                    @endforeach
                </x-form>

                {{-- Notes --}}
                <x-form
                    name="notes"
                    label="Notes"
                    type="textarea"
                    placeholder="Add notes (optional)"
                />
            </div>

            {{-- Kolom Kanan --}}
            <div class="p-3 w-full">
                {{-- Start Date --}}
                <x-form
                    name="start_date"
                    label="Start Date"
                    type="date"
                    value="{{ old('start_date') }}"
                    required="true"
                    id="startDateInput"
                />

                {{-- End Date --}}
                <x-form
                    name="end_date"
                    label="End Date"
                    type="date"
                    value="{{ old('end_date') }}"
                    required="true"
                    id="endDateInput"
                />


                {{-- <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Estimasi Biaya</h4>
                    <div class="text-lg font-bold text-orange-600" id="estimationResult">
                        —
                    </div>
                    <div class="text-xs text-gray-500 mt-1" id="estimationDetail">
                        Pilih storage dan isi tanggal untuk melihat estimasi.
                    </div>
                </div> --}}
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex flex-row justify-end mt-4">
            <x-button variant="neutral" onclick="window.history.back()" class="mx-3">Batal</x-button>
            <x-button variant="primary" type="submit">Simpan</x-button>
        </div>
    </form>
</section>

<!-- Modal -->
<section id="newCustomerModal" class="fixed inset-0 z-50 bg-black bg-opacity-40 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg w-full max-w-xl p-6 relative shadow-3xl border border-gray-200">
        <span class="absolute top-3 right-4 text-2xl font-bold cursor-pointer" onclick="closeCustomerModal()">&times;</span>
        <form action="{{ route('data-customer.store') }}" method="POST" class="w-full">
            @csrf
            <div class="flex flex-col gap-3 mt-5">
                <h2 class="text-xl font-semibold mb-2">New Customer</h2>
                <x-form name="name" label="Name" type="text" required="true" />
                <x-form name="email" label="Email" type="email" required="true" />
                <x-form name="phone" label="Phone" type="text" required="true" />
                <x-form name="address" label="Address" type="text" required="true" />
                <x-form name="credential" label="Credential (ID, Passport, KTP, SIM, etc.)" type="file" required="true" />
                <div class="flex justify-end gap-2 mt-4">
                    <x-button type="button" onclick="closeCustomerModal()">Cancel</x-button>
                    <x-button type="submit">Add Customer</x-button>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('startDateInput');
    const endDateInput = document.getElementById('endDateInput');
    const storageSelect = document.getElementById('storageSelect');
    const estimationResult = document.getElementById('estimationResult');
    const estimationDetail = document.getElementById('estimationDetail');

    if (!startDateInput || !endDateInput || !storageSelect || !estimationResult) {
        console.error('[Estimasi] Elemen tidak ditemukan');
        return;
    }


    function parsePrice(priceStr) {
        if (!priceStr) return 0;
        const cleaned = String(priceStr).replace(/[^0-9.]/g, '');
        if (!cleaned) return 0;

        
        const parts = cleaned.split('.');
        if (parts.length === 1) {
            return parseInt(parts[0], 10) || 0;
        } else if (parts.length === 2) {

            return parseFloat(parts[0] + '.' + parts[1]) || 0;
        } else {
       
            return parseInt(cleaned.replace(/\./g, ''), 10) || 0;
        }
    }


    function updateEstimation() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const selectedOption = storageSelect.options[storageSelect.selectedIndex];

        // Reset tampilan
        estimationResult.textContent = '—';
        estimationResult.className = 'text-lg font-bold text-orange-600';
        estimationDetail.innerHTML = 'Pilih storage dan isi tanggal untuk melihat estimasi.';

        // Validasi minimal
        if (!startDate || !endDate || !selectedOption?.value) {
            return;
        }

        // Ambil & parse harga dengan aman
        const rawPrice = selectedOption.dataset.price;
        const pricePerDay = parsePrice(rawPrice);
        const size = selectedOption.dataset.size || '';

        // Validasi harga
        if (pricePerDay <= 0) {
            estimationResult.className = 'text-lg font-bold text-red-500';
            estimationResult.textContent = 'Error';
            estimationDetail.innerHTML = '⚠️ Harga tidak valid (harus > 0).<br>Periksa data storage di backend.';
            console.warn('[Estimasi] Harga tidak valid:', rawPrice, '→', pricePerDay);
            return;
        }

        // Parse tanggal
        const start = new Date(startDate);
        const end = new Date(endDate);

        if (isNaN(start) || isNaN(end)) {
            estimationResult.className = 'text-lg font-bold text-red-500';
            estimationResult.textContent = 'Error';
            estimationDetail.textContent = '❌ Format tanggal tidak valid.';
            return;
        }

        if (end < start) {
            estimationResult.className = 'text-lg font-bold text-red-600';
            estimationResult.textContent = 'Error';
            estimationDetail.innerHTML = '⛔ End date tidak boleh sebelum start date.';
            return;
        }

        // Hitung durasi (inklusif)
        const diffDays = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
        const total = diffDays * pricePerDay;

        // Format ke Rupiah (lokal ID)
        const formatRupiah = (num) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(num);

        const formattedPrice = formatRupiah(pricePerDay);
        const formattedTotal = formatRupiah(total);

        estimationResult.textContent = formattedTotal;
        estimationResult.className = 'text-lg font-bold text-orange-600';
        estimationDetail.innerHTML = `
            Storage: <strong>${size}</strong><br>
            Durasi: <strong>${diffDays} hari</strong><br>
            Harga: ${formattedPrice}/hari<br>
            Total: <strong>${formattedTotal}</strong>
        `;
    }

    // Event listener
    startDateInput.addEventListener('change', function () {
        if (this.value) {
            endDateInput.min = this.value;
            if (endDateInput.value && new Date(endDateInput.value) < new Date(this.value)) {
                endDateInput.value = this.value;
            }
        }
        updateEstimation();
    });

    endDateInput.addEventListener('change', updateEstimation);
    storageSelect.addEventListener('change', updateEstimation);

    // Inisiasi awal
    updateEstimation();
});

// Modal functions
function openCustomerModal() {
    document.getElementById('newCustomerModal').classList.remove('hidden');
}
function closeCustomerModal() {
    document.getElementById('newCustomerModal').classList.add('hidden');
}
</script>

@endsection