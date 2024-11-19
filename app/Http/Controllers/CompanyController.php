<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyIndexRequest;
use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(CompanyIndexRequest $request)
    {
        $companies = Company::showAll($request->all());

        return response()->json(['data' => $companies]);
    }

    public function store(CompanyStoreRequest $request)
    {
        $company = Company::setup($request->all());

        $company->load(['managers' => function ($query) {
            $query->with('user');
        }]);

        return response()->json(['message' => 'Company created successfully', 'data' => $company]);
    }

    public function show(Company $company)
    {
        $company = Company::find($company)->first();

        $company = $company->load(['managers' => function ($query) {
            $query->with('user');
        }]);

        return response()->json(['data' => $company]);
    }

    public function update(CompanyUpdateRequest $request, Company $company)
    {
        $company->update($request->all());

        return response()->json(['message' => "{$company->name} updated successfully", 'data' => $company]);
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json(['message' => "{$company->name} deleted successfully"]);
    }
}
