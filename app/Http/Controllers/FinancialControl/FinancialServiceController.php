<?php

namespace App\Http\Controllers\FinancialControl;

use App\Http\Controllers\Controller;
use App\Http\Resources\FinancialControl\FinancialServiceCollection;
use App\Http\Resources\FinancialControl\FinancialServiceResource;
use App\Models\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FinancialServiceController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $financialServices = FinancialService::where('user_id',$user->id)->paginate($request->per_page);
        return new FinancialServiceCollection($financialServices);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'regex:/^[a-zA-Z\s]*$/'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', 422, $validator->errors());
        }

        $financialService = $request->user()->financial_services()->create($request->all());

        return $this->sendResponse(new FinancialServiceResource($financialService),
            201,
            'criado com sucesso'
            );

    }

    public function show(FinancialService $financialService)
    {
        if(Auth::user()->id != $financialService->user->id){
            return $this->sendError('Unauthorized.', 401);
        }
        return response()->json($financialService);
    }

    public function update(Request $request, FinancialService $financialService)
    {
        if(Auth::user()->id != $financialService->user->id){
            return $this->sendError('Unauthorized.', 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'regex:/^[a-zA-Z\s]*$/'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', 422, $validator->errors());
        }

        $financialService
            ->fill($request->all())
            ->save();
        return response()->json(['message' => 'atualizado com sucesso', 200, 'data' => new FinancialServiceResource($financialService)]);
    }

    public function destroy(FinancialService $financialService)
    {
        if(Auth::user()->id != $financialService->user->id){
            return $this->sendError('Unauthorized.', 401);
        }

        $financialService->delete();
        return response()->json(['message' => 'removido com sucesso'], 200);
    }
}
