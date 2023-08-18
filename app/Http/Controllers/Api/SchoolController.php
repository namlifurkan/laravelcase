<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SchoolService;

class SchoolController extends Controller
{
    public function __construct(private SchoolService $schoolService){}

    public function index()
    {
        $schools = $this->schoolService->getAll();
        return json_decode($schools->toJson());
    }

    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $school = $this->schoolService->getSchool($id);
        return response()->json($school);
    }
}
