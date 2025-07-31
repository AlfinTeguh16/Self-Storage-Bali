@extends('layouts.master')
@section('title', 'Storage Management')
@section('content')

<h1 class="text-2xl font-bold mb-4">Storage Management</h1>

@foreach($storages as $size => $storageGroup)
    {{-- Header per Size --}}
    <h2 class="text-xl font-semibold mt-4 mb-2">Size: {{ $size }}</h2>

    <div class="grid grid-cols-6 gap-4">
        @foreach($storageGroup as $storage)
            @php
                $isBooked = $storage->storageManagement
                    ->where('status', 'booked')
                    ->isNotEmpty();

                $lastClean = optional($storage->storageManagement->last())->last_clean;
            @endphp

            <button
                class="storage-button p-3 border rounded-lg text-center w-full
                {{ $isBooked ? 'bg-red-300 text-white' : 'bg-green-200' }}"
                data-storage-id="{{ $storage->id }}"
                data-storage-description="{{ $storage->description ?? 'Tidak ada deskripsi' }}"
                data-storage-size="{{ $storage->size }}"
                data-last-clean="{{ $lastClean ?? 'Belum Pernah' }}"
            >
                <p><strong>ID:</strong> {{ $storage->id }}</p>
                <p>{{ $storage->description }}</p>
                <p class="text-xs mt-2"><strong>Last Clean:</strong> {{ $lastClean ?? '-' }}</p>
            </button>
        @endforeach
    </div>
@endforeach
{{-- Overlay (hanya 1x, di luar loop) --}}
@foreach($storages as $size => $storageGroup)
    <h2 class="text-xl font-semibold mt-4 mb-2">Size: {{ $size }}</h2>
    <div class="grid grid-cols-6 gap-4">
        @foreach($storageGroup as $storage)
            @php
                $isBooked = $storage->storageManagement->where('status', 'booked')->isNotEmpty();
                $management = $storage->storageManagement->last(); // Ambil relasi management terakhir
            @endphp

            {{-- Tombol untuk buka modal --}}
            <button class="p-3 border rounded-lg text-center w-full {{ $isBooked ? 'bg-red-300 text-white' : 'bg-green-200' }}"
                data-bs-toggle="modal"
                data-bs-target="#editLastCleanModal-{{ $management->id }}">
                <p><strong>ID:</strong> {{ $storage->storages_id }}</p>
                <p>{{ $storage->description }}</p>
                <p class="text-xs mt-2"><strong>Last Clean:</strong> {{ $management->last_clean ?? '-' }}</p>
            </button>

            {{-- Modal Bootstrap untuk Edit Last Clean --}}
            <div id="editLastCleanModal-{{ $management->id }}" class="modal fade" tabindex="-1" aria-labelledby="editLastCleanLabel-{{ $management->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('storage-management.last-clean', $management->id) }}" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title fs-6" id="editLastCleanLabel-{{ $management->id }}">Update Last Clean</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4">
                            <div class="mb-3 form-group">
                                <label class="form-label" for="lastClean-{{ $management->id }}">Tanggal Last Clean <span class="text-danger">*</span></label>
                                <input id="lastClean-{{ $management->id }}" class="form-control" type="date" name="last_clean" value="{{ $management->last_clean }}" required />
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endforeach



@endsection
