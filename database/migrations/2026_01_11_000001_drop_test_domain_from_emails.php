<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Renames the seeded staff logins from "@highlandproperties.test" to
 * "@highlandproperties". Existing installs are updated in place, so nobody
 * has to edit the accounts by hand — and the passwords are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('email', 'like', '%@highlandproperties.test')
            ->update(['email' => DB::raw("REPLACE(email, '@highlandproperties.test', '@highlandproperties')")]);

        DB::table('team_members')
            ->where('email', 'like', '%@highlandproperties.test')
            ->update(['email' => DB::raw("REPLACE(email, '@highlandproperties.test', '@highlandproperties')")]);

        DB::table('settings')
            ->where('value', 'like', '%@highlandproperties.test')
            ->update(['value' => DB::raw("REPLACE(value, '@highlandproperties.test', '@highlandproperties')")]);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'like', '%@highlandproperties')
            ->update(['email' => DB::raw("CONCAT(email, '.test')")]);
    }
};
