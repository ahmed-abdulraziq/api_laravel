<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\OrderProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class OrderProductController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $orderProduct = OrderProduct::all();

        return response()->json($this->success($orderProduct, 'OrderProduct retrieved successfully.'));
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
            'quantity' => 'required',
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json($this->error(['Validation Error.' => $validator->errors()], 400));
        }

        $orderProduct = OrderProduct::create($input);

        return response()->json($this->success($orderProduct, 'OrderProduct created successfully.', 201));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $orderProduct = OrderProduct::find($id);

        if (is_null($orderProduct)) {
            return response()->json($this->error('OrderProduct not found.'));
        }

        return response()->json($this->success($orderProduct, 'OrderProduct retrieved successfully.'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderProduct $orderProduct): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'quantity' => 'required',
            'order_id' => 'required',
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($this->error(['Validation Error.' => $validator->errors()], 400));
        }

        $orderProduct->name = $input['name'];
        $orderProduct->price = $input['price'];
        $orderProduct->category = $input['category'];
        $orderProduct->save();

        return response()->json($this->success($orderProduct, 'Product updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderProduct $orderProduct): JsonResponse
    {
        $orderProduct->delete();

        return response()->json($this->success($msg = 'OrderProduct deleted successfully.', $status = 204));
    }

    public function order($id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], ['id' => 'required|exists:orders,id']);

        if ($validator->fails()) {
            return response()->json($this->error(['Validation Error.' => $validator->errors()], 400));
        }

        // $order = DB::table('order_products')
        // ->select('products.*')
        // ->rightJoin('products','order_products.product_id','=','products.id')
        // ->where('order_products.order_id','=',$id)
        // ->get();
        $order = DB::select("SELECT products.* FROM `order_products` RIGHT JOIN products ON order_products.product_id = products.id WHERE order_products.order_id = :id", ['id' => $id]);

        return response()->json($this->success([
            'order' => $order,
            'count' => count($order),
            'Total' => array_reduce($order, fn($t, $p) => $t += $p->price)
        ], 'Order selected successfully.'));
    }

}
