<?php

namespace App\Http\Controllers\Auth\Doctors;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorRegsiterRequest;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class DoctorRegisterContoller extends Controller
{
    #[OA\Post(
        path: '/api/v1/doctor/register',
        operationId: 'registerDoctor',
        summary: 'Register a new doctor',
        description: "Register a doctor account. The account is created with a 'pending' status and cannot access the panel until approved by an admin.",
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/DoctorRegisterRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Doctor registered (pending approval)', content: new OA\JsonContent(ref: '#/components/schemas/RegisterResponse')),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    public function registerDoctor(DoctorRegsiterRequest $request)
    {
        // dd('test');
        // 2. ایجاد کاربر جدید
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('doctor');

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
            'user' => $user->load('roles'), // می توانید اطلاعات کاربر را برگردانید
            'doctor_profile' => $doctorProfile, // و اطلاعات پروفایل پزشک را
        ], 201);
    }
}
