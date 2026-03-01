<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('organization_user')
            ->where('role', 'member')
            ->update(['role' => 'artist']);
    }

    public function down(): void
    {
        DB::table('organization_user')
            ->where('role', 'artist')
            ->update(['role' => 'member']);
    }
};
