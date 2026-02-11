<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Testimonial::create([
            'name' => 'Ahmed R.',
            'role' => 'Gamer',
            'content' => 'The best gaming setup I have ever bought. Fast delivery and top quality!',
            'rating' => 5,
        ]);

        Testimonial::create([
            'name' => 'Sarah K.',
            'role' => 'Content Creator',
            'content' => 'Customer service was amazing. They helped me build my custom PC from scratch.',
            'rating' => 5,
        ]);

        Testimonial::create([
            'name' => 'John D.',
            'role' => 'Developer',
            'content' => 'Great prices for the components. Packaging was secure.',
            'rating' => 4,
        ]);
    }
}
