<?php
// database/seeders/MasterDataSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterMerk;
use App\Models\MasterType;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Data Master Merk & Type
        $masterData = [
            'STB' => [
                'Fiberhome' => ['HG6145D2', 'HG680-FJ', 'HG8145V5'],
                'Huawei' => ['DN8145V', 'DN8245V', 'HG8245H'],
                'ZTE' => ['ZX279128', 'B860H V5', 'B760H'],
            ],
            'ONT' => [
                'Fiberhome' => ['HG6243C', 'AN5506-04-F', 'HG6145F'],
                'Huawei' => ['HG8245H5', 'EG8145V5', 'HG8346M'],
                'ZTE' => ['F670L', 'F609', 'F660'],
            ],
            'ROUTER' => [
                'TP-Link' => ['Archer C6', 'Archer AX23', 'TL-WR840N'],
                'Tenda' => ['AC10', 'AC6', 'F3'],
                'Xiaomi' => ['4A Gigabit', 'AX1800', 'AX3000'],
            ]
        ];

        foreach ($masterData as $jenis => $merkList) {
            foreach ($merkList as $merkName => $typeList) {
                // Create Merk
                $merk = MasterMerk::create([
                    'jenis' => $jenis,
                    'merk' => $merkName,
                    'is_active' => true
                ]);

                // Create Types for this Merk
                foreach ($typeList as $typeName) {
                    MasterType::create([
                        'merk_id' => $merk->id,
                        'type' => $typeName,
                        'is_active' => true
                    ]);
                }
            }
        }

        $this->command->info('✓ Master Merk & Type seeded successfully!');
    }
}