<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController
{
    public $month = array('1'  => 'Janeiro',
                              '2'  => 'Fevereiro',
                              '3'  => 'Março',
                              '4'  => 'Abril',
                              '5'  => 'Maio',
                              '6'  => 'Junho',
                              '7'  => 'Julho',
                              '8'  => 'Agosto',
                              '9'  => 'Setembro',
                              '10' => 'Outubro',
                              '11' => 'Novembro',
                              '12' => 'Dezembro',
                              );

    public $week  = array('Sunday'    => 'Domingo',
                         'Monday'    => 'Segunda',
                         'Tuesday'   => 'Terça',
                         'Wednesday' => 'Quarta',
                         'Thursday'  => 'Quinta',
                         'Friday'    => 'Sexta',
                         'Saturday'  => 'Sábado'
    );

    public function dashboard_status(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(DB::table('dashboard')->select('state')->first());
    }

    public function dashboard_change_status(Request $request): \Illuminate\Http\JsonResponse
    {
        DB::table('dashboard')->where('id_dashboard', 1)->update(['state' => $request->status]);
        return response()->json(DB::table('dashboard')->select('state')->first());
    }

    public function dashboard(Request $request): \Illuminate\Http\JsonResponse
    {
        $response = '';
        try {
            if ($request->type == 'year')
            {
                $select = " MONTH(created_at) as `month`";
                $month_number =  DB::table('order')->select(DB::raw('count(*) as `qtd`'), DB::raw($select), 'created_at')
                    ->whereBetween('created_at',[(new Carbon)->subYear(1)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString()] )
                    ->groupBy('month')
                    ->orderBy('created_at')
                    ->get()
                    ->pluck('qtd', 'month');

                $select = " MONTH(created_at) as `month`";
                $invoicing =  DB::table('order')->select(DB::raw('sum(total) as `qtd`'), DB::raw($select), 'created_at')
                    ->whereBetween('created_at',[(new Carbon)->subYear(1)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString()] )
                    ->groupBy('month')
                    ->orderBy('created_at')
                    ->get()
                    ->pluck('qtd', 'month');

                $response = array();
                $month_number->each(function ($item, $key) use(&$invoicing, &$response)
                {
                    $response[$this->month[$key].' '. 'R$ '.number_format($invoicing[$key], 2, ',', '.')] = $item;
                });
            }
            elseif ($request->type == 'month')
            {
                $select = " DAY(created_at) as `day`";
                $week_number =  DB::table('order')->select(DB::raw('count(*) as `qtd`'), DB::raw($select), 'created_at')
                    ->whereBetween('created_at',[(new Carbon)->subMonth(1)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString()] )
                    ->groupBy('day')
                    ->orderBy('created_at')
                    ->get()
                    ->pluck('qtd', 'day');

                $select = " DAY(created_at) as `day`";
                $invoicing =  DB::table('order')->select(DB::raw('sum(total) as `qtd`'), DB::raw($select), 'created_at')
                    ->whereBetween('created_at',[(new Carbon)->subMonth(1)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString()] )
                    ->groupBy('day')
                    ->orderBy('created_at')
                    ->get()
                    ->pluck('qtd', 'day');

                $response = array();
                $week_number->each(function ($item, $key) use(&$invoicing, &$response)
                {
                    $response[$key.' '. 'R$ '.number_format($invoicing[$key], 2, ',', '.')] = $item;
                });
            }
            elseif ($request->type == 'week')
            {
                $select = " DAYNAME(created_at) as `week`";
                $week_number =  DB::table('order')->select(DB::raw('count(*) as `qtd`'), DB::raw($select), 'created_at')
                    ->whereBetween('created_at',[(new Carbon)->subWeek(1)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString()] )
                    ->groupBy('week')
                    ->orderBy('created_at')
                    ->get()
                    ->pluck('qtd', 'week');


                $select = " DAYNAME(created_at) as `week`";
                $invoicing =  DB::table('order')->select(DB::raw('sum(total) as `qtd`'), DB::raw($select))
                    ->whereBetween('created_at',[(new Carbon)->subWeek(1)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString()] )
                    ->groupBy('week')
                    ->orderBy('created_at')
                    ->get()
                    ->pluck('qtd', 'week');

                $response = array();
                $week_number->each(function ($item, $key) use(&$invoicing, &$response)
                {
                    $response[$this->week[$key].' '. 'R$ '.number_format($invoicing[$key], 2, ',', '.')] = $item;
                });
            }
            elseif ($request->type == 'day')
            {
                $select = " Day(created_at) as `day`";
                
                $day_number =  DB::table('order')->select(DB::raw('count(*) as `qtd`'), DB::raw($select), 'created_at')
                    ->whereBetween('created_at',[(new Carbon)->subDay(1)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString()] )
                    ->get()
                    ->pluck('qtd', 'day');
                    

                $invoicing =  DB::table('order')->select(DB::raw('sum(total) as `qtd`'), DB::raw($select), 'created_at')
                    ->whereBetween('created_at',[(new Carbon)->subDay(1)->startOfDay()->toDateString(),(new Carbon)->now()->endOfDay()->toDateString()] )
                    ->get()
                    ->pluck('qtd', 'day');

                $response = array();
                $day_number->each(function ($item, $key) use(&$invoicing, &$response)
                {
                    if($key)
                    $response[$key.' '. 'R$ '.number_format($invoicing[$key], 2, ',', '.')] = $item;
                });
            }

            return response()->json(array(
                'success' => true,
                'data'    => $response
            ));

        } catch (\Exception $e) {
            return response()->json(array(
                'success'      => false,
                'message'      => 'Houve um erro',
                'messageError' => $e->getMessage(),
            ));
        }
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json(Order::with('client')->with('order_product.product')
                                                                 ->orderBy('id_order', 'desc')
                                                                 ->get(),
            );

        } catch (\Exception $e)
        {
            return response()->json(array(
                'success'      => false,
                'message'      => 'Houve um erro',
                'messageError' => $e->getMessage(),
            ));
        }
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $dashboard = DB::table('dashboard')->select('state')->first();
         if ($dashboard->state == 0){
             return response()->json(array('message' => 'Sinto Muito estamos fechados no momento!'));
         }

        $subTotal = 0;
        $create_order_product = array();
        $data = $request->only(['items', 'formOfPayment', 'deliveryFee', 'thing']);

        $list_id_product = collect($data['items']);
        $list_id_product->each(function ($item) use (&$create_order_product, &$subTotal) {

            array_push($create_order_product, array('product_id' => $item['id'], 'quantity' => $item['quantity'], 'description' => $item['description']));

            $product   = Product::find($item['id']);
            $price     = $product->price_promotion ?? $product->price;
            $value     = $price * $item['quantity'];
            $subTotal += $value;
        });

        $total = $subTotal + $data['deliveryFee'];

        $create = array(
            'deliveryFee'   => $data['deliveryFee'],
            'formOfPayment' => $data['formOfPayment'],
            'thing'         => $data['thing'],
            'subTotal'      => $subTotal,
            'total'         => $total,
        );

        $client = Client::create($request->client);
        $Order  = $client->order()->create($create);
        $Order->order_product()->createMany($create_order_product);

        return response()->json(array(
            'success' => true,
            'data'    => $Order,
            'message' => 'pedido  cadastrados com sucesso !'
        ));
    }

    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($id);

        if (is_null($order)) {
            return response()->json(['message' => 'order não encontrado'], 404);
        }

        return response()->json($order);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function getValidate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $messages = array(
            'required' => 'o campo :attribute  não pode ser vazio !'
        );

        return Validator::make($data, array(
            'name'        => 'required',
            'price'       => 'required',
            'description' => 'required',
        ), $messages);
    }

    public function change_status(Request $request, int $orderId): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($orderId);

        if (is_null($order)) {
            return response()->json(['message' => 'product não encontrado'], 404);
        }

        $order->state = $request->state;
        $order->save();
        return response()->json($order);
    }
}
