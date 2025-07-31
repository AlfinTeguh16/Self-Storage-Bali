@extends('layouts.master')
@section('title', 'Data Payment')
@section('content')

<section>
    <div>
        <h1 class="text-2xl font-semibold mb-4">Data Payment</h1>
        <x-button onclick="window.location='{{ route('data-payment.create') }}'" class="gap-1 flex flex-row items-center align-middle justify-center">
            Create New Payment <i class="ph-bold ph-plus-square"></i>
        </x-button>
        <p class="text-gray-600 mb-6">Manage your payments here.</p>
    </div>

    <div class="overflow-x-auto w-full rounded-xl border border-gray-200">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Customer</th>
                    <th class="px-4 py-2 text-left">Method</th>
                    <th class="px-4 py-2 text-left">Transaction File</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr class="hover:bg-orange-50">
                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="px-4 py-2">{{ $payment->customer->name }}</td>
                    <td class="px-4 py-2 capitalize">{{ $payment->method }}</td>
                    <td class="px-4 py-2">
                        @if($payment->transaction_file)
                            <a href="{{ asset($payment->transaction_file) }}" target="_blank" class=" underline"> <i class="ph ph-receipt"></i> View</a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-2 flex justify-center">
                        <x-button variant="neutral" onclick="window.location='{{ route('data-payment.edit', $payment->id) }}'" class="gap-1 flex mx-1"><i class="ph-bold ph-pencil-simple"></i></x-button>
                        <form action="{{ route('data-payment.destroy', $payment->id) }}" method="POST" class="mx-1">
                            @csrf @method('DELETE')
                            <x-button variant="delete" onclick="return confirm('Hapus payment ini?')"><i class="ph-bold ph-trash"></i></x-button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>



@endsection
