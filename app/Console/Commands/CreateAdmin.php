<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Create default admin';

    public function handle()
    {
        $name = $this->ask('Name', 'Admin');
        $email = $this->ask('Email', 'info@schwarz-und-web.de');
        $pw = $this->secret('Password');

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($pw),
            'role' => 1
        ]);

        $this->info("Admin created successfully");
    }
}
