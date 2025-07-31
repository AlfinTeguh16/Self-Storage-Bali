@extends('layouts.master')
@section('title', 'Data Box')

@section('content')
<section>
    <div>
        <h1 class="text-2xl font-semibold mb-4">Data Storage</h1>
        <x-button onclick="window.location='{{ route('data-storage.create') }}'" class="gap-1 flex flex-row items-center align-middle justify-center">Create New Storage <i class="ph-bold ph-plus-square"></i></x-button>
        <p class="text-gray-600 mb-6">Manage your storage units here.</p>
    </div>
    <div class="overflow-x-auto w-full rounded-xl border border-gray-200 ">
        <table class="min-w-full bg-white ">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Ukuran</th>
                    <th class="px-4 py-2 text-left">Deskripsi</th>
                    <th class="px-4 py-2 text-left">Harga</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($storages as $storage)
                <tr class="hover:bg-orange-50">
                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="px-4 py-2">{{ $storage->size }}</td>
                    <td class="px-4 py-2">{{ $storage->description }}</td>
                    <td class="px-4 py-2">{{ number_format($storage->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-2 flex justify-center ">
                        <x-button variant="neutral" onclick="window.location='{{ route('data-storage.edit', $storage->id) }}'" class="gap-1 flex flex-row items-center align-middle justify-center mx-1 "> <i class="ph-bold ph-pencil-simple"></i></x-button>
                        <form action="{{ route('data-storage.destroy', $storage->id) }}" method="POST" class="mx-1">
                            @csrf @method('DELETE')
                            <x-button variant="delete" onclick="return confirm('Hapus storage ini?')"><i class="ph-bold ph-trash"></i></x-button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>


@endsection
