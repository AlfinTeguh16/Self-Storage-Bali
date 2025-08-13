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

                <x-form type="select" name="sm_id" label="Storage" required="true">
                    <option value="">-- Select Storage --</option>
                    @foreach($availableStorages as $item)
                        <option value="{{ $item->sm_id }}"
                            {{ old('sm_id', $currentSm->id ?? '') == $item->sm_id ? 'selected' : '' }}>
                            {{ $item->size }} — Rp{{ number_format($item->price, 0, ',', '.') }} ({{ ucfirst($item->status) }})
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
                />

                {{-- End Date --}}
                <x-form
                    name="end_date"
                    label="End Date"
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

<!-- Modal untuk menambahkan customer baru -->

<section id="newCustomerModal"
class="absolute inset-0 z-50  items-center justify-center  hidden h-[90vh]">
    {{-- <div class="z-60 absolute bg-[radial-gradient(closest-side,theme(colors.gray.500),theme(colors.gray.400),theme(colors.gray.200))] opacity-50 h-full w-full"></div> --}}
    <div class=" bg-white rounded-lg w-full max-w-xl p-6 relative opacity-100 shadow-3xl border border-gray-200">
        <span class="absolute top-3 right-4 text-2xl font-bold cursor-pointer" onclick="closeCustomerModal()">&times;</span>
        <form action="{{ route('data-customer.store') }}" method="POST" class="w-full">
            @csrf
            <div class="flex flex-col gap-3 mt-5">
                <h2 class="text-xl font-semibold mb-2">New Customer</h2>
                <x-form name="name" label="Name" type="text" required="true" />
                <x-form name="email" label="Email" type="email" required="true" />
                <x-form name="phone" label="Phone" type="text" required="true" />
                <x-form name="address" label="Address" type="text" required="true" />
                <x-form name="credential" label="Credential" type="file" required="true" />
                <div class="flex justify-end gap-2 mt-4">
                    <x-button type="button" onclick="closeCustomerModal()">Cancel</x-button>
                    <x-button type="submit">Add Customer</x-button>
                </div>
            </div>
        </form>
    </div>
</section>


<script>
    function openCustomerModal() {
        document.getElementById('newCustomerModal').classList.remove('hidden');
        document.getElementById('newCustomerModal').classList.add('flex');
    }

    function closeCustomerModal() {
        document.getElementById('newCustomerModal').classList.add('hidden');
        document.getElementById('newCustomerModal').classList.remove('flex');
    }
</script>

@endsection
