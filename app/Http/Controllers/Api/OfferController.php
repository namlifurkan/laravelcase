<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFormRequest;
use App\Services\OfferService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    private OfferService $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    public function submitForm(CreateFormRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->offerService->create($request->all());
    }

    public function list(): \Illuminate\Http\JsonResponse
    {
        $offers = $this->offerService->getAll();

        return response()->json(['offers' => $offers]);
    }

    public function approveOffer($id): \Illuminate\Http\JsonResponse
    {
        $response = $this->offerService->approve($id);

        if ($response) {
            return response()->json(['message' => 'Form başarılı bir şekilde onaylandı.']);
        } else {
            return response()->json(['message' => 'Bir hata olustu.']);
        }
    }

    public function rejectOffer($id): \Illuminate\Http\JsonResponse
    {
        $response = $this->offerService->reject($id);

        if ($response) {
            return response()->json(['message' => 'Form başarılı bir şekilde reddedildi.']);
        } else {
            return response()->json(['message' => 'Bir hata olustu.']);
        }
    }
}
