<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = ['company_id', 'user_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function scopeShowAll($query, $data)
    {
        $employees = $query->with('company', 'user');

        if (isset($data['search'])) {
            $employees = $employees->whereHas('user', function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['search'] . '%');
            });
        }

        if (isset($data['order_by'])) {
            $employees = $employees->whereHas('user', function ($query) use ($data) {
                $query->orderBy($data['order_by'], $data['order_direction']);
            });
        }

        if (isset($data['limit'])) {
            $employees = $employees->take($data['limit']);
        }

        if (isset($data['offset'])) {
            $employees = $employees->skip($data['offset']);
        }

        return $employees->get();
    }
}
