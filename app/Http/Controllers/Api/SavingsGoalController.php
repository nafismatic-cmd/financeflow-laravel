<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SavingsGoalController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->savingsGoals()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|gt:0',
            'target_date' => 'nullable|date|after_or_equal:today',
        ]);

        $goal = $request->user()
            ->savingsGoals()
            ->create($data);

        return response()->json([
            'message' => 'Target tabungan berhasil dibuat',
            'data' => $goal,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $goal = $request->user()
            ->savingsGoals()
            ->findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'target_amount' => 'sometimes|required|numeric|gt:0',
            'target_date' => 'nullable|date',
        ]);

        $goal->update($data);

        return response()->json([
            'message' => 'Target tabungan berhasil diperbarui',
            'data' => $goal,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $goal = $request->user()
            ->savingsGoals()
            ->findOrFail($id);

        $goal->delete();

        return response()->json([
            'message' => 'Target tabungan berhasil dihapus',
        ]);
    }
}