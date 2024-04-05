<?php

namespace App\Http\Controllers\FinancialControl;

use App\Http\Controllers\Controller;
use App\Http\Resources\FinancialControl\FinancialServiceCollection;
use App\Http\Resources\FinancialControl\FinancialServiceResource;
use App\Models\FinancialService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FinancialServiceController extends Controller
{
    protected function validations($request)
    {
        return [
            'id' => 'prohibited',
            'name' => [
                'required',
                'string',
                'regex:/^[\pL\s.-]+$/u',
                Rule::unique('financial_services')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user()->id);
                }),
            ]
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // busca os serviços do usuário
        $financialServices = $user->financial_services()->paginate($request->per_page);

        return new FinancialServiceCollection($financialServices);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // valida os dados
        $validator = Validator::make($request->all(), $this->validations($request));
        if ($validator->fails()) {
            abort(422, $validator->errors()->toJson());
        }

        // grava o novo serviço financeiro
        $financialService = $user->financial_services()->create($request->all());

        return response()->json([
            'data' => new FinancialServiceResource($financialService),
            'message' => __('messages.store.success')
        ], 201);
    }

    public function show(FinancialService $financialService)
    {
        $user = Auth::user();

        // verifica se o parâmetro recebido pertence ao usuário logado
        if ($user->id != $financialService->user_id) abort(404);

        return response()->json(['data' => new FinancialServiceResource($financialService)]);
    }

    public function update(Request $request, FinancialService $financialService)
    {
        $user = Auth::user();

        // verifica se o parâmetro recebido pertence ao usuário logado
        if ($user->id != $financialService->user_id) abort(404);

        // valida os dados
        $validator = Validator::make($request->all(), $this->validations($request));
        if ($validator->fails()) {
            abort(422, $validator->errors()->toJson());
        }

        // atualiza o serviço financeiro
        try {
            $financialService
                ->fill($request->all())
                ->save();
        } catch (QueryException $e) {
            abort(500, __('messages.update.error'));
        }

        return response()->json([
            'data' => new FinancialServiceResource($financialService),
            'message' => __('messages.update.success')
        ]);
    }

    public function destroy(FinancialService $financialService)
    {
        $user = Auth::user();

        // verifica se o parâmetro recebido pertence ao usuário logado
        if ($user->id != $financialService->user_id) abort(404);

        // deleta o registro
        try {
            $financialService->delete();
        } catch (QueryException $e) {
            abort(500, __('messages.destroy.error'));
        }

        return response()->json(['message' => __('messages.destroy.success')]);
    }
}
