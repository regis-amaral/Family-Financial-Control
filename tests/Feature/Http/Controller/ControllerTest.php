<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Mockery\MockInterface;
use App\Http\Controllers\Controller;

class ControllerStub extends Controller {
    public function sendResponse($result, $code = 200, $message = ''): JsonResponse
    {
        return parent::sendResponse($result, $code, $message);
    }
};

// pode lançar uma exceção ao receber um response code inesperado
test('can throw an exception when receiving an unexpected response code', function () {
    $result = ['data' => 'result data'];
    $code = 404;
    $message = __('messages.store.success');
    $controllerStub = new ControllerStub();
    $response = $controllerStub->sendResponse($result, $code, $message);
})->throws(InvalidArgumentException::class, 'Invalid response code.');
