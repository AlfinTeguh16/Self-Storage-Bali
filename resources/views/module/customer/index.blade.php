@extends('layouts.master')
@section('title', 'Data Customer')
@section('content')

<section>
    <div>
            <h1 class="text-2xl font-semibold mb-4">Data customers</h1>
            <x-button onclick="openCustomerModal()" class="gap-1 flex flex-row items-center align-middle justify-center">Create New Customer <i class="ph-bold ph-plus-square"></i></x-button>
            <p class="text-gray-600 mb-6">Manage your customers here.</p>
    </div>

    <div class="overflow-x-auto w-full rounded-xl border border-gray-200">
        <table class=" min-w-full bg-white">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-2 text-left">#</th>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Telepon</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $index => $customer)
                <tr>
                    <td class="px-4 py-2 text-left">{{ $index + 1 }}</td>
                    <td class="px-4 py-2 text-left">{{ $customer->name }}</td>
                    <td class="px-4 py-2 text-left">{{ $customer->email }}</td>
                    <td class="px-4 py-2 text-left">{{ $customer->phone }}</td>
                    <td class="px-4 py-2 text-left">
                        <x-button variant="neutral" onclick="window.location='{{ route('data-customer.show', $customer->id) }}'"> <i class="ph-bold ph-eye"></i></x-button>
                        <x-button variant="secondary" onclick="window.location='{{ route('data-customer.edit', $customer->id) }}'"> <i class="ph-bold ph-pencil-simple"></i></x-button>
                        <form action="{{ route('data-customer.destroy', $customer->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <x-button variant="delete" onclick="return confirm('Yakin hapus customer ini?')"> <i class="ph-bold ph-trash"></i></x-button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>



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
