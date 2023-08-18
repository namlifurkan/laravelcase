<?php

use Tests\TestCase;

class RouteTest extends TestCase
{
    public function testSubmitFormRoute()
    {
        $response = $this->get(route('submit-form'));

        $response->assertStatus(200);
    }

    public function testSchoolIndexRoute()
    {
        $response = $this->get(route('school'));

        $response->assertStatus(200);
    }

    public function testSchoolShowRoute()
    {
        $schoolId = 1; // Replace with a valid school ID
        $response = $this->get(route('school.show', ['school_id' => $schoolId]));

        $response->assertStatus(200);
    }

    public function testOffersRoute()
    {
        $user = \App\Models\User::factory()->create();

        \Laravel\Sanctum\Sanctum::actingAs($user);

        $response = $this->get(route('offers'));

        $response->assertStatus(200);
    }

    public function testApproveOfferRoute()
    {
        $user = \App\Models\User::factory()->create();

        \Laravel\Sanctum\Sanctum::actingAs($user);

        $offerId = 1; // Replace with a valid offer ID
        $response = $this->get(route('approveOffer', ['offer_id' => $offerId]));

        $response->assertStatus(200);
    }

    public function testRejectOfferRoute()
    {
        $user = \App\Models\User::factory()->create();

        \Laravel\Sanctum\Sanctum::actingAs($user);

        $offerId = 1; // Replace with a valid offer ID
        $response = $this->get(route('rejectOffer', ['offer_id' => $offerId]));

        $response->assertStatus(200);
    }
}
