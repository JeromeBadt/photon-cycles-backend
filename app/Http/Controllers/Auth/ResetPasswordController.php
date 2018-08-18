<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    public function reset(ChangePasswordRequest $request): JsonResponse
    {
        $passwordReset = $this->getPasswordResetQuery($request->token)->first();

        return $passwordReset
            ? $this->changePassword($passwordReset->email, $request)
            : $this->resetFailedResponse();
    }

    public function getPasswordResetQuery(string $token): Builder
    {
        return DB::table('password_resets')->where('token', $token);
    }

    private function resetFailedResponse(): JsonResponse
    {
        return response()->json(['error' => 'Invalid token'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function changePassword(string $email, ChangePasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $email)->first();
        $user->update(['password' => $request->password]);
        $this->getPasswordResetQuery($request->token)->delete();

        return response()->json(['data' => 'Done! You can now login with your new password'], Response::HTTP_CREATED);
    }
}
