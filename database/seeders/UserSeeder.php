<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // List of districts
        $districts = [
            'Бектемир тумани',
            'Мирзо Улуғбек тумани',
            'Миробод тумани',
            'Олмазор тумани',
            'Сирғали тумани',
            'Учтепа тумани',
            'Чилонзор тумани',
            'Шайхонтоҳур тумани',
            'Юнусобод тумани',
            'Яккасарой тумани',
            'Янги ҳаёт тумани',
            'Яшнобод тумани',
        ];

        // Create Super Admin
        User::create([
            'name' => 'Тошкент ш',
            'email' => 'admin@toshkentinvest.uz',
            'password' => Hash::make('Admin@2025'),
            'role' => 'super_admin',
            'tuman' => null,
            'can_edit' => true,
            'is_active' => true,
        ]);

        $this->command->info('✓ Super Admin created: admin@toshkentinvest.uz / Admin@2025');

        // Create District Users
        foreach ($districts as $index => $district) {
            $email = $this->generateEmail($district);
            $password = 'District@2025';

            User::create([
                'name' => $district,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'district',
                'tuman' => $district,
                'can_edit' => false,
                'is_active' => true,
            ]);

            $this->command->info("✓ District user created: {$district} / {$email} / {$password}");
        }

        $this->command->info("\n=== User Creation Complete ===");
        $this->command->info("Total users created: " . (count($districts) + 1));
        $this->command->info("\nSuper Admin Login:");
        $this->command->info("  Email: admin@toshkentinvest.uz");
        $this->command->info("  Password: Admin@2025");
        $this->command->info("\nDistrict Users Login:");
        $this->command->info("  Password for all: District@2025");
        $this->command->info("\nIMPORTANT: Change these passwords in production!");
    }

    /**
     * Generate email from district name
     */
    private function generateEmail($district): string
    {
        // Simple mapping for each district
        $emailMap = [
            'Бектемир тумани' => 'bektemir',
            'Мирзо Улуғбек тумани' => 'mirzo_ulugbek',
            'Миробод тумани' => 'mirobod',
            'Олмазор тумани' => 'olmazor',
            'Сирғали тумани' => 'sirgali',
            'Учтепа тумани' => 'uchtepa',
            'Чилонзор тумани' => 'chilonzor',
            'Шайхонтоҳур тумани' => 'shayxontohur',
            'Юнусобод тумани' => 'yunusobod',
            'Яккасарой тумани' => 'yakkasaroy',
            'Янги ҳаёт тумани' => 'yangi_hayot',
            'Яшнобод тумани' => 'yashnobod',
        ];

        $slug = $emailMap[$district] ?? 'district';
        return $slug . '@toshkentinvest.uz';
    }
}
