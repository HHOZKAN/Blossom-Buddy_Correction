<?php

namespace App\Interfaces;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface AuthControllerInterface
{
    public function register(Request $request): JsonResponse;
    public function login(Request $request): JsonResponse;
    public function logout(Request $request): JsonResponse;
    public function me(Request $request): JsonResponse;
}
