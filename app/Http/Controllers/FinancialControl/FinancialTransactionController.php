<?php

namespace App\Http\Controllers\FinancialControl;

use App\Http\Controllers\Controller;
use App\Http\Resources\FinancialControl\FinancialTransactionCollection;
use App\Http\Resources\FinancialControl\FinancialTransactionResource;
use App\Models\FinancialService;
use App\Models\FinancialTransaction;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FinancialTransactionController extends Controller
{
    protected $validations = [
        'id' => 'prohibited',
        'date' => 'required|date_format:Y-m-d H:i:s',
        'description' => 'required|string',
        'credit' => 'required_without:debit|nullable|numeric',
        'debit' => 'required_without:credit|nullable|numeric',
        'note' => 'nullable|string',
        'identifier' => 'prohibited',
    ];

    public function index(Request $request, FinancialService $financialService)
    {
        $user = Auth::user();

        // verifica se o parâmetro recebido pertence ao usuário logado
        if ($user->id != $financialService->user_id) abort(404);

        // busca as transações
        $financialTransactions = $financialService->financial_transactions()->paginate($request->per_page);

        return new FinancialTransactionCollection($financialTransactions);
    }

    public function store(Request $request, FinancialService $financialService)
    {
        $user = Auth::user();

        // verifica se o parâmetro recebido pertence ao usuário logado
        if ($user->id != $financialService->user_id) abort(404);

        // valida os dados recebidos
        $validator = Validator::make($request->all(), $this->validations);
        if ($validator->fails()) {
            abort(422, $validator->errors()->toJson());
        }

        // grava a nova transação
        $financialTransaction = $financialService->financial_transactions()->create($request->all());

        return response()->json([
            'data' => new FinancialTransactionResource($financialTransaction),
            'message' => __('messages.store.success')
        ], 201);
    }

    public function show(FinancialService $financialService, FinancialTransaction $financialTransaction)
    {
        $user = Auth::user();

        // verifica se os parâmetros recebidos estão relacionados e pertencem ao usuário logado
        if ($user->id != $financialTransaction->financial_service->user_id
            || $financialTransaction->financial_service->id != $financialService->id)
            abort(404);

        return response()->json(['data' => new FinancialTransactionResource($financialTransaction)]);
    }

    public function update(Request $request, FinancialService $financialService, FinancialTransaction $financialTransaction)
    {
        $user = Auth::user();

        // verifica se os parâmetros recebidos estão relacionados e pertencem ao usuário logado
        if ($user->id != $financialTransaction->financial_service->user_id
            || $financialTransaction->financial_service->id != $financialService->id)
            abort(404);

        // valida os dados recebidos
        $validator = Validator::make($request->all(), $this->validations);
        if ($validator->fails()) {
            abort(422, $validator->errors()->toJson());
        }

        // atualiza o serviço financeiro
        try {
            $financialTransaction
                ->fill($request->all())
                ->save();
        } catch (QueryException $e) {
            abort(500, __('messages.update.error'));
        }
        return response()->json([
            'data' => new FinancialTransactionResource($financialTransaction),
            'message' => __('messages.update.success')
        ]);
    }

    public function destroy(FinancialService $financialService, FinancialTransaction $financialTransaction)
    {
        $user = Auth::user();

        // verifica se os parâmetros recebidos estão relacionados e pertencem ao usuário logado
        if ($user->id != $financialTransaction->financial_service->user_id
            || $financialTransaction->financial_service->id != $financialService->id)
            abort(404);

        // deleta o registro
        try {
            $financialTransaction->delete();
        } catch (QueryException $e) {
            abort(500, __('messages.destroy.error'));
        }

        return response()->json(['message' => __('messages.destroy.success')]);
    }
}
