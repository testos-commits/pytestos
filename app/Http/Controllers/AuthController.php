<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use YieldStudio\LaravelExpoNotifier\Storage\ExpoTokenStorageMysql;
use App\Services\ProfileService;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    private $profileService;

    public function __construct()
    {
        $this->profileService = new ProfileService();
    }
    // @TODO: add email verification, forget password, and reset password
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 'description' => 'nullable',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'required|exists:countries,id',
            'nationality_ids' => 'required|array',
            'passport_ids' => 'required|array',
            'app_language' => 'string',
            'phone' => 'nullable|string',
            "country_code_country_id" => 'nullable|exists:countries,id',
            'country_code' => 'nullable|string',
            'gender' => 'required|string',
            'referral_code' => 'nullable|string',
        ]);

        if ($request->referral_code) {
            $referred_by = User::where('referral_code', $request->referral_code)->first();

            if ($referred_by) {
                $request->merge(['referred_by' => $referred_by->id]);
            }
        }


        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'referred_by' => $request->referred_by ? $request->referred_by : null,
            'app_language' => $request->app_language ? $request->app_language : 'en'
        ]);

        $referral_code = 'PY-' . (string)$user->id . (string)rand(100, 999);
        $user->referral_code = $referral_code;
        $user->save();


        event(new Registered($user));

        if (request()->has('phone') && request()->has('country_code')) {
            request()->set('phone', request()->country_code . request()->phone);
        }

        $user->profile()->save(
            Profile::make(
                $request->merge([
                    'is_default' => true,
                    'is_first_login' => 1
                ])
                    ->except(['email', 'password', 'password_confirmation', 'expo_token', 'nationality_ids', 'passport_ids'])
            )
        );

        $user->profile->nationalities()->attach($request->nationality_ids);
        $user->profile->passports()->attach($request->passport_ids);



        return response()->json(['message' => 'Account created successfully'], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'push_token' => 'nullable|string'
        ]);

        $user = User::where('email', $request->email)
        ->whereNotNull('email_verified_at')
        ->firstOrFail();

        if (is_null($user->referral_code)) {
            $referral_code = 'PY-' . (string)$user->id . (string)rand(100, 999);
            $user->referral_code = $referral_code;
            $user->save();
        }

        if ($request->get('push_token')) {
            $expoService = new ExpoTokenStorageMysql();
            $expoService->store($request->get('push_token'), $user);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        auth()->login($user);

        $userWithDetails = $this->profileService->getUser();

        if ($userWithDetails->profile->is_first_login) {
            $user->profile->is_first_login = 0;
            $user->profile->save();
        }

        return response()->json([
            'token' => $user->createToken('TalentProfile')->plainTextToken,
            'user' => $userWithDetails
        ]);
    }


    public function sendResetPasswordLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 500);
    }

    public function resetPassword(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );


        return $status == Password::PASSWORD_RESET
            ? redirect()->back()->with('status', __($status))
            : back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        $user = User::find(auth('sanctum')->id());

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->expoToken()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

    public function verify($user_id, $hash, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
        }
    
        $user = User::findOrFail($user_id);
    
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    
        return view('post-verification');
    }
}
