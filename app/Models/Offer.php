<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $fillable = ['first_name', 'surname', 'phone', 'email', 'gsm', 'school_id', 'date_of_birth', 'status'];
}
