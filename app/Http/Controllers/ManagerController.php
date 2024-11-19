<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManagerIndexRequest;
use App\Http\Requests\ManagerUpdateRequest;
use App\Models\Company;
use App\Models\Manager;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index(ManagerIndexRequest $request, Company $company)
    {
        $managers = $company->managers()->showAll($request->validated());

        $managers = $managers->map(function ($manager) {
            return $manager->user;
        });

        return response()->json([ 'data' => $managers ]);
    }

    public function show(Company $company, Manager $manager)
    {
        $manager = $manager->load('user');
        return response()->json([ 'data' => $manager ]);
    }

    public function update(ManagerUpdateRequest $request, Company $company, Manager $manager)
    {
        $manager->user()->update($request->validated());
        return response()->json([ 'message' => "Manager {$manager->name} updated successfully", 'data' => $manager ]);
    }
}
