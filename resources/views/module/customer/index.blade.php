@extends('layouts.master')
@section('title', 'Data Customer')
@section('content')

<h1 class="text-2xl font-bold mb-4">Data Customer</h1>

<div class="flex justify-end mb-3">
    <x-button variant="primary" onclick="window.location='{{ route('data-customer.create') }}'">Tambah Customer</x-button>
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
                    <x-button variant="neutral" onclick="window.location='{{ route('data-customer.show', $customer->id) }}'">Detail</x-button>
                    <x-button variant="secondary" onclick="window.location='{{ route('data-customer.edit', $customer->id) }}'">Edit</x-button>
                    <form action="{{ route('data-customer.destroy', $customer->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <x-button variant="delete" onclick="return confirm('Yakin hapus customer ini?')">Hapus</x-button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

    @endsection
