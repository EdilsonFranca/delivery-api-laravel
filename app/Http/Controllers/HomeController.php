<?php
namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $products = Product::whereNotNull('price_promotion')
                                ->orderByDesc('updated_at')
                                ->limit(4)
                                ->get();

            return response()->json(array(
                'success' => true,
                'data'    => $products,
            ));

        } catch (\Exception $e) {
            return response()->json(array(
                'success'      => false,
                'message'      => 'Houve um erro',
                'messageError' => $e->getMessage(),
            ));
        }
    }
}
