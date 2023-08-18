<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFormRequest;
use App\Services\OfferService;
use Illuminate\Http\JsonResponse;

class OfferController extends Controller
{
    public function __construct(private OfferService $offerService){}

    public function submitForm(CreateFormRequest $request): JsonResponse
    {
        return $this->offerService->create($request->all());
    }

    public function list(): array
    {
        $offers = $this->offerService->getAll();

        return json_decode($offers->toJson(), true);
    }

    public function approveOffer(int $id): JsonResponse
    {
        return $this->offerService->approve($id);
    }

    public function rejectOffer(int $id): JsonResponse
    {
        return $this->offerService->reject($id);
    }
}
