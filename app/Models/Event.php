<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use \DateTime;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'address',
        'phone',
        'registration_fee',
        'start_date',
        'end_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function startDate()
    {
        return date('d/m/Y H:i', strtotime($this->start_date));
    }

    public function endDate()
    {
        return date('d/m/Y H:i', strtotime($this->end_date));
    }

    public function eventDuration()
    {
        return date_diff(date_create($this->start_date), date_create($this->end_date))->format('%d dias');
    }
}
