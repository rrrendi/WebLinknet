<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Database Seeding...');
        
        // Seed Users (Admin & User)
        $this->call(UserSeeder::class);
        
        // Seed Master Data (Merk & Type)
        $this->call(MasterDataSeeder::class);
        
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('📋 Login Credentials:');
        $this->command->info('   👤 Admin: admin@linknet.com / admin123');
        $this->command->info('   👤 User: user@linknet.com / user123');
        $this->command->info('   👤 Tamu: tamu@linknet.com / tamu123');
        $this->command->newLine();
    }
}