<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Brands;
use App\Models\Models;
use App\Models\Customer;
use App\Models\Agency;
use App\Models\Car;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. إنشاء الأدمن
        User::factory()->admin()->create([
            'name'  => 'Admin User',
            'email' => 'admin@rento.com',
        ]);

        // 2. إنشاء البراندات
        Brands::factory()->count(5)->create();

        // 3. إنشاء الموديلات
        Models::factory()->count(15)->create();

        // 4. إنشاء المستخدمين
        $users = User::factory()->count(14)->create();

        // 5. تقسيم المستخدمين
        $customers   = $users->take(10);
        $agencyUsers = $users->skip(10)->take(4);

        // ✅ 6. إنشاء العملاء (بدون فاكتوري)
        foreach ($customers as $user) {
            Customer::create([
                'user_id' => $user->id,
                'driving_license' => 'license_' . $user->id . '.jpg',
            ]);
        }

        // ✅ 7. إنشاء الوكالات (بدون فاكتوري)
        foreach ($agencyUsers as $user) {
            Agency::create([
                'user_id' => $user->id,
                'commercial_register' => 'CR-' . $user->id,
                'contact_email' => $user->email,
            ]);
        }

        // 8. إنشاء السيارات
        $agencyIds = Agency::pluck('id');
        $modelIds  = Models::pluck('id');

        Car::factory()
            ->count(20)
            ->make()
            ->each(function ($car) use ($agencyIds, $modelIds) {
                $car->agency_id = $agencyIds->random();
                $car->model_id  = $modelIds->random();
                $car->save();
            });

        // 9. إنشاء الحجوزات
        Booking::factory()->count(20)->create();

        // 10. إنشاء المدفوعات
        Payment::factory()->count(15)->create();

        // 11. إنشاء التقييمات
        Review::factory()->count(10)->create();
    }
}
