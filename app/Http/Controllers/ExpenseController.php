<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controller untuk mengelola data pengeluaran operasional.
 */
class ExpenseController extends Controller
{
    /**
     * Menampilkan halaman daftar pengeluaran.
     * Mengambil data pengeluaran diurutkan dari yang terbaru.
     */
    public function index()
    {
        $expenses = \App\Models\OperationalExpense::orderBy('date', 'desc')->paginate(10);
        return view('module.expenses.index', compact('expenses'));
    }

    /**
     * Menyimpan data pengeluaran baru ke database.
     * Validasi input sebelum disimpan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'category' => 'required|string',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        \App\Models\OperationalExpense::create($request->all());

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    /**
     * Memperbarui data pengeluaran yang sudah ada.
     * Mencari data berdasarkan ID lalu mengupdate dengan data baru.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'category' => 'required|string',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expense = \App\Models\OperationalExpense::findOrFail($id);
        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil diperbaharui.');
    }

    /**
     * Menghapus data pengeluaran berdasarkan ID.
     */
    public function destroy($id)
    {
        $expense = \App\Models\OperationalExpense::findOrFail($id);
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
