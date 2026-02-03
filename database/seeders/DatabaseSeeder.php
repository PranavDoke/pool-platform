<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@poll.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create Regular User
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'user@poll.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Create Sample Poll 1
        $poll1 = Poll::create([
            'user_id' => $admin->id,
            'title' => 'What is your favorite programming language?',
            'description' => 'Vote for the programming language you enjoy working with the most.',
            'is_active' => true,
        ]);

        PollOption::create([
            'poll_id' => $poll1->id,
            'option_text' => 'JavaScript',
            'display_order' => 1,
        ]);

        PollOption::create([
            'poll_id' => $poll1->id,
            'option_text' => 'Python',
            'display_order' => 2,
        ]);

        PollOption::create([
            'poll_id' => $poll1->id,
            'option_text' => 'PHP',
            'display_order' => 3,
        ]);

        PollOption::create([
            'poll_id' => $poll1->id,
            'option_text' => 'Java',
            'display_order' => 4,
        ]);

        // Create Sample Poll 2
        $poll2 = Poll::create([
            'user_id' => $user->id,
            'title' => 'Which framework do you prefer for web development?',
            'description' => 'Choose your preferred web development framework.',
            'is_active' => true,
        ]);

        PollOption::create([
            'poll_id' => $poll2->id,
            'option_text' => 'Laravel',
            'display_order' => 1,
        ]);

        PollOption::create([
            'poll_id' => $poll2->id,
            'option_text' => 'React',
            'display_order' => 2,
        ]);

        PollOption::create([
            'poll_id' => $poll2->id,
            'option_text' => 'Vue.js',
            'display_order' => 3,
        ]);

        PollOption::create([
            'poll_id' => $poll2->id,
            'option_text' => 'Angular',
            'display_order' => 4,
        ]);

        // Create Sample Poll 3
        $poll3 = Poll::create([
            'user_id' => $admin->id,
            'title' => 'Best database for modern applications?',
            'description' => 'Vote for the database system you find most suitable for modern web applications.',
            'is_active' => true,
        ]);

        PollOption::create([
            'poll_id' => $poll3->id,
            'option_text' => 'MySQL',
            'display_order' => 1,
        ]);

        PollOption::create([
            'poll_id' => $poll3->id,
            'option_text' => 'PostgreSQL',
            'display_order' => 2,
        ]);

        PollOption::create([
            'poll_id' => $poll3->id,
            'option_text' => 'MongoDB',
            'display_order' => 3,
        ]);

        PollOption::create([
            'poll_id' => $poll3->id,
            'option_text' => 'Redis',
            'display_order' => 4,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@poll.com / password');
        $this->command->info('User: user@poll.com / password');
    }
}
