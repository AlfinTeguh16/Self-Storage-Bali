@extends('layouts.master')
@section('title', 'Pengeluaran Operasional')

@section('content')
<section>
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold mb-2">Pengeluaran Operasional</h1>
            <p class="text-gray-600 mb-6">Kelola pengeluaran operasional (Listrik, Kebersihan, Gaji, dll).</p>
        </div>
        <x-button onclick="document.getElementById('addExpenseModal').showModal()" class="gap-1 flex flex-row items-center align-middle justify-center">
            Tambah Pengeluaran <i class="ph-bold ph-plus-circle"></i>
        </x-button>
    </div>

    <div class="overflow-x-auto w-full rounded-xl border border-gray-200">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-left">Kategori</th>
                    <th class="px-4 py-2 text-left">Deskripsi</th>
                    <th class="px-4 py-2 text-left">Jumlah</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($expenses as $expense)
                <tr class="hover:bg-orange-50">
                    <td class="px-4 py-2 whitespace-nowrap">{{ $expense->date->format('d M Y') }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ $expense->category }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ Str::limit($expense->description, 50) }}</td>
                    <td class="px-4 py-2 whitespace-nowrap font-medium text-emerald-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-2 flex justify-center gap-2">
                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <x-button variant="delete" type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                <i class="ph-bold ph-trash"></i>
                            </x-button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada data pengeluaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $expenses->links() }}
    </div>
</section>

<dialog id="addExpenseModal" class="m-auto w-full max-w-lg rounded-2xl bg-white p-0 shadow-2xl backdrop:bg-gray-900/50 open:flex open:flex-col">
    <div class="flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                    <i class="ph-bold ph-receipt text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Catat Pengeluaran</h3>
                    <p class="text-xs text-gray-500 font-medium">Isi detail pengeluaran baru</p>
                </div>
            </div>
            <button onclick="document.getElementById('addExpenseModal').close()" class="group rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all cursor-pointer">
                <i class="ph-bold ph-x text-lg transition-transform group-hover:rotate-90"></i>
            </button>
        </div>

        <form action="{{ route('expenses.store') }}" method="POST" class="overflow-y-auto">
            @csrf
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-gray-700" for="date">
                            Tanggal
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="ph-bold ph-calendar-blank text-gray-400"></i>
                            </div>
                            <input id="date" type="date" name="date" value="{{ date('Y-m-d') }}" required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-3 py-2 text-sm text-gray-800 transition-all focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-gray-700" for="category">
                            Kategori
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="ph-bold ph-tag text-gray-400"></i>
                            </div>
                            <select id="category" name="category" required
                                class="w-full appearance-none rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-8 py-2 text-sm font-normal text-gray-800 transition-all focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="Listrik">Listrik</option>
                                <option value="Kebersihan">Kebersihan</option>
                                <option value="Gaji">Gaji</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="ph-bold ph-caret-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-gray-700" for="amount">
                        Jumlah Pengeluaran
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-sm font-bold text-gray-400">Rp</span>
                        </div>
                        <input id="amount" type="number" name="amount" placeholder="Contoh: 150000" required
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 transition-all focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-gray-700" for="description">
                        Keterangan / Deskripsi
                    </label>
                    <textarea id="description" name="description" rows="3" placeholder="Tuliskan detail pengeluaran..."
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm leading-relaxed text-gray-800 placeholder:text-gray-400 transition-all focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 rounded-b-2xl border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                <button type="button" onclick="document.getElementById('addExpenseModal').close()" 
                    class="h-10 px-6 rounded-xl border border-gray-200 bg-white text-gray-700 font-medium text-sm hover:bg-gray-50 hover:text-gray-800 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                    class="h-10 flex items-center gap-2 px-6 rounded-xl bg-indigo-600 text-white font-medium text-sm hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-md shadow-indigo-200 transition-all cursor-pointer">
                    <i class="ph-bold ph-check"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</dialog>
@endsection