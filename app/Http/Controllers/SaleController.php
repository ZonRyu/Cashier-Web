<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Produk;
use App\Models\Receipt;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Produk::all();
        $checkout = Sale::where('status', 'Undone')->get();
        $total_harga = Sale::where('status', 'Undone')->sum('total_harga');
        $receipt = Receipt::where('status', 'Undone')->first();

        if($receipt){
            return view('sales.sales', compact('produk', 'checkout', 'total_harga', 'receipt'));
        }
        else{

            Receipt::create([
                'nama_pembeli' => null,
                'total_harga' => null,
                'status'
            ]);

            $receipt = Receipt::where('status', 'Undone')->first();
            return view('sales.sales', compact('produk', 'checkout', 'total_harga', 'receipt'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $id = $request->produk_id;
        $harga = Produk::select('id', 'harga_produk')->where('id','=', $id)->first();


        $validateData = Sale::create([
            'receipt_id' => $request->receipt_id,
            'produk_id' => $id,
            'total_harga' => $harga->harga_produk
        ]);



        // User::create($validateData);
        Alert::toast('Berhasil menambahkan barang!', 'success');
        return redirect()->route('checkout');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $data = [
            'quantity_produk' => 'required',
        ];
        // dd($request->all());
        $validate = $request->validate($data);
        $quantity = $request->quantity_produk;
        $harga = Produk::select('id', 'harga_produk')->where('id','=', $id)->first();
        $total_harga = $harga->harga_produk *  $quantity;
        // dd($total_harga);

        Sale::where('id', $id)->update([
            'quantity_produk' => $quantity,
            'total_harga' => $total_harga,
        ]);


        Alert::success('Berhasil', 'Berhasil mengedit');
        return redirect()->route('checkout');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //get post by ID
        $item = Sale::findOrFail($id);

        //delete post
        $item->delete();

        //redirect to checkout
        Alert::success('Berhasil', 'Berhasil menghapus');
        return redirect()->route('checkout');
    }
}
