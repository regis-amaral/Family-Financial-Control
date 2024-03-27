<?php

namespace App\Http\Controllers;

use App\Http\Resources\FinancialServiceResource;
use App\Models\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinancialServiceController extends BaseController
{

    public function index(Request $request): JsonResponse
    {
        return response()->json(FinancialService::all());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'alpha'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $financialService = FinancialService::create($request->all());

        return $this->sendResponse(new FinancialServiceResource($financialService),
            'criado com sucesso',
            201);

    }

    public function show(FinancialService $financialService)
    {
        return response()->json($financialService);
    }

    public function update(Request $request, FinancialService $financialService)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

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
