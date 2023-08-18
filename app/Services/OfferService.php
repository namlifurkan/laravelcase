<?php

namespace App\Services;

use App\Jobs\CheckOfferStatus;
use App\Models\Offer;
use Carbon\Carbon;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

class OfferService
{
    public function __construct(private SmsService $smsService)
    {
    }

    public function getAll(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $paginate = 10;
        return Offer::query()->paginate($paginate);
    }

    public function getOffer($id): \Illuminate\Database\Eloquent\Model|null
    {
        return Offer::query()->where('id', $id)->first();
    }

    public function create($attributes): \Illuminate\Http\JsonResponse
    {
        if (!$this->canCreateOffer($attributes)) {
            return response()->json(['message' => '24 saat içinde aynı okula form gönderemezsiniz.']);
        }

        $attributes['status'] = 'pending';
        $offer = Offer::query()->create($attributes);

        if (!$offer) {
            return response()->json(['message' => 'Bir hata oluştu.']);
        }

        //quequ eklemek mantikli, third party oldugunda
        $this->smsService->sendSms('Talebiniz alınmıştır.');
        CheckOfferStatus::dispatch()->delay(now()->addHours(48));
        return response()->json(['message' => 'Form başarılı bir şekilde oluşturuldu.']);
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

        if (!$offer) {
            return response()->json(['message' => 'Boyle bir form bulunmamaktadir.']);
        }

        if ($offer->status !== "pending") {
            return response()->json(['message' => 'Bu formun statusu daha önce değiştirilmiş.']);
        }

        $this->sendSmsToSchool($offer);
        $updated = $offer->update(['status' => 'approved']);

        if (!$updated) {
            return response()->json(['message' => 'Bir hata oluştu.']);
        }

        return response()->json(['message' => 'Form başarılı bir şekilde onaylandı.']);
    }

    public function reject($id): \Illuminate\Http\JsonResponse
    {
        $offer = Offer::query()->find($id);

        if (!$offer) {
            return response()->json(['message' => 'Boyle bir form bulunmamaktadir.']);
        }

        if ($offer->status !== "pending") {
            return response()->json(['message' => 'Bu formun statusu daha önce değiştirilmiş.']);
        }

        $this->sendSmsToSchool($offer);
        $updated = $offer->update(['status' => 'rejected']);

        if (!$updated) {
            return response()->json(['message' => 'Bir hata oluştu.']);
        }

        return response()->json(['message' => 'Form başarılı bir şekilde onaylandı.']);
    }

    private function sendSmsToSchool($offer): void
    {
        app(SmsService::class)->sendSmsToSchool($offer);
    }
}
