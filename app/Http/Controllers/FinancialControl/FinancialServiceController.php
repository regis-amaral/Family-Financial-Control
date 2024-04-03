<?php

namespace App\Http\Controllers\FinancialControl;

use App\Http\Controllers\Controller;
use App\Http\Resources\FinancialControl\FinancialServiceCollection;
use App\Http\Resources\FinancialControl\FinancialServiceResource;
use App\Models\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
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
            'name' => ['required', 'string', 'regex:/^[\pL\s.-]+$/u'],
        ]);

        if ($validator->fails()) {
            abort(422, $validator->errors()->toJson());
        }

        $financialService = $request->user()->financial_services()->create($request->all());

        return response()->json([
            'data' => new FinancialServiceResource($financialService),
            'message' => __('messages.store.success')
        ], 201);
    }

    public function show(FinancialService $financialService)
    {
        if (Auth::user()->cannot('update', $financialService)) {
            abort(403);
        }
        return response()->json(['data' => new FinancialServiceResource($financialService)]);
    }

    public function update(Request $request, FinancialService $financialService)
    {
        if ($request->user()->cannot('update', $financialService)) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'regex:/^[\pL\s.-]+$/u'],
        ]);

        if ($validator->fails()) {
            abort(422, $validator->errors()->toJson());
        }

        $financialService
            ->fill($request->all())
            ->save();

        return response()->json([
            'data' => new FinancialServiceResource($financialService),
            'message' => __('messages.update.success')
        ]);
    }

    public function destroy(FinancialService $financialService)
    {
        if (Auth::user()->cannot('update', $financialService)) {
            abort(403);
        }

        $financialService->delete();
        return response()->json(['message' => __('messages.destroy.success')]);
    }
}
