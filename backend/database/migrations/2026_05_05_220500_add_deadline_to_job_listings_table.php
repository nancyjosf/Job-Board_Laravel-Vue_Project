<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('job_listings', 'deadline')) {
            Schema::table('job_listings', function (Blueprint $table) {
                $table->date('deadline')->nullable()->after('salary_max');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('job_listings', 'deadline')) {
            Schema::table('job_listings', function (Blueprint $table) {
                $table->dropColumn('deadline');
            });
        }
    }
};
