<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Foydalanuvchi tizimga kirganligini tekshiramiz
        if (Auth::check()) {
            $user = Auth::user();

            // User modelidagi ma'lumotlarni olamiz
            $currentRole = $user->current_role;
            $hemisRoles = $user->hemis_roles; // Casts bo'lgani uchun bu array bo'lib qaytadi

            // 2. Agar hemis_roles mavjud bo'lsa va massiv bo'lsa
            if (!empty($hemisRoles) && is_array($hemisRoles)) {

                // 3. Agar current_role ro'yxat ichida bo'lmasa (YOKI null bo'lsa)
                if (!in_array($currentRole, $hemisRoles)) {

                    // 4. Ro'yxatdagi ENG OXIRGI rolni olamiz
                    $lastRole = end($hemisRoles); // end() funksiyasi massiv oxirgi elementini beradi

                    // 5. Bazaga yozamiz
                    $user->current_role = $lastRole;
                    $user->save();

                    // Agar sessiyada rol saqlanayotgan bo'lsa, uni ham yangilash kerak bo'lishi mumkin (Projectga qarab)
                }
            }
        }

        return $next($request);
    }
}
