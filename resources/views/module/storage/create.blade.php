@extends('layouts.master')
@section('title', 'Create Storage')
@section('content')

<section>
    <form action="{{ route('data-storage.store') }}" method="post">
        @csrf
        <div class="flex md:flex-row md:justify-between">

          {{-- Kolom Kiri --}}
          <div class="p-3 w-full">
            <x-form
                name="size"
                label="Ukuran"
                type="text"
                placeholder="Masukkan Ukuran (contoh: 2x2)"
                required="true"
            />

            <x-form
                name="description"
                label="Deskripsi"
                type="textarea"
                placeholder="Masukkan Deskripsi"
            />
          </div>

          {{-- Kolom Kanan --}}
          <div class="p-3 w-full">
            <x-form
                name="price"
                label="Harga"
                type="text"
                placeholder="Masukkan Harga"
                required="true"
                allow="numeric"
            />
          </div>

        </div>

        {{-- Tombol --}}
        <div class="flex flex-row justify-end mt-4">
          <x-button variant="neutral" onclick="window.history.back()" class="mx-3">
              Batal
          </x-button>
          <x-button variant="primary" type="submit">
              Simpan
          </x-button>
        </div>
    </form>
</section>

@endsection
