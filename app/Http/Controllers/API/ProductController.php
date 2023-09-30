<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $products = Product::all();
    
        return response()->json($this->success($products, 'Products retrieved successfully.'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'price' => 'required',
            'category' => 'required'
        ]);
   
        if($validator->fails()){
            return response()->json($this->error(('Validation Error.' . $validator->errors()), 400));       
        }
   
        $product = Product::create($input);
   
        return response()->json($this->success($product, 'Product created successfully.', 201));
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return response()->json($this->error('Product not found.'));
        }
   
        return response()->json($this->success($product, 'Product retrieved successfully.'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'price' => 'required',
            'category' => 'required'
        ]);
   
        if($validator->fails()){
            return response()->json($this->error(['Validation Error.' => $validator->errors()], 400));       
        }
   
        $product->name = $input['name'];
        $product->price = $input['price'];
        $product->category = $input['category'];
        $product->save();
   
        return response()->json($this->success($product, 'Product updated successfully.'));
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
   
        return response()->json($this->success($msg = 'Product deleted successfully.', $status = 204));
    }
}
