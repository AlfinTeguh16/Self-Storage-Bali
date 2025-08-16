@extends('layouts.master')
@section('title', 'Edit Customer')
@section('content')

<section>
    <form action="{{ route('data-customer.update', $customer->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="flex md:flex-col md:justify-between">
          <div>
            <x-form
                name="name"
                label="Name"
                type="text"
                placeholder="Insert Name"
                required="true"
                value="{{ old('name', $customer->name) }}"
            />
            <x-form
                name="address"
                label="Address"
                type="textarea"
                placeholder="Insert Address"
                required="true"
                value="{{ old('address', $customer->address) }}"
            />
          </div>
          <div>
            <x-form
                name="email"
                label="Email"
                type="text"
                placeholder="Insert Email"
                required="true"
                value="{{ old('email', $customer->email) }}"
            />
          </div>
          <div>
            <x-form
                name="phone"
                label="Phone"
                type="text"
                placeholder="Insert Phone"
                required="true"
                value="{{ old('phone', $customer->phone) }}"
            />
          </div>
            <div>
                <x-form
                    name="credentials"
                    label="Credentials"
                    type="file"
                    placeholder="Add credentials (ID, Passport, KTP, SIM, etc.)"
                    value="{{ old('credentials', $customer->credentials) }}"
                />
        </div>
        <div class="flex flex-row justify-end">
            <x-button variant="neutral" onclick="window.history.back()" class="mx-3">Batal</x-button>
            <x-button variant="primary" type="submit">Update</x-button>
        </div>
    </form>
</section>


@endsection
