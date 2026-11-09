<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()
            ->transactions()
            ->with('category');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('month')) {
            $query->whereMonth(
                'transaction_date',
                $request->month
            );
        }

        if ($request->filled('year')) {
            $query->whereYear(
                'transaction_date',
                $request->year
            );
        }

        return response()->json(
            $query->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->paginate(10)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'type' => ['required', Rule::in(['income', 'expense'])],
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|gt:0',
            'transaction_date' => 'required|date',
        ]);

        if (!empty($data['category_id'])) {
            $category = \App\Models\Category::findOrFail(
                $data['category_id']
            );

            if (
                $category->user_id !== null &&
                $category->user_id !== $request->user()->id
            ) {
                return response()->json([
                    'message' => 'Kategori tidak valid'
                ], 403);
            }

            if ($category->type !== $data['type']) {
                return response()->json([
                    'message' => 'Jenis kategori tidak sesuai transaksi'
                ], 422);
            }
        }

        $transaction = $request->user()
            ->transactions()
            ->create($data);

        return response()->json([
            'message' => 'Transaksi berhasil ditambahkan',
            'data' => $transaction,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $transaction = $request->user()
            ->transactions()
            ->with('category')
            ->findOrFail($id);

        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        $transaction = $request->user()
            ->transactions()
            ->findOrFail($id);

        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'type' => ['sometimes', Rule::in(['income', 'expense'])],
            'description' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|gt:0',
            'transaction_date' => 'sometimes|required|date',
        ]);

        $type = $data['type'] ?? $transaction->type;

        if (!empty($data['category_id'])) {
            $category = \App\Models\Category::findOrFail(
                $data['category_id']
            );

            if (
                ($category->user_id !== null &&
                $category->user_id !== $request->user()->id) ||
                $category->type !== $type
            ) {
                return response()->json([
                    'message' => 'Kategori tidak valid'
                ], 422);
            }
        }

        $transaction->update($data);

        return response()->json([
            'message' => 'Transaksi berhasil diperbarui',
            'data' => $transaction,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $transaction = $request->user()
            ->transactions()
            ->findOrFail($id);

        $transaction->delete();

        return response()->json([
            'message' => 'Transaksi berhasil dihapus',
        ]);
    }
}