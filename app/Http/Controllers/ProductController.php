<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Storage::disk('local')->exists('data.json') ? json_decode(Storage::disk('local')->get('data.json')) : [];
        return response()->json(['products' => $products]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
             'product_name' => 'required|max:255',
             'quantity' => 'required',
             'price' => 'required',
             ]);
             
            $products = Storage::disk('local')->exists('data.json') ? json_decode(Storage::disk('local')->get('data.json')) : [];

            $id = collect($products)-> max('id')  ? collect($products)-> max('id') + 1 : 1;

            $product = $request->only(['product_name', 'quantity', 'price']);           
            $product['created_at'] = date('Y-m-d H:i:s');
            $product['id'] = $id;
 
            array_push($products,$product);
    
            Storage::disk('local')->put('data.json', json_encode($products));
 
            response()->json(['status' => "success"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Storage::disk('local')->exists('data.json') ? collect(json_decode(Storage::disk('local')->get('data.json')))->firstWhere('id', $id) : [];
        return response()->json(['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'product_name' => 'required|max:255',
            'quantity' => 'required',
            'price' => 'required',
            ]);

            $products = Storage::disk('local')->exists('data.json') ? json_decode(Storage::disk('local')->get('data.json'),true) : [];

            $newProductList = collect($products)->map(function ($item, $key) use($id,$request) {
                if($item['id'] == $id){
                     $item['product_name'] = $request->product_name;
                     $item['quantity'] = $request->quantity;
                     $item['price'] = $request->price;
                }
                return $item;
            });
            Storage::disk('local')->put('data.json', json_encode($newProductList));
             response()->json(['status' => "success"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

       $products = Storage::disk('local')->exists('data.json') ? json_decode(Storage::disk('local')->get('data.json'),true) : [];

       $newProductList = [];
        foreach($products as $item){
            if($item['id']!= $id){
                array_push($newProductList,$item);
            }
        }
        if($newProductList == []){
            Storage::disk('local')->delete('data.json');
        }else{
            Storage::disk('local')->put('data.json', json_encode($newProductList));
        }
               
        return response()->json(['status' => "success"]);
    }
}
