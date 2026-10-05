<?php

namespace App\Http\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class SupportController extends Controller
{
    protected function ok(mixed $data = null, string $message = 'Thành công.', int $status = 200): JsonResponse
    {
        return response()->json(['status' => true, 'message' => $message, 'data' => $data], $status);
    }

    protected function reject(string $message, int $status): never
    {
        throw new HttpResponseException(response()->json(['status' => false, 'message' => $message, 'data' => null], $status));
    }

    protected function input(Request $request, array $rules): array
    {
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json(['status' => false, 'message' => 'Dữ liệu không hợp lệ.', 'data' => ['errors' => $validator->errors()]], 422));
        }

        return $validator->validated();
    }
}
