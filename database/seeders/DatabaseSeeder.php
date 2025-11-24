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
            'name' => 'Admin User',
            'email' => 'admin@rento.com',
        ]);

        // 2. إنشاء العلامات التجارية (براندات)
        Brands::factory()->count(5)->create();

        // 3. إنشاء الموديلات (ترتبط بالبراندات العشوائية)
        Models::factory()->count(10)->create();

        // 4. إنشاء المستخدمين (14 مستخدم)
        $users = User::factory()->count(14)->create();

        // 5. تقسيم المستخدمين إلى عملاء ووكالات
        $customers = $users->take(10);
        $agencies = $users->skip(10)->take(4);

        // 6. إنشاء سجلات العملاء
        foreach ($customers as $user) {
            Customer::create([
                'user_id' => $user->id,
                'driving_license' => 'license_' . $user->id . '.jpg',
            ]);
        }

        // 7. إنشاء سجلات الوكالات
        foreach ($agencies as $user) {
            Agency::create([
                'user_id' => $user->id,
                'commercial_register' => 'CR-' . $user->id,
                'contact_email' => $user->email,
            ]);
        }

        // 8. إنشاء السيارات (ترتبط بالوكالات العشوائية)
        // أولاً نحتاج لإضافة agency_id لجدول car
        $agencies = Agency::all();
        Car::factory()->count(15)->create()->each(function ($car) use ($agencies) {
            // إضافة agency_id للسيارة
            $car->update([
                'agency_id' => $agencies->random()->id,
            ]);
        });

        // 9. إنشاء الحجوزات (ترتبط بالعملاء والسيارات العشوائية)
        Booking::factory()->count(20)->create();

        // 10. إنشاء المدفوعات (ترتبط بالحجوزات العشوائية)
        Payment::factory()->count(15)->create();

        // 11. إنشاء التقييمات (ترتبط بالحجوزات العشوائية)
        Review::factory()->count(10)->create();
    }
}
