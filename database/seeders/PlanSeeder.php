<?php

namespace Database\Seeders;

use App\Models\Plans;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plans::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'price' => 0,
            'features' => [
                'max_links' => 5,
                'max_clicks_per_month' => 500,
                'custom_domain' => false,
                'analytics' => false,
            ]
        ]);

        Plans::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'price' => 30000, // 30k per bulan
            'features' => [
                'max_links' => 9999,
                'max_clicks_per_month' => 999999,
                'custom_domain' => true,
                'analytics' => true,
            ]
        ]);
    }
}
