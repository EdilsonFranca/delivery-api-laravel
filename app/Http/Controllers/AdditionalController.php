<?php

namespace App\Http\Controllers;

use App\Models\Additional;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AdditionalController extends Controller
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
                'data'    => Additional::all(),
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
        $data       = $request->only(['name','price']);
        $validation = $this->getValidate($data);

        if ($validation->fails()) {
            return response()->json(array(
                'success' => false,
                'message' => $validation->errors()
            ));
        }

        $Additional = Additional::where('name', $data['name'])->first();

        if (is_null($Additional)) {
            $Additional = Additional::create($data);
        }
        return response()->json(array(
            'success' => true,
            'data'    => $Additional,
            'message' => 'adicional  cadastrados com sucesso !'
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {

        $qtd = Additional::destroy($id);

        if ($qtd === 0) {
            return response()->json(['erro' => 'adicional não encontrado'], 404);
        }

        return response()->json(array(
            'success' => true,
            'message' => 'adicional  removido com sucesso !'
        ));
    }

    private function getValidate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $messages = array(
            'required' => 'o campo :attribute  não pode ser vazio !'
        );

        return Validator::make($data, array(
            'name'        => 'required',
            'price'       => 'required',
        ), $messages);
    }
}
