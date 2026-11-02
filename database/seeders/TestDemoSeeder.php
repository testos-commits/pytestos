<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * TEST ENVIRONMENT ONLY.
 * Fills the test database with made-up countries, staff accounts, talents,
 * projects, auditions and applications. No real people or data.
 */
class TestDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $json = fn (string $en) => json_encode(['en' => $en]);

        // Countries
        $countryIds = [];
        foreach ([
            'AE' => 'United Arab Emirates', 'GE' => 'Georgia', 'CY' => 'Cyprus',
            'LB' => 'Lebanon', 'EG' => 'Egypt', 'SA' => 'Saudi Arabia',
        ] as $code => $name) {
            $countryIds[$code] = DB::table('countries')->insertGetId([
                'country_code' => $code, 'is_active' => 1, 'name' => $json($name),
                'aws_bucket' => 'default', 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Staff accounts (test passwords only)
        $admin = User::create([
            'email' => 'admin@test.local', 'password' => Hash::make('password'),
            'is_admin' => true, 'name' => 'Test Super Admin', 'email_verified_at' => $now,
        ]);
        $admin->assignRole('super admin');

        $director = User::create([
            'email' => 'director@test.local', 'password' => Hash::make('password'),
            'is_director' => true, 'name' => 'Test Super Director', 'email_verified_at' => $now,
        ]);
        $director->assignRole('super director');

        foreach ([$admin, $director] as $u) {
            foreach ($countryIds as $cid) {
                DB::table('admin_countries')->insert([
                    'user_id' => $u->id, 'country_id' => $cid, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // Talents (invented names)
        $talents = [
            ['Omar', 'Haddad', 'male', 'AE', '2001-04-12'], ['Karim', 'Nassar', 'male', 'AE', '1999-09-03'],
            ['Youssef', 'Mansour', 'male', 'LB', '2000-01-22'], ['Hamad', 'Al Suwaidi', 'male', 'AE', '2002-06-30'],
            ['Layla', 'Khoury', 'female', 'AE', '2000-11-08'], ['Malak', 'Rahman', 'female', 'AE', '2003-02-14'],
            ['Sara', 'Aziz', 'female', 'EG', '2001-07-19'], ['Nour', 'Saleh', 'female', 'LB', '1998-03-25'],
            ['Nino', 'Beridze', 'female', 'GE', '1997-05-02'], ['Elena', 'Georgiou', 'female', 'CY', '1996-12-11'],
        ];
        $profileIds = [];
        foreach ($talents as $i => [$first, $last, $gender, $cc, $dob]) {
            $u = User::create([
                'email' => strtolower($first) . ($i + 1) . '@talent.test', 'password' => Hash::make('password'),
                'name' => "$first $last", 'email_verified_at' => $now,
            ]);
            $profileIds[] = DB::table('user_profiles')->insertGetId([
                'user_id' => $u->id, 'first_name' => $first, 'last_name' => $last,
                'description' => 'Test talent profile.', 'country_id' => $countryIds[$cc],
                'date_of_birth' => $dob, 'city' => $cc === 'AE' ? 'Dubai' : 'Test City',
                'phone' => '+000000000' . $i, 'gender' => $gender,
                'height' => (string) rand(160, 190), 'weight' => (string) rand(50, 85),
                'is_default' => 1, 'is_first_login' => 0, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Projects and auditions (mirroring what we created on the live dashboard)
        $projects = [
            ['Lifestyle shoot', [
                ['Lifestyle Model', 'Casting a lifestyle model for a one-day shoot.', 'yogi.jpg', 'Cyprus', 'female', 18, 40, ['CY'], '2026-11-20', '2026-11-15'],
            ]],
            ['Yoga Shoot - Egypt', [
                ['Female Yogi', 'We are casting a female yoga practitioner for an outdoor lifestyle shoot in Egypt.', 'yogi.jpg', 'Egypt', 'female', 18, 100, ['GE'], null, null],
            ]],
            ['Pizza Hut Dip and Crunch', [
                ['Male 20-30', "We are casting stylish, trendy, camera-confident men for a Pizza Hut commercial shoot in Dubai. We are looking for 2 male talents, aged 20-30.\n\nShoot date: 9 November 2026 (1 day)\nLocation: Dubai, UAE\nUsage: 1 year, social media + TV", 'male.jpg', 'Dubai, UAE', 'male', 20, 30, ['AE'], '2026-11-09', '2026-12-16'],
                ['Female 20-30', "We are casting stylish, trendy, camera-confident women for a Pizza Hut commercial shoot in Dubai. We are looking for 2 female talents, aged 20-30.\n\nShoot date: 9 November 2026 (1 day)\nLocation: Dubai, UAE\nUsage: 1 year, social media + TV", 'female.jpg', 'Dubai, UAE', 'female', 20, 30, ['AE'], '2026-11-09', '2026-12-16'],
            ]],
        ];

        foreach ($projects as [$title, $auditions]) {
            $pid = DB::table('projects')->insertGetId([
                'user_id' => $admin->id, 'title' => $title, 'created_at' => $now, 'updated_at' => $now,
            ]);
            foreach ($auditions as [$aTitle, $desc, $img, $loc, $gender, $min, $max, $ccs, $film, $deadline]) {
                $aid = DB::table('auditions')->insertGetId([
                    'project_id' => $pid, 'title' => $json($aTitle), 'description' => $json($desc),
                    'image' => $json('audition-images/' . $img), 'location' => $json($loc),
                    'duration' => $json('1 day'), 'gender' => $gender, 'min_age' => $min, 'max_age' => $max,
                    'filming_date' => $film, 'applications_deadline' => $deadline,
                    'filming_budget' => 0, 'usage_budget' => 0, 'show_budget' => 0, 'meta' => json_encode([]),
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                foreach ($ccs as $cc) {
                    DB::table('audition_country')->insert([
                        'audition_id' => $aid, 'country_id' => $countryIds[$cc], 'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
                // Matching talents apply; a couple get shortlisted
                $n = 0;
                foreach ($talents as $i => $t) {
                    if ($t[2] !== $gender) continue;
                    DB::table('auditions_applications')->insert([
                        'audition_id' => $aid, 'user_profile_id' => $profileIds[$i],
                        'is_shortlisted' => $n < 2 ? 1 : 0, 'is_accepted' => 0, 'in_selection' => 0,
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                    $n++;
                }
            }
        }
    }
}
