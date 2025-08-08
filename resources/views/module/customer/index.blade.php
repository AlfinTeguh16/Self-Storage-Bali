@extends('layouts.master')
@section('title', 'Data Customer')
@section('content')

<section>
    <div>
            <h1 class="text-2xl font-semibold mb-4">Data customers</h1>
            <x-button onclick="window.location='{{ route('data-customer.create') }}'" class="gap-1 flex flex-row items-center align-middle justify-center">Create New Customer <i class="ph-bold ph-plus-square"></i></x-button>
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

    @endsection
