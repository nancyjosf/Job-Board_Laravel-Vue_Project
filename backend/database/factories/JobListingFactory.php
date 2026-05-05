<?php

namespace Database\Factories;

use App\Models\JobListing;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobListingFactory extends Factory
{
    protected $model = JobListing::class;

    public function definition(): array
    {
        $jobDetails = [
            // --- Programming & Tech ---
            'Full Stack Web Developer (Laravel & Vue)' => [
                'description' => 'Build high-performance web applications using the TALL stack or Laravel/Vue. You will be responsible for both server-side logic and frontend interactivity.',
                'responsibilities' => "• Architect scalable backend systems.\n• Develop interactive UIs with Vue 3.\n• Manage PostgreSQL/MySQL databases.",
                'requirements' => "• 3+ years experience with Laravel.\n• Strong JS skills (Composition API).\n• Experience with Docker & Git."
            ],
            'AI & Machine Learning Engineer' => [
                'description' => 'Implement cutting-edge AI models and integrate them into production environments to solve complex data problems.',
                'responsibilities' => "• Develop NLP and Computer Vision models.\n• Optimize LLM performance for production.\n• Fine-tune transformer models.",
                'requirements' => "• Proficiency in Python & PyTorch/TensorFlow.\n• Understanding of Deep Learning architectures.\n• MSc in CS or related field."
            ],
            'Software QA Automation Engineer' => [
                'description' => 'Ensure software reliability by creating automated test suites and performing rigorous quality checks.',
                'responsibilities' => "• Build automation frameworks (Cypress/Playwright).\n• Execute regression and stress tests.\n• Collaborate with Devs on bug resolution.",
                'requirements' => "• Experience with Selenium or Cypress.\n• Strong logic and edge-case detection.\n• Knowledge of CI/CD pipelines."
            ],

            // --- Design ---
            'Senior UI/UX Designer' => [
                'description' => 'Lead the design process from discovery to final hand-off, ensuring a world-class user experience.',
                'responsibilities' => "• Conduct user interviews & persona building.\n• Create high-fidelity interactive prototypes.\n• Maintain and expand the design system.",
                'requirements' => "• Mastery of Figma & Auto-layout.\n• Portfolio showcasing UX problem-solving.\n• Ability to prototype complex interactions."
            ],
            'Motion Graphics & 3D Artist' => [
                'description' => 'Create stunning visual stories through 2D/3D animation for marketing campaigns and product explainers.',
                'responsibilities' => "• Animate brand assets and logos.\n• Create 3D renders using Blender/C4D.\n• Collaborate on video editing tasks.",
                'requirements' => "• Expert in After Effects & Premiere Pro.\n• Knowledge of 3D modeling (Blender).\n• Strong sense of timing and motion."
            ],

            // --- Marketing & Business ---
            'Growth Marketing Specialist' => [
                'description' => 'Drive user acquisition and retention through data-driven experiments and multi-channel marketing.',
                'responsibilities' => "• Manage Meta & Google Ads campaigns.\n• Optimize conversion rate (CRO).\n• Run A/B tests on landing pages.",
                'requirements' => "• Proven track record in scaling startups.\n• Analytical mindset (SQL is a plus).\n• Expert in Google Analytics 4."
            ],
            'SEO & Content Strategist' => [
                'description' => 'Own the organic growth strategy by optimizing on-page content and building high-authority backlink profiles.',
                'responsibilities' => "• Perform advanced keyword research.\n• Audit site structure for search bots.\n• Manage technical SEO & Core Web Vitals.",
                'requirements' => "• Mastery of Ahrefs, Semrush, & GSC.\n• Basic understanding of HTML/CSS.\n• Excellent copywriting skills."
            ],

            // --- Management & Data ---
            'Technical Product Manager' => [
                'description' => 'Bridge the gap between business needs and technical execution. You will define the roadmap and prioritize the backlog.',
                'responsibilities' => "• Write detailed PRDs and user stories.\n• Lead Scrum ceremonies (Daily, Sprint).\n• Coordinate between Stakeholders & Devs.",
                'requirements' => "• Technical background (former Dev preferred).\n• Proficiency in Jira/Linear.\n• Excellent leadership skills."
            ],
            'Senior Data Analyst' => [
                'description' => 'Turn raw data into actionable insights through sophisticated visualization and statistical modeling.',
                'responsibilities' => "• Build automated BI dashboards (Tableau/PowerBI).\n• Perform cohort and churn analysis.\n• Provide data for executive decision-making.",
                'requirements' => "• Expert level SQL & Python.\n• Strong statistical knowledge.\n• Experience with BigQuery or Snowflake."
            ],
            'Cybersecurity Analyst' => [
                'description' => 'Protect our digital assets by performing vulnerability assessments and maintaining security protocols.',
                'responsibilities' => "• Perform penetration testing.\n• Monitor logs for suspicious activity.\n• Ensure compliance (GDPR/SOC2).",
                'requirements' => "• OSCP or CISSP certification.\n• Deep knowledge of network protocols.\n• Experience with SIEM tools."
            ],
        ];

        $title = $this->faker->randomElement(array_keys($jobDetails));
        $details = $jobDetails[$title];
        $companyName = $this->faker->randomElement(['Wayzon', 'Google', 'Meta', 'Amazon', 'Salla', 'Zid', 'Freelance Hub']);

        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $title,
            'company_name' => $companyName,
            'company_logo' => 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($companyName) . '&backgroundColor=6366f1&fontFamily=Arial&fontWeight=700',
            'description' => $details['description'],
            'responsibilities' => $details['responsibilities'],
            'requirements' => $details['requirements'],
            'benefits' => "• Health & Dental Insurance.\n• 100% Remote Opportunity.\n• Annual Learning Budget ($2000).\n• Quarterly Performance Bonuses.",
            'location' => $this->faker->randomElement(['Dubai, UAE', 'Riyadh, KSA', 'Cairo, Egypt', 'Remote', 'London, UK', 'New York, USA']),
            'work_type' => $this->faker->randomElement(['remote', 'onsite', 'hybrid']),
            'experience_level' => $this->faker->randomElement(['junior', 'mid', 'senior', 'lead']),
            'salary_min' => $this->faker->numberBetween(3000, 5000),
            'salary_max' => $this->faker->numberBetween(6000, 12000),
            'salary_currency' => 'USD',
            'status' => 'published',
            'published_at' => now(),
            'approved_at' => now(),
        ];
    }
}
