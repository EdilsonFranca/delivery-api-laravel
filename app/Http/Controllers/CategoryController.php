<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): JsonResponse
    {
        try {
            return response()->json(array(
                'success' => true,
                'data'    => Category::all(),
            ));

        } catch (\Exception $e) {
            return response()->json(array(
                'success'      => false,
                'message'      => 'Houve um erro',
                'messageError' => $e->getMessage(),
            ));
        }
    }

    public function categoryWithProduct(): JsonResponse
    {
        try {
            $spotlight        =  Product::whereNotNull('price_promotion')->get();
            $category_product =  Category::with('product')->get();
            $dashboard = DB::table('dashboard')->first();

            return response()->json(array(
                'success' => true,
                'data'    =>  array($spotlight, $category_product,$dashboard)
            ));

        } catch (\Exception $e) {
            return response()->json(array(
                'success'      => false,
                'message'      => 'Houve um erro',
                'messageError' => $e->getMessage(),
            ));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $data       = $request->only(['name']);
        $validation = $this->getValidate($data);

        if ($validation->fails()) {
            return response()->json(array(
                'success' => false,
                'message' => $validation->errors()
            ));
        }

        $Category = Category::where('name', $data['name'])->first();

        if (is_null($Category)) {
            $Category = Category::create($data);
        }
        return response()->json(array(
            'success' => true,
            'data'    => $Category,
            'message' => 'categoria  cadastrados com sucesso !'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add_additional(Request $request): JsonResponse
    {
        try {
            $collect_number = collect($request->additional_id);

            $Category = Category::find($request->category_id);

            $diff_add = $collect_number->diff($Category->category_additional()->pluck('id'));

            $diff_remove = $Category->category_additional()->pluck('id')->diff($request->additional_id);

            $diff_add = collect($diff_add)->map(function ($item) {
                return array('additional_id' => $item) ;
            });

            $Category->category_additional()->createMany($diff_add);

            $Category->category_additional()->whereIn('id', $diff_remove->toArray())->delete();

            $response = array(
                'success' => true,
                'message' => 'Adicionais alterados com sucesso!',
            );

        } catch (\Exception $e) {
            $response = array(
                'success'      => false,
                'message'      => 'Houve um erro',
                'messageError' => $e->getMessage(),
            );
        }

        return response()->json($response);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $product = Product::where('category_id', $id)->get();

        if($product->count() > 0)
        {
            $ProductName = $product->pluck('name');

            $response = array(
                'error'        => true ,
                'productsName' => $ProductName->implode(', ')
            );

            return response()->json($response);
        }

        $qtd = Category::destroy($id);

        if ($qtd === 0) {
            return response()->json(['erro' => 'categoria não encontrado'], 404);
        }

        return response()->json(array(
            'success' => true,
            'message' => 'categoria  removido com sucesso !'
        ));
    }

    public function show(int $id): \Illuminate\Http\JsonResponse {

        $Category = Category::where('id', $id)->with(['category_additional' => function ($query) {
                                                    $query->select('category_id', 'additional_id');
                                                }])
                                             ->first();

        if (is_null($Category)) {
            return response()->json(['message' => 'categoria não encontrado'], 404);
        }

        return response()->json( $Category );
    }

    private function getValidate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $messages = array(
            'required' => 'o campo :attribute  não pode ser vazio !'
        );

        return Validator::make($data, array(
            'name'        => 'required',
        ), $messages);
    }
}
