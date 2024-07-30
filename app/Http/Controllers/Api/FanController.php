<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fans = Fan::with('klub')->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Fans',
            'data' => $fans,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama_fan' => 'required',
            'klub' => 'required|array',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validate->errors(),
            ], 422);
        }

        try {
            $fan = new Fan();
            $fan->nama_fan = $request->nama_fan;
            $fan->save();
            // tampilkan banyak Klub
            $fan->klub()->attach($request->klub);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dibuat',
                'data' => $fan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    public function show(string $id)
    {
        try {
            $fan = Fan::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail fan',
                'data' => $fan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'data tidak ada',
                'errors' => $e->getMessage(),
            ], 404);
        }

    }

    public function update(Request $request, string $id)
    {
        $validate = Validator::make($request->all(), [
            'nama_fan' => 'required',
            'klub' => 'required|array',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validate->errors(),
            ], 422);
        }

        try {
            $fan = Fan::findOrFail($id);
            $fan->nama_fan = $request->nama_fan;
            $fan->save();
            // tampilkan banyak Klub
            $fan->klub()->sync($request->klub);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui',
                'data' => $fan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }

    }

    public function destroy(string $id)
    {
        try {
            $fan = Fan::findOrFail($id);
            $fan->klub()->detach();
            $fan->delete();
            // hapus banyak klub
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus',
                'data' => $fan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
