<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // panggil library validator bawaan laravel
use App\Models\Item; // kita panggil model Item

class ItemController extends Controller
{
    public function index() // function untuk menampilkan view
    {
        $data = [
            'title' => 'Item',
            'url_json' => url('/items/get_data'),
            'url' => url('/items'),
        ];
        return view('item', $data);
    }

    public function getData() // function untuk menampilkan data melalui json
    {
        return response()->json([
            'status' => true,
            'data' => Item::all(),
            'message' => 'data berhasil ditemukan',
        ])->header('Content-Type', 'application/json')->setStatusCode(200);
    }



public function storeData(Request $request) // function menyimpan data
{
    $data = $request->only(['item_name', 'status']);

    $validator = Validator::make($data, [
        'item_name' => ['required', 'unique:items', 'min:3', 'max:255'],
        'status' => ['required', 'in:1,0'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()
        ], 422);
    }

    Item::create($data);

    return response()->json([
        'status' => true,
        'message' => 'data berhasil ditambahkan',
    ])->header('Content-Type', 'application/json')->setStatusCode(201);
}

public function getDataById($idItem) // function untuk mengambil data berdasarkan id
{
    $item = Item::where('id', $idItem)->first();
    if(!$item) {
        return response()->json([
            'status' => false,
            'message' => 'data tidak ditemukan',
        ])->header('Content-Type', 'application/json')->setStatusCode(404);
    }

    return response()->json([
        'status' => true,
        'data' => $item,
        'message' => 'data berhasil ditemukan',
    ])->header('Content-Type', 'application/json')->setStatusCode(200);
}
public function updateData(Request $request, $idItem) // function untuk mengubah data
{
    $item = Item::where('id', $idItem)->first();
    if(!$item) {
        return response()->json([
            'status' => false,
            'message' => 'data tidak ditemukan',
        ])->header('Content-Type', 'application/json')->setStatusCode(404);
    }

    $data = $request->only(['item_name', 'status']);

    $validator = Validator::make($data, [
        'item_name' => ['required', 'min:3', 'max:255', 'unique:items,item_name,' . $item->id],
        'status' => ['required', 'in:1,0'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()
        ], 422);
    }

    $item->update($data);

    return response()->json([
        'status' => true,
        'message' => 'data berhasil diubah',
    ])->header('Content-Type', 'application/json')->setStatusCode(200);
}
public function destroyData($idItem) // function untuk menghapus data
{
    $item = Item::where('id', $idItem)->first();
    if(!$item) {
        return response()->json([
            'status' => false,
            'message' => 'data tidak ditemukan',
        ])->header('Content-Type', 'application/json')->setStatusCode(404);
    }

    $item->delete();

    return response()->json([
        'status' => true,
        'message' => 'data berhasil dihapus',
    ])->header('Content-Type', 'application/json')->setStatusCode(200);
}

}
