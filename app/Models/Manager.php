<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manager extends Model
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
        $managers = $query->with('user');

        if (isset($data['search'])) {
            $managers = $managers->whereHas('user', function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['search'] . '%');
            });
        }

        if (isset($data['order_by'])) {
            $managers = $managers->whereHas('user', function ($query) use ($data) {
                $query->orderBy($data['order_by'], $data['order_direction']);
            });
        }

        if (isset($data['limit'])) {
            $managers = $managers->take($data['limit']);
        }

        if (isset($data['offset'])) {
            $managers = $managers->skip($data['offset']);
        }

        return $managers->get();
    }
}
