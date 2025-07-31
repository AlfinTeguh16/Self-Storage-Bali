@extends('layouts.master')
@section('title', 'Edit Box')
@section('content')

<section>
    <form action="{{ route('data-box.update', $dataBox->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="flex md:flex-row md:justify-between">
          <div class="p-3 w-full">
            <x-form
                name="ukuran"
                label="Ukuran"
                type="text"
                placeholder="Masukkan Ukuran"
                required="true"
                :value="$dataBox->ukuran"
            />
            <x-form
                name="deskripsi"
                label="Deskripsi"
                type="textarea"
                placeholder="Masukkan Deskripsi"
                required="true"
                :value="$dataBox->deskripsi"
            />
          </div>
          <div class="p-3 w-full">
            <x-form
                name="harga"
                label="Harga"
                type="text"
                placeholder="Masukkan Harga"
                required="true"
                allow="numeric"
                :value="$dataBox->harga"
            />
            <x-form
                name="jumlah"
                label="Jumlah"
                type="text"
                placeholder="Masukkan Jumlah"
                required="true"
                :value="$dataBox->jumlah"
            />
          </div>
        </div>
        <div class="flex flex-row justify-end">
          <x-button variant="neutral" onclick="window.history.back()" class="mx-3">Batal</x-button>
          <x-button variant="primary" type="submit">Update</x-button>
        </div>
    </form>
</section>

@endsection
