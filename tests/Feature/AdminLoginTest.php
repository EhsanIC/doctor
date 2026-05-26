<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an admin can login successfully', function () {
    // ۱. ساخت یک کاربر ادمین
    // $adminUser = User::factory()->create([
    //     'email' => 'admin@example.com',
    //     'password' => bcrypt('password123'),
    //     'role' => 'admin', // فرض می‌کنیم ستون role داریم
    // ]);

    // ۲. ارسال درخواست ورود
    $data = [
        'email' => 'admin@test.com',
        'password' => 'password',
    ];
    // dd($this->postJson('/api/v1/auth/login', $data));
    $response = $this->postJson('/api/v1/auth/login', $data);

    $response->assertStatus(200);

    // ۳. بررسی نتیجه
    $response->assertStatus(200); // موفقیت‌آمیز
    $response->assertJsonStructure(['token']); // وجود توکن در پاسخ

    // استخراج توکن برای استفاده‌های بعدی
    $token = $response->json('token');
    expect($token)->not()->toBeEmpty();
});

// test('a normal user cannot login with admin credentials', function () {
//     // ساخت کاربر عادی
//     $normalUser = User::factory()->create([
//         'email' => 'user@example.com',
//         'password' => bcrypt('password123'),
//         'role' => 'user',
//     ]);

//     // تلاش برای ورود با اطلاعات کاربر عادی (که نقش ادمین نیست)
//     $response = $this->postJson('/api/auth/login', [
//         'email' => 'user@example.com',
//         'password' => 'password123',
//     ]);

//     // انتظار داریم که ورود موفق نباشه (مثلاً 401 Unauthorized یا 422)
//     $response->assertStatus(401); // یا 422 بسته به پیاده‌سازی login شما
// });
