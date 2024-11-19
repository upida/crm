<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'email', 'phone'];

    public function managers(): HasMany
    {
        return $this->hasMany(Manager::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public static function setup(array $data): self
    {
        try {
            DB::beginTransaction();

            $company = self::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);
    
            $company->managers()->create([
                'user_id' => $data['manager']['user_id'],
            ]);

            $manager = User::find($data['manager']['user_id']);

            $manager->role = 'manager';
            $manager->save();
            
            DB::commit();

            return $company;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public static function scopeShowAll($query, array $data): array
    {
        $companies = $query->get();

        if (isset($data['search'])) {
            $companies->where('name', 'like', '%'.$data['search'].'%');
        }

        if (isset($data['order_by'])) {
            $companies->orderBy($data['order_by'], $data['order_direction'] ?? 'asc');
        }

        if (isset($data['limit'])) {
            $companies->limit($data['limit']);
        }

        if (isset($data['offset'])) {
            $companies->offset($data['offset']);
        }

        return $companies->toArray();
    }
}
