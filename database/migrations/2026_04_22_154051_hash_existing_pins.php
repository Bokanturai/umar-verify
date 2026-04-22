<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $users = User::whereNotNull('pin')
            ->where('pin', 'not like', '$2y$%') // Not already hashed
            ->get();

        foreach ($users as $user) {
            // Since we added the 'hashed' cast to the model, 
            // setting the pin now will automatically hash it.
            // But to be safe and explicit in migration:
            $user->pin = Hash::make($user->pin);
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot unhash pins
    }
};
