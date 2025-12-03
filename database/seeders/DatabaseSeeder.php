<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Igi;
use App\Models\UjiFungsi;
use App\Models\Repair;
use App\Models\Rekondisi;
use App\Models\Packing;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@linknet.com',
            'password' => Hash::make('password'),
        ]);

        echo "✓ User created: admin@linknet.com / password\n";

        // Create sample IGI data
        $categories = ['ONT', 'STB', 'ROUTER'];
        $types = ['ZTE F609', 'Fiberhome HG6243C', 'Huawei HG8245H', 'TP-Link Archer', 'Asus RT-AC68U'];
        
        echo "Creating sample IGI data...\n";
        
        for ($i = 1; $i <= 50; $i++) {
            $category = $categories[array_rand($categories)];
            $type = $types[array_rand($types)];
            $serialNumber = strtoupper(substr($category, 0, 3)) . date('Y') . str_pad($i, 5, '0', STR_PAD_LEFT);
            
            $igi = Igi::create([
                'no_do' => 'DO-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal_datang' => Carbon::now()->subDays(rand(1, 30))->addHours(rand(0, 23)),
                'nama_barang' => $category,
                'type' => $type,
                'serial_number' => $serialNumber,
                'total_scan' => rand(1, 100),
            ]);

            // 80% chance to create Uji Fungsi
            if (rand(1, 10) <= 8) {
                $status = rand(1, 10) <= 7 ? 'OK' : 'NOK'; // 70% OK, 30% NOK
                
                UjiFungsi::create([
                    'serial_number' => $serialNumber,
                    'nama_barang' => $category,
                    'type' => $type,
                    'status' => $status,
                    'waktu_uji' => Carbon::now()->subDays(rand(1, 25))->addHours(rand(0, 23)),
                ]);

                // If NOK, might go to Repair
                if ($status === 'NOK' && rand(1, 10) <= 6) { // 60% go to repair
                    $repairStatus = rand(1, 10) <= 5 ? 'OK' : 'NOK'; // 50% fixed
                    $jenisKerusakan = ['Konektor LAN rusak', 'Konektor Optic rusak', 'Adapter rusak', 'Port mati', 'LED mati', 'Board rusak'];
                    
                    Repair::create([
                        'serial_number' => $serialNumber,
                        'nama_barang' => $category,
                        'type' => $type,
                        'status' => $repairStatus,
                        'jenis_kerusakan' => $jenisKerusakan[array_rand($jenisKerusakan)],
                        'waktu_repair' => Carbon::now()->subDays(rand(1, 20))->addHours(rand(0, 23)),
                    ]);

                    // If repair OK, go to Rekondisi
                    if ($repairStatus === 'OK' && rand(1, 10) <= 7) { // 70% go to rekondisi
                        Rekondisi::create([
                            'serial_number' => $serialNumber,
                            'nama_barang' => $category,
                            'type' => $type,
                            'waktu_rekondisi' => Carbon::now()->subDays(rand(1, 15))->addHours(rand(0, 23)),
                        ]);

                        // 80% go to packing
                        if (rand(1, 10) <= 8) {
                            Packing::create([
                                'serial_number' => $serialNumber,
                                'nama_barang' => $category,
                                'type' => $type,
                                'waktu_packing' => Carbon::now()->subDays(rand(1, 10))->addHours(rand(0, 23)),
                            ]);
                        }
                    }
                }
                // If Uji Fungsi OK, might go directly to Rekondisi
                elseif ($status === 'OK' && rand(1, 10) <= 8) { // 80% go to rekondisi
                    Rekondisi::create([
                        'serial_number' => $serialNumber,
                        'nama_barang' => $category,
                        'type' => $type,
                        'waktu_rekondisi' => Carbon::now()->subDays(rand(1, 15))->addHours(rand(0, 23)),
                    ]);

                    // 80% go to packing
                    if (rand(1, 10) <= 8) {
                        Packing::create([
                            'serial_number' => $serialNumber,
                            'nama_barang' => $category,
                            'type' => $type,
                            'waktu_packing' => Carbon::now()->subDays(rand(1, 10))->addHours(rand(0, 23)),
                        ]);
                    }
                }
            }

            if ($i % 10 == 0) {
                echo "  ✓ Created $i items...\n";
            }
        }

        echo "✓ Sample data created successfully!\n";
        echo "\n";
        echo "===========================================\n";
        echo "  Login credentials:\n";
        echo "  Email: admin@linknet.com\n";
        echo "  Password: password\n";
        echo "===========================================\n";
    }
}