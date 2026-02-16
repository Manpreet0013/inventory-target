<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Example: create company first
        $company = Company::firstOrCreate(['name'=>'Default Company']);

        // Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'info@servocaregroup.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id
        ]);
        $admin->assignRole('Admin');

        // Inventory Manager
        $inv = User::create([
            'name' => 'Inventory Manager',
            'email' => 'inventory@servocaregroup.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id
        ]);
        $inv->assignRole('Inventory Manager');
        
         $acc = User::create([
            'name' => 'Accountant User',
            'email' => 'accountant@servocaregroup.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id
        ]);
        $acc->assignRole('Accountant');

        // Executive
        $exe = User::create([
            'name' => 'Executive User',
            'email' => 'executive@servocaregroup.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id
        ]);
        $exe->assignRole('Executive');

        // Accountant
       
    }
}
