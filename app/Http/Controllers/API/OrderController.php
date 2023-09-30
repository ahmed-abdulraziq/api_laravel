<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $orders = Order::all();

        return response()->json($this->success($orders, 'Orders retrieved successfully.'));
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
            'user_id' => 'required|exists:users,id',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($this->error(['Validation Error.' => $validator->errors()]), 400);
        }

        $order = Order::create($input);

        return response()->json($this->success($order, 'Order created successfully.', 201));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $order = Order::find($id);

        if (is_null($order)) {
            return response()->json($this->error('Order not found.'));
        }

        return response()->json($this->success($order, 'Order retrieved successfully.'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($this->error(['Validation Error.' => $validator->errors()], 400));
        }

        $order->user_id = $input['user_id'];
        $order->status = $input['status'];
        $order->save();

        return response()->json($this->success($order, 'Order updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return response()->json($this->success($msg = 'Order deleted successfully.', $status = 204));
    }

    public function time(Request $request, Order $order): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'from' => 'date',
            'to' => 'date'
        ]);

        $orders = DB::select("SELECT * FROM `orders` WHERE `created_at` >= :from AND `created_at` <= :to", ['from' => ($input['from'] ?? 0), 'to' => ($input['to'] ?? now())]);

        if ($validator->fails()) {
            return response()->json($this->error(['Validation Error.' => $validator->errors()], 400));
        }

        return response()->json($this->success([
            'orders' => $orders,
            'count' => count($orders)
        ], 'Orders selected successfully.'));
    }

    public function user($id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], ['id' => 'required|exists:users,id']);

        // $orders = Order::where('user_id', '=', $id)->get();
        $orders = DB::select("SELECT * FROM `orders` WHERE `user_id` = :id", ['id' => $id]);

        if ($validator->fails()) {
            return response()->json($this->error(['Validation Error.' => $validator->errors()], 400));
        }

        return response()->json($this->success([
            'orders' => $orders,
            'count' => count($orders)
        ], 'Orders selected successfully.'));
    }

    public function status($status): JsonResponse
    {
        $validator = Validator::make(['status' => $status], ['status' => 'required|boolean']);

        // $orders = Order::where('status', '=', $status)->get();
        $orders = DB::select("SELECT * FROM `orders` WHERE `status` = :status", ['status' => $status]);

        if ($validator->fails()) {
            return response()->json($this->error(['Validation Error.' => $validator->errors()], 400));
        }

        return response()->json($this->success([
            'orders' => $orders,
            'count' => count($orders)
        ], 'Orders selected successfully.'));
    }

    public function userStatus($id, $status): JsonResponse
    {
        $validator = Validator::make(
            [
                'id' => $id,
                'status' => $status
            ],
            [
                'id' => 'required|exists:users,id',
                'status' => 'required|boolean'
            ]
        );

        // $orders = Order::where('status', '=', $status)->where('user_id', '=', $id)->get();
        $orders = DB::select("SELECT * FROM `orders` WHERE `user_id` = :id AND `status` = :status", [
            'id' => $id,
            'status' => $status
        ]);

        if ($validator->fails()) {
            return response()->json($this->error(['Validation Error.' => $validator->errors()], 400));
        }

        return response()->json($this->success([
            'orders' => $orders,
            'count' => count($orders)
        ], 'Orders selected successfully.'));
    }

    public function topUser($num): JsonResponse
    {
        $validator = Validator::make(['num' => $num], ['num' => 'integer']);

        if ($validator->fails()) {
            return response()->json($this->error(['Validation Error.' => $validator->errors()], 400));
        }

        // $user = DB::table('orders')
        // ->select('users.id', 'users.name', DB::raw("COUNT(orders.user_id) as count"))
        // ->join('users','orders.user_id','=','users.id')
        // ->groupBy('users.id')
        // ->orderByRaw("COUNT(orders.user_id) desc")
        // ->limit($num)
        // ->get();
        $user = DB::select("SELECT users.id, users.name, COUNT(orders.user_id) as count FROM orders INNER JOIN users ON orders.user_id = users.id GROUP BY users.id ORDER BY COUNT(orders.user_id) DESC LIMIT :num", ['num' => $num]);

        return response()->json($this->success([
            'user' => $user,
            'count' => count($user),
        ], 'User selected successfully.'));
    }
}