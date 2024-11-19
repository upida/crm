<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeIndexRequest;
use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(EmployeeIndexRequest $request, Company $company)
    {
        $employees = $company->employees()->showAll($request->validated());
        return response()->json([ 'data' => $employees ]);
    }

    public function show(Company $company, Employee $employee)
    {
        $employee = $employee->load('user');
        return response()->json([ 'data' => $employee ]);
    }

    public function store(EmployeeStoreRequest $request, Company $company)
    {
        $employee = $company->employees()->create($request->validated());

        $employee->load('user');

        return response()->json([ 'message' => "Employee {$employee->user->name} created successfully", 'data' => $employee ]);
    }

    public function update(EmployeeUpdateRequest $request, Company $company, Employee $employee)
    {
        $employee->user->update($request->validated());

        return response()->json([ 'message' => "Employee {$employee->user->name} updated successfully", 'data' => $employee ]);
    }

    public function destroy(Company $company, Employee $employee)
    {
        $employee->delete();

        return response()->json([ 'message' => "Employee {$employee->user->name} deleted successfully" ]);
    }
}
