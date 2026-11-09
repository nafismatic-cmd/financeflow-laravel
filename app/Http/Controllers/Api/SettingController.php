<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(
            $request->user()->setting
        );
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'initial_balance' => 'required|numeric|min:0',
        ]);

        $setting = $request->user()->setting()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json([
            'message' => 'Saldo awal berhasil diperbarui',
            'data' => $setting,
        ]);
    }
}