<?php

namespace App\Services;

use App\Jobs\CheckOfferStatus;
use App\Models\Offer;
use Carbon\Carbon;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

class OfferService
{
    private SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection|array
    {
        $query = Offer::query();

        return $query->get();
    }

    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function create($attributes): \Illuminate\Http\JsonResponse
    {
        if ($this->canCreateOffer($attributes)) {
            $attributes['status'] = 'pending';
            $offer = Offer::create($attributes);

            if ($offer) {
                $this->smsService->sendSms('Talebiniz alınmıştır.');
                CheckOfferStatus::dispatch()->delay(now()->addHours(48));
                return response()->json(['message' => 'Form başarılı bir şekilde oluşturuldu.']);
            }
        } else {
            return response()->json(['message' => '24 saat içinde aynı okula form gönderemezsiniz.']);
        }

        return response()->json(['message' => 'Bir hata oluştu.']);
    }

    private function canCreateOffer($attributes): bool|\Illuminate\Http\JsonResponse
    {
        //user can't create an offer for same school in 24 hours

        $times = Offer::query()
            ->where('school_id', $attributes['school_id'])
            ->where('email', $attributes['email'])
            ->where('phone', $attributes['phone'])
            ->pluck('created_at')->toArray();

        $now = Carbon::now();

        foreach ($times as $time) {
            $timeDifference = $now->diffInHours($time);
            if ($timeDifference <= 24) {
                return false;
            }
        }

        return true;
    }

    public function approve($id)
    {
        $offer = Offer::find($id);

        return $offer->update(['status' => 'approved']);
    }

    public function reject($id)
    {
        $offer = Offer::find($id);

        return $offer->update(['status' => 'rejected']);
    }
}