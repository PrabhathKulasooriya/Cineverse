<?php

use Illuminate\Database\Seeder;
use App\Shows;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingsSeeder extends Seeder
{
    public function run()
    {
        // 1. Define base pricing and static data based on your specific database
        $snackMenu = [
            4  => 200, // COCA COLA (S)
            5  => 300, // COCA COLA (M)
            6  => 350, // COCA COLA (L)
            13 => 150, // POP CORN (S)
            14 => 200, // POP CORN (M)
            15 => 250  // POP CORN (L)
        ];
        
        // Allowed Customers (Role 4) - MUST use CARD
        $customers = [
            ['id' => 84, 'name' => 'ISURU PRABHATH KULASOORIYA', 'email' => 'prabhath.kulasooriya@gmail.com'],
            ['id' => 85, 'name' => 'ISHARA SAHENSHI', 'email' => 'sahenshi@gmail.com'],
            ['id' => 86, 'name' => 'DILNETH KULASOORIYA', 'email' => 'dilneth@gmail.com'],
            ['id' => 88, 'name' => 'PRABHATHA KULASOORIYAA', 'email' => 'prabhath@gmail.com'],
            ['id' => 92, 'name' => 'ISURU KULASOORIYA', 'email' => 'isuruprabhathkulasooriya@gmail.com'],
            ['id' => 94, 'name' => 'VIDULA AKASH', 'email' => 'vidulaakash@gmail.com'],
            ['id' => 95, 'name' => 'SANDALI AKARSHA', 'email' => 'sandaliakarsha@gmail.com'],
            ['id' => 97, 'name' => 'UMESHA KULASOORIYA', 'email' => 'umeshakulasooriya@gmail.com'],
            ['id' => 98, 'name' => 'THATHSARA BANDARA', 'email' => 'thathsara@gmail.com'],
            ['id' => 99, 'name' => 'DILNETH KULASOORIYA', 'email' => 'dilneth.kulasooriya11@gmail.com'],
            ['id' => 102, 'name' => 'KANTHI RATHNAYAKE', 'email' => 'kanthirathnayake@gmail.com'],
            ['id' => 107, 'name' => 'GITHMIN JAYAWARDHANA', 'email' => 'githmin@gmail.com'],
            ['id' => 114, 'name' => 'ISURU PRABHATH', 'email' => 'isurup@gmail.com'],
            ['id' => 115, 'name' => 'TEST TEST', 'email' => 'test@gmail.com'],
            ['id' => 118, 'name' => 'TEST TEST', 'email' => 'testtttttttt@gmail.com'],
            ['id' => 119, 'name' => 'CINEVERSE CUSTOMER', 'email' => 'customer@gmail.com']
        ];

        // Allowed Ticket Counter Staff (Role 3) - MUST use CASH
        $counterStaff = [
            ['id' => 117, 'name' => 'COUNTER EMPLOYEE', 'email' => 'counter@cineverse.lk']
        ];
        
        $shows = Shows::where('date', '>=', Carbon::now()->format('Y-m-d'))->get();

        if ($shows->isEmpty()) {
            $this->command->info('No shows found! Please run PastShowsSeeder first.');
            return;
        }

        $this->command->info('Generating realistic bookings. This may take a minute...');

        foreach ($shows as $show) {
            // A theater has 100 seats in your DB (1-64 Standard, 65-100 Prime)
            $availableSeats = range(1, 100);
            shuffle($availableSeats);

            // Determine theater occupancy: Never empty, never fully booked (25% to 95% full)
            $targetOccupancy = rand(25, 95);
            $bookedCount = 0;

            $showDateTime = Carbon::parse($show->date . ' ' . $show->time);

            while ($bookedCount < $targetOccupancy) {
                // A single booking usually consists of 1 to 8 seats
                $seatsToBook = rand(1, 8);
                
                // Prevent overbooking the target occupancy
                if ($bookedCount + $seatsToBook > $targetOccupancy) {
                    $seatsToBook = $targetOccupancy - $bookedCount;
                }

                $bookingSeats = array_splice($availableSeats, 0, $seatsToBook);
                
                // 2. Profile Selection based on your rules
                $userTypeRoll = rand(1, 100);
                
                if ($userTypeRoll <= 60) {
                    // 60% chance it's a registered Customer (Role 4) -> CARD ONLY
                    $selectedUser = $customers[array_rand($customers)];
                    $userId = $selectedUser['id'];
                    $customerName = $selectedUser['name'];
                    $customerEmail = $selectedUser['email'];
                    $paymentMethod = 'CARD';
                } elseif ($userTypeRoll <= 75) {
                    // 15% chance it's processed by Counter Employee (Role 3) -> CASH ONLY
                    $selectedUser = $counterStaff[array_rand($counterStaff)];
                    $userId = $selectedUser['id'];
                    // The customer name should be random for walk-ins, but tied to the counter's ID
                    $customerName = 'GUEST WALK-IN ' . rand(100, 999);
                    $customerEmail = null; // Walk-ins might not provide email
                    $paymentMethod = 'CASH';
                } else {
                    // 25% chance it's a standard unregistered Guest -> CARD OR CASH
                    $userId = null;
                    $customerName = 'GUEST ' . rand(1000, 9999);
                    $customerEmail = 'guest' . rand(1000, 9999) . '@gmail.com';
                    $paymentMethod = rand(0, 1) ? 'CARD' : 'CASH';
                }

                // 3. Calculate exact seat cost dynamically based on Seat IDs
                $seatCost = 0;
                $seatInserts = [];
                foreach ($bookingSeats as $seatId) {
                    // IDs 1-64 are Standard (500), 65-100 are Prime (750)
                    $price = ($seatId <= 64) ? 500 : 750;
                    $seatCost += $price;
                }

                // 4. Generate Snack Data (65% chance the customer buys snacks)
                $snackCost = 0;
                $bookingSnacksData = [];
                
                if (rand(1, 100) <= 65) {
                    // Pick 1 to 3 different types of snacks (Multiple variants)
                    $numSnackTypes = rand(1, 3);
                    $snackKeys = (array) array_rand($snackMenu, $numSnackTypes);

                    foreach ($snackKeys as $sId) {
                        $qty = rand(1, 5); // Realistic quantity (1 to 5  of this specific variant)
                        $price = $snackMenu[$sId];
                        $snackCost += ($qty * $price);
                        
                        $bookingSnacksData[] = [
                            'snacks_idsnacks' => $sId,
                            'quantity' => $qty,
                            'price' => $price
                        ];
                    }
                }

                $grandTotal = $seatCost + $snackCost;

                // Create a random booking timestamp (30 mins to 7 days before the show)
                $bookingDate = $showDateTime->copy()->subMinutes(rand(30, 10080));

                // 5. INSERT: Bookings Table (Amount = Seats Only)
                // Extract the first seat ID for the composite key
                $firstSeatId = $bookingSeats[0];
                
                // Use the booking's actual Minutes and Seconds as the time component
                $timeComponent = $bookingDate->format('is');

                // Generate the booking ID using your exact system logic
                $bookingId = (int) (
                    str_pad($show->show_id, 2, '0', STR_PAD_LEFT) .
                    str_pad($firstSeatId, 3, '0', STR_PAD_LEFT) .
                    $timeComponent
                );

                DB::table('bookings')->insertGetId([
                    'booking_id' => $bookingId,
                    'shows_show_id' => $show->show_id,
                    'movies_movie_id' => $show->movies_movie_id,
                    'master_user_idmaster_user' => $userId,
                    'amount' => $seatCost,
                    'customer_name' => $customerName,
                    'email' => $customerEmail,
                    'payment_status' => 'PAID',
                    'created_at' => $bookingDate,
                    'updated_at' => $bookingDate,
                ]);

                // 6. INSERT: Booked Seats Table
                foreach ($bookingSeats as $seatId) {
                    $seatInserts[] = [
                        'bookings_booking_id' => $bookingId,
                        'seats_seat_id' => $seatId,
                        'created_at' => $bookingDate,
                        'updated_at' => $bookingDate,
                    ];
                }
                DB::table('booked_seats')->insert($seatInserts);

                // 7. INSERT: Booking Snacks Table
                if (!empty($bookingSnacksData)) {
                    $snackInserts = [];
                    foreach ($bookingSnacksData as $snack) {
                        $snackInserts[] = [
                            'booking_id' => $bookingId,
                            'snacks_idsnacks' => $snack['snacks_idsnacks'],
                            'quantity' => $snack['quantity'],
                            'price' => $snack['price'],
                            'created_at' => $bookingDate,
                            'updated_at' => $bookingDate,
                        ];
                    }
                    DB::table('booking_snacks')->insert($snackInserts);
                }

                // 8. INSERT: Payments Table (Amount = Grand Total)
                DB::table('payments')->insert([
                    'bookings_booking_id' => $bookingId,
                    'email' => $customerEmail,
                    'amount' => $grandTotal,
                    'method' => $paymentMethod,
                    'created_at' => $bookingDate,
                    'updated_at' => $bookingDate,
                ]);

                $bookedCount += $seatsToBook;
            }
        }

        $this->command->info('All mathematically synced booking data has been generated successfully!');
    }
}