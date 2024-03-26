<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinancialServiceRequest;
use App\Http\Requests\UpdateFinancialServiceRequest;
use App\Http\Resources\FinancialServiceResource;
use App\Models\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
class FinancialServiceController extends BaseController
{

    public function index(Request $request): JsonResponse
    {
        return response()->json(FinancialService::all());
    }

    public function store(StoreFinancialServiceRequest $request)
    {
        $financialService = FinancialService::create($request->all());

        return response()->json(['message' => 'criado com sucesso', 'data' => new FinancialServiceResource($financialService)], 201);
    }

    public function show(FinancialService $financialService)
    {
        return response()->json($financialService);
    }

    public function update(UpdateFinancialServiceRequest $request, FinancialService $financialService)
    {
        $financialService
            ->fill($request->all())
            ->save();
        return response()->json(['message' => 'atualizado com sucesso', 'data' => new FinancialServiceResource($financialService)], 200);
    }

    public function destroy(FinancialService $financialService)
    {
        $financialService->delete();
        return response()->json(['message' => 'removido com sucesso'], 200);
    }
}
