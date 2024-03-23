<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinancialServiceRequest;
use App\Http\Requests\UpdateFinancialServiceRequest;
use App\Http\Resources\FinancialServiceResource;
use App\Models\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
class FinancialServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(FinancialService::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function welcome(): JsonResponse
    {
        $response['status'] = 'API Online';
        return Response::json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFinancialServiceRequest $request)
    {
        $financialService = FinancialService::create($request->all());

        return response()->json(['message' => 'criado com sucesso', 'data' => new FinancialServiceResource($financialService)], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(FinancialService $financialService)
    {
        return response()->json($financialService);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinancialService $financialService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFinancialServiceRequest $request, FinancialService $financialService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinancialService $financialService)
    {
        //
    }
}
