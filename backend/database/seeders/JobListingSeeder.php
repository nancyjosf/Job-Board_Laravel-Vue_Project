<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\JobListing;
use Illuminate\Database\Seeder;

class JobListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()->get();

        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::query()->get();
        }

        foreach ($categories as $category) {
            JobListing::factory()
                ->count(12)
                ->create([
                    'category_id' => $category->id,
                ]);
        }
    }
}
