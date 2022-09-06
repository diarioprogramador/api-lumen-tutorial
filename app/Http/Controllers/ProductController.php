<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
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
        $product = Product::all();
        if (count($product) == 0) {
            return response()->json([
                'message' => 'There are no registered products yet'
            ], 404);
        }
        return response()->json($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
        return response()->json($product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //se validan los datos recibidos
        $this->validate($request, [
            'name' => 'required',
            'description' => 'nullable',
            'image' => 'required',
            'quantity' => 'required',
            'price' => 'required'
        ]);

        //se recepciona el archivo de imagen
        if ($request->hasFile('image')) {
            $originalName = $request->file('image')->getClientOriginalName();
            $newName =  Carbon::now()->timestamp . "_" . $originalName;
            $destinationFolder = './upload/';
            $request->file('image')->move($destinationFolder, $newName);

            //se crea un nuevo objeto Product y se guarda en la BD
            $product = new Product;

            $product->name = $request->name;
            $product->description = $request->description;
            $product->image = ltrim($destinationFolder, '.') . $newName;
            $product->quantity = $request->quantity;
            $product->price = $request->price;

            $product->save();

            return response()->json($product, 201);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if ($request->hasFile('image')) {

            if($product){
                $productImage = base_path('public').$product->image;
                if(file_exists($productImage)){
                    unlink($productImage);
                }
                $product->delete();
            }

            $originalName = $request->file('image')->getClientOriginalName();
            $newName =  Carbon::now()->timestamp . "_" . $originalName;
            $destinationFolder = './upload/';
            $request->file('image')->move($destinationFolder, $newName);
            $product->image = ltrim($destinationFolder, '.') . $newName;

            $product->save();
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->quantity = $request->quantity;
        $product->price = $request->price;

        $product->save();

        return response()->json('The product was successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product  = Product::find($id);
        if($product){
            $productImage = base_path('public').$product->image;
            if(file_exists($productImage)){
                unlink($productImage);
            }
            $product->delete();
        }

        return response()->json([
            'message' => 'The product was successfully deleted'
        ], 200);
    }

}
