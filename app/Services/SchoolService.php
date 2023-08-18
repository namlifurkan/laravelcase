<?php

namespace App\Services;

use App\Models\School;

class SchoolService
{
    public function getAll(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $paginate = 10;

        return School::query()->paginate($paginate);
    }

    public function getSchool($id): \Illuminate\Database\Eloquent\Model|null
    {
        $query = School::query()->where('id', $id);

        return $query->first();
    }
}
