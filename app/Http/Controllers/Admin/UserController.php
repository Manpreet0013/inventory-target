<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index() {
        $users = User::with('company','roles')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create() {
        $companies = Company::all();
        $roles = Role::all();
        return view('admin.users.create', compact('companies','roles'));
    }

    public function store(Request $request) {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:6|confirmed',
            'role'=>'required|exists:roles,name',
            'company_id'=>'nullable|exists:companies,id'
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
            'company_id'=>$request->company_id
        ]);

        $user->assignRole($request->role);

        $user->notify(new \App\Notifications\NewUserNotification($user->name));

        return redirect()->route('admin.users.index')->with('success','User created!');
    }

    public function edit(User $user) {
        $companies = Company::all();
        $roles = Role::all();
        return view('admin.users.edit', compact('user','companies','roles'));
    }

    public function update(Request $request, User $user) {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email,'.$user->id,
            'role'=>'required|exists:roles,name',
            'company_id'=>'nullable|exists:companies,id'
        ]);

        $user->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'company_id'=>$request->company_id
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success','User updated!');
    }

    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success','User deleted!');
    }

    public function profile(User $user) {
        return view('admin.users.profile', compact('user'));
    }
}
