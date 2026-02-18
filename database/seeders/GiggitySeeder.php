<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\OrganizationRole;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GiggitySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create organization
        $org = Organization::create([
            'name' => 'Demo Artist Management',
            'slug' => 'demo-artist-management',
            'settings' => [],
        ]);

        // 2. Create users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@giggity.test',
            'password' => Hash::make('password'),
            'current_organization_id' => $org->id,
        ]);
        $org->users()->attach($admin, ['role' => OrganizationRole::ADMIN->value]);

        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@giggity.test',
            'password' => Hash::make('password'),
            'current_organization_id' => $org->id,
        ]);
        $org->users()->attach($manager, ['role' => OrganizationRole::MANAGER->value]);

        $member = User::create([
            'name' => 'Member User',
            'email' => 'member@giggity.test',
            'password' => Hash::make('password'),
            'current_organization_id' => $org->id,
        ]);
        $org->users()->attach($member, ['role' => OrganizationRole::MEMBER->value]);

        // 3. Create events — mix of past/upcoming over 3 months
        $events = [
            // Bookings
            [
                'type' => 'booking',
                'name' => 'Tomorrowland 2026',
                'date' => now()->addDays(30)->toDateString(),
                'venue_name' => 'Tomorrowland',
                'venue_location' => 'Boom, Belgium',
                'hotel_name' => 'Hotel Boom',
                'hotel_location' => 'Boom, Belgium',
                'set_time_from' => '22:00',
                'set_time_to' => '23:30',
                'set_info' => 'Main stage closing set',
                'status' => BookingStatus::CONFIRMED->value,
                'created_by_user_id' => $admin->id,
            ],
            [
                'type' => 'booking',
                'name' => 'Awakenings Festival',
                'date' => now()->addDays(45)->toDateString(),
                'venue_name' => 'Gashouder',
                'venue_location' => 'Amsterdam, Netherlands',
                'hotel_name' => 'NH Amsterdam',
                'hotel_location' => 'Amsterdam, Netherlands',
                'set_time_from' => '02:00',
                'set_time_to' => '04:00',
                'set_info' => 'Area V headliner',
                'status' => BookingStatus::OPTION->value,
                'created_by_user_id' => $manager->id,
            ],
            [
                'type' => 'booking',
                'name' => 'Fabric London',
                'date' => now()->addDays(14)->toDateString(),
                'venue_name' => 'Fabric',
                'venue_location' => 'London, United Kingdom',
                'hotel_name' => 'Premier Inn Farringdon',
                'hotel_location' => 'London, United Kingdom',
                'set_time_from' => '03:00',
                'set_time_to' => '05:00',
                'set_info' => 'Room One',
                'status' => BookingStatus::CONFIRMED->value,
                'created_by_user_id' => $admin->id,
            ],
            [
                'type' => 'booking',
                'name' => 'Berghain',
                'date' => now()->subDays(15)->toDateString(),
                'venue_name' => 'Berghain',
                'venue_location' => 'Berlin, Germany',
                'set_time_from' => '04:00',
                'set_time_to' => '08:00',
                'set_info' => 'Panorama Bar',
                'status' => BookingStatus::CONFIRMED->value,
                'created_by_user_id' => $admin->id,
            ],
            [
                'type' => 'booking',
                'name' => 'Cancelled Club Gig',
                'date' => now()->subDays(30)->toDateString(),
                'venue_name' => 'Rex Club',
                'venue_location' => 'Paris, France',
                'set_time_from' => '01:00',
                'set_time_to' => '03:00',
                'status' => BookingStatus::CANCELLED->value,
                'created_by_user_id' => $manager->id,
            ],

            // Travels
            [
                'type' => 'travel',
                'name' => 'Flight to Boom',
                'date' => now()->addDays(29)->toDateString(),
                'flight_number' => 'KL1234',
                'leave_from_name' => 'Amsterdam Schiphol',
                'leave_from_location' => 'Schiphol, Netherlands',
                'arrival_at_name' => 'Brussels Airport',
                'arrival_at_location' => 'Zaventem, Belgium',
                'time_from' => '14:00',
                'time_to' => '15:00',
                'created_by_user_id' => $admin->id,
            ],
            [
                'type' => 'travel',
                'name' => 'Flight to London',
                'date' => now()->addDays(13)->toDateString(),
                'flight_number' => 'BA456',
                'leave_from_name' => 'Amsterdam Schiphol',
                'leave_from_location' => 'Schiphol, Netherlands',
                'arrival_at_name' => 'London Heathrow',
                'arrival_at_location' => 'London, United Kingdom',
                'time_from' => '10:00',
                'time_to' => '11:00',
                'created_by_user_id' => $admin->id,
            ],
            [
                'type' => 'travel',
                'name' => 'Flight Home from Berlin',
                'date' => now()->subDays(14)->toDateString(),
                'flight_number' => 'LH789',
                'leave_from_name' => 'Berlin Brandenburg',
                'leave_from_location' => 'Berlin, Germany',
                'arrival_at_name' => 'Amsterdam Schiphol',
                'arrival_at_location' => 'Schiphol, Netherlands',
                'time_from' => '16:00',
                'time_to' => '17:30',
                'created_by_user_id' => $admin->id,
            ],
        ];

        foreach ($events as $eventData) {
            Event::create(array_merge($eventData, [
                'organization_id' => $org->id,
            ]));
        }
    }
}
