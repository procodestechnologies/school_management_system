<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Production's student_details carries a `user_id` column that this
 * codebase never created - schema drift from an imported dump. It
 * duplicates `student_id` exactly (verified: every row sets both, and
 * they agree), and it is NOT NULL with no default.
 *
 * User::studentUserDetails() used to write it, by pointing its foreign
 * key there; now that the relation correctly uses student_id, nothing
 * populates user_id, and creating a student would fail on a column the
 * application doesn't know exists.
 *
 * Making it nullable is the conservative repair: existing rows keep their
 * values, new rows simply leave it empty, and no data is destroyed.
 * Dropping the column outright is the eventual tidy-up, once you're
 * satisfied nothing outside this codebase reads it.
 *
 * Guarded, because on a database built from this repo's migrations the
 * column has never existed and there is nothing to change.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('student_details', 'user_id')) {
            return;
        }

        Schema::table('student_details', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Deliberately not reinstating NOT NULL: by the time this runs,
        // rows created since may legitimately have a null there, and the
        // migration would fail on them.
    }
};
