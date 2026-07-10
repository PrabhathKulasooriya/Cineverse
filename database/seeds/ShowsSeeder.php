<?php

use Illuminate\Database\Seeder;
use App\Shows;
use Carbon\Carbon;

class ShowsSeeder extends Seeder
{
    public function run()
    {
        // 1. Your exact pattern requirements
        $pattern = [
            '10:00:00' => 25,
            '14:00:00' => 18,
            '18:00:00' => 26,
            '22:00:00' => 27
        ];

        // 2. Set timeframe: Today to 7 days later
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(8);

        // 3. Loop through days
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');

            foreach ($pattern as $time => $movieId) {
                // Use firstOrCreate to protect your existing past data
                Shows::firstOrCreate(
                    [
                        'date' => $formattedDate, 
                        'time' => $time
                    ],
                    [
                        'movies_movie_id' => $movieId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }
        $this->command->info('Next 7 days scheduled with your specific movie pattern.');
    }
}