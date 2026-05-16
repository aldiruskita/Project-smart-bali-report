<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Jalan Rusak', 'icon' => 'road', 'color' => '#ef4444', 'urgency_level' => 4],
            ['name' => 'Sampah', 'icon' => 'delete', 'color' => '#f59e0b', 'urgency_level' => 3],
            ['name' => 'Banjir', 'icon' => 'water', 'color' => '#3b82f6', 'urgency_level' => 5],
            ['name' => 'Lampu Jalan', 'icon' => 'lightbulb', 'color' => '#eab308', 'urgency_level' => 2],
            ['name' => 'Pohon Tumbang', 'icon' => 'forest', 'color' => '#22c55e', 'urgency_level' => 4],
            ['name' => 'Drainase', 'icon' => 'plumbing', 'color' => '#06b6d4', 'urgency_level' => 3],
            ['name' => 'Fasilitas Umum', 'icon' => 'location_city', 'color' => '#8b5cf6', 'urgency_level' => 2],
            ['name' => 'Lainnya', 'icon' => 'more_horiz', 'color' => '#6b7280', 'urgency_level' => 1],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
