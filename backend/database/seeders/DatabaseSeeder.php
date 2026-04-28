<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

     $categories = [
        'Programming' => \App\Models\Category::create(['name' => 'Programming', 'slug' => 'programming']),
        'Design'      => \App\Models\Category::create(['name' => 'Design', 'slug' => 'design']),
        'Marketing'   => \App\Models\Category::create(['name' => 'Marketing', 'slug' => 'marketing']),
        'Data & AI'   => \App\Models\Category::create(['name' => 'Data & AI', 'slug' => 'data-ai']),
        'Cybersecurity'=> \App\Models\Category::create(['name' => 'Cybersecurity', 'slug' => 'cybersecurity']),
        'Management'  => \App\Models\Category::create(['name' => 'Management', 'slug' => 'management']),
    ];

    // 1. Programming (20 Jobs)
    foreach(['Full Stack Web Developer (Laravel & Vue)', 'DevOps Engineer', 'Software QA Automation Engineer'] as $title) {
        \App\Models\JobListing::factory(7)->create(['category_id' => $categories['Programming']->id, 'title' => $title]);
    }

    // 2. Data & AI (15 Jobs)
    foreach(['AI & Machine Learning Engineer', 'Senior Data Analyst'] as $title) {
        \App\Models\JobListing::factory(8)->create(['category_id' => $categories['Data & AI']->id, 'title' => $title]);
    }

    // 3. Design (15 Jobs)
    foreach(['Senior UI/UX Designer', 'Motion Graphics & 3D Artist'] as $title) {
        \App\Models\JobListing::factory(8)->create(['category_id' => $categories['Design']->id, 'title' => $title]);
    }

    // 4. Marketing, Management & Cyber (20 Jobs total)
    \App\Models\JobListing::factory(7)->create(['category_id' => $categories['Marketing']->id, 'title' => 'Growth Marketing Specialist']);
    \App\Models\JobListing::factory(7)->create(['category_id' => $categories['Management']->id, 'title' => 'Technical Product Manager']);
    \App\Models\JobListing::factory(6)->create(['category_id' => $categories['Cybersecurity']->id, 'title' => 'Cybersecurity Analyst']);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
