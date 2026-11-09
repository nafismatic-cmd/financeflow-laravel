<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $initialBalance = $user->setting?->initial_balance ?? 0;

        $income = $user->transactions()
            ->where('type', 'income')
            ->sum('amount');

        $expense = $user->transactions()
            ->where('type', 'expense')
            ->sum('amount');

        $balance = $initialBalance + $income - $expense;

        $monthlyIncome = $user->transactions()
            ->where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $monthlyExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $recentTransactions = $user->transactions()
            ->with('category')
            ->latest('transaction_date')
            ->latest('id')
            ->take(5)
            ->get();

        return response()->json([
            'balance' => $balance,
            'initial_balance' => $initialBalance,
            'total_income' => $income,
            'total_expense' => $expense,
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'recent_transactions' => $recentTransactions,
        ]);
    }
}