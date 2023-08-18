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

    public function getAll(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $paginate = 10;
        return Offer::query()->paginate($paginate);
    }

    public function getOffer($id): \Illuminate\Database\Eloquent\Model|null
    {
        $query = Offer::query()
            ->where('id', $id);

        return $query->first();
    }

    public function create($attributes): \Illuminate\Http\JsonResponse
    {
        if ($this->canCreateOffer($attributes)) {
            $attributes['status'] = 'pending';
            $offer = Offer::query()->create($attributes);

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

    public function approve($id): \Illuminate\Http\JsonResponse
    {
        $offer = Offer::query()->find($id);

        if ($offer) {
            if ($offer->status === "pending") {
                $this->sendSmsToSchool($offer);
                $updated = $offer->update(['status' => 'approved']);
                if ($updated) {
                    return response()->json(['message' => 'Form başarılı bir şekilde onaylandı.']);
                }
            } else {
                return response()->json(['message' => 'Bu formun statusu daha önce değiştirilmiş.']);
            }
        } else {
            return response()->json(['message' => 'Boyle bir form bulunmamaktadir.']);
        }

        return response()->json(['message' => 'Bir hata oluştu.']);
    }

    public function reject($id): \Illuminate\Http\JsonResponse
    {
        $offer = Offer::query()->find($id);

        if ($offer) {
            if ($offer->status === "pending") {
                $this->sendSmsToSchool($offer);
                $updated = $offer->update(['status' => 'rejected']);
                if ($updated) {
                    return response()->json(['message' => 'Form başarılı bir şekilde reddedildi.']);
                }
            } else {
                return response()->json(['message' => 'Bu formun statusu daha önce değiştirilmiş.']);
            }
        } else {
            return response()->json(['message' => 'Boyle bir form bulunmamaktadir.']);
        }

        return response()->json(['message' => 'Bir hata oluştu.']);
    }

    private function sendSmsToSchool($offer): void
    {
        app(SmsService::class)->sendSmsToSchool($offer);
    }
}
