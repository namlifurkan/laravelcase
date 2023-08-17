<?php

namespace App\Services;

use App\Models\School;

class SchoolService
{
    public function getAll(): \Illuminate\Database\Eloquent\Collection|array
    {
        $query = School::query();

        return $query->get();
    }

    public function getSchool($id): \Illuminate\Database\Eloquent\Model
    {
        $query = School::query()->where('id', $id);

        return $query->first();
    }
}
