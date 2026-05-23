<?php

namespace App\Http\Controllers\Auth\Doctors;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorRegsiterRequest;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DoctorRegisterContoller extends Controller
{
    public function registerDoctor(DoctorRegsiterRequest $request)
    {
        // dd('test');
        // 2. ایجاد کاربر جدید
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('admin');


        // // 3. اختصاص نقش 'doctor' به کاربر
        // // ابتدا مطمئن شو که نقش 'doctor' وجود دارد، اگر نه آن را ایجاد کن
        // $doctorRole = Role::findByName('doctor');
        // if (!$doctorRole) {
        //     // اگر نقش doctor وجود ندارد، آن را ایجاد کن
        //     $doctorRole = Role::create(['name' => 'doctor']);
        // }
        // $user->assignRole($doctorRole); // اختصاص نقش doctor

        // 4. ایجاد پروفایل پزشک
        $doctorProfile = DoctorProfile::create([
            'user_id' => $user->id,
            'specialty_id' => $request->specialty_id,
            'status' => 'pending', // وضعیت اولیه: در انتظار تایید
            'image' => $request->image, // اگر تصویر ارسال شده
            'bio' => $request->bio, // اگر توضیحات ارسال شده
        ]);

        // 5. بازگرداندن پاسخ موفقیت آمیز
        // در اینجا توکن API را برنمی‌گردانیم چون پزشک هنوز تایید نشده است
        return response()->json([
            'message' => 'ثبت نام پزشک با موفقیت انجام شد. حساب شما در انتظار تایید مدیر است.',
            'user' => $user, // می توانید اطلاعات کاربر را برگردانید
            'doctor_profile' => $doctorProfile // و اطلاعات پروفایل پزشک را
        ], 201);
    }
}
