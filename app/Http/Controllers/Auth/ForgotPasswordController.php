<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordController extends Controller
{
    public function sendEmail(Request $request): JsonResponse
    {
        if (!$this->validateEmail($request->email)) {
            return $this->failedResponse();
        }

        $this->send($request->email);
        return $this->successResponse();
    }

    public function send($email)
    {
        $token = $this->createToken($email);
        Mail::to($email)->send(new ResetPasswordMail($token));
    }

    public function createToken(string $email): string
    {
        $oldToken = DB::table('password_resets')->where('email', $email)->first();
        if ($oldToken) {
            return $oldToken->token;
        }

        $token = str_random(60);
        $this->saveToken($token, $email);
        return $token;
    }

    public function saveToken(string $token, string $email)
    {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }

    public function validateEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function successResponse(): JsonResponse
    {
        return response()->json([
            'data' => 'Reset Email sent, please check your inbox'
        ]);
    }

    public function failedResponse(): JsonResponse
    {
        return response()->json([
            'error' => 'Email doesn\'t exist'
        ], Response::HTTP_NOT_FOUND);
    }
}
