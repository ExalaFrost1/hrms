<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        // Check if user already exists
        if (User::where('email', $this->email)->exists()) {
            Notification::make()
                ->title('User Already Exists')
                ->danger()
                ->body("A user with email {$this->email} already exists.")
                ->send();

            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        // Create the user if not exists
        User::create([
            'name' => 'New User', // you can pass a custom name
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Notification::make()
            ->title("User Created for employee {$this->full_name}")
            ->success()
            ->body("User {$this->email} has been created successfully.")
            ->send();

    }

}
