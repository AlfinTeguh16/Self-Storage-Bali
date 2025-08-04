@extends('layouts.master')
@section('title', 'Edit Box')
@section('content')

<section>
    <form action="{{ route('data-storage.update', $storage->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="flex md:flex-row md:justify-between">
          <div class="p-3 w-full">
            <x-form
                name="size"
                label="Size"
                type="text"
                placeholder="Masukkan size"
                required="true"
                value="{{ old('size', $storage->size) }}"
            />
            <x-form
                name="description"
                label="Description"
                type="textarea"
                placeholder="Masukkan Description"
                required="true"
                value="{{ old('description', $storage->description) }}"
            />
          </div>
          <div class="p-3 w-full">
            <x-form
                name="price"
                label="Price"
                type="text"
                placeholder="Masukkan Price"
                required="true"
                allow="numeric"
                value="{{ old('price', $storage->price) }}"
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
