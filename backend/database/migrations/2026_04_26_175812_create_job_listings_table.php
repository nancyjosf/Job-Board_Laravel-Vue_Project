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
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->string('company_name')->nullable();
            
            
            $table->string('company_logo')->nullable(); 
          

            $table->text('description');
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();

            $table->string('location')->nullable();
            $table->string('work_type')->default('onsite'); // remote | onsite | hybrid
            $table->string('experience_level')->nullable(); // junior | mid | senior | lead

            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->string('salary_currency', 3)->nullable(); // USD, EUR, ...

            $table->string('status')->default('draft'); // draft | published
            $table->timestamp('published_at')->nullable();
            $table->timestamp('approved_at')->nullable(); // set by admin (person 5)

            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('applications_count')->default(0);

            $table->index(['status', 'approved_at', 'published_at']);
            $table->index(['category_id', 'location']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};