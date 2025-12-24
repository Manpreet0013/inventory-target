<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index() {
        $companies = Company::all();
        return view('admin.companies.index', compact('companies'));
    }

    public function create() {
        return view('admin.companies.create');
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        Company::create($request->only('name'));
        return redirect()->route('admin.companies.index')->with('success','Company created!');
    }

    public function edit(Company $company) {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company) {
        $request->validate(['name' => 'required|string|max:255']);
        $company->update($request->only('name'));
        return redirect()->route('admin.companies.index')->with('success','Company updated!');
    }

    public function destroy(Company $company) {
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success','Company deleted!');
    }
}
