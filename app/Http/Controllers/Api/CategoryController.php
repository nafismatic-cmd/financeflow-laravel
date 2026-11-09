<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return Category::whereNull('user_id')
            ->orWhere('user_id', $request->user()->id)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => ['required', Rule::in(['income', 'expense'])],
        ]);

        $category = $request->user()
            ->categories()
            ->create($data);

        return response()->json([
            'message' => 'Kategori berhasil dibuat',
            'data' => $category,
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $category = $request->user()
            ->categories()
            ->findOrFail($id);

        $category->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus',
        ]);
    }
}