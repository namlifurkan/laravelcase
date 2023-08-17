<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SchoolService;

class SchoolController extends Controller
{
    protected SchoolService $schoolService;

    public function __construct(SchoolService $schoolService)
    {
        $this->schoolService = $schoolService;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        $schools = $this->schoolService->getAll();
        return response()->json(['schools' => $schools]);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $school = $this->schoolService->getSchool($id);
        return response()->json($school);
    }
}
