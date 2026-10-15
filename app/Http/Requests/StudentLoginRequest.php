<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Student;
use App\Traits\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class StudentLoginRequest extends FormRequest
{
   use ApiResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'          => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Generate a unique throttle key based on email and IP address.
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('email')) . '|' . $this->ip();
    }

    /**
     * Check if the request is rate limited.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            $seconds = RateLimiter::availableIn($this->throttleKey());

            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
                'status'  => Response::HTTP_TOO_MANY_REQUESTS
            ], Response::HTTP_TOO_MANY_REQUESTS));
        }
    }

    /**
     * Authenticate the student and manage rate limiter hits.
     */
    public function authenticate(): Student
    {
        // 1. Check rate limit
        $this->ensureIsNotRateLimited();

        // 2. Find student credentials
        $student = Student::where('email', $this->input('email'))
            ->where('id_card_number', $this->input('password'))
            ->first();

        // 3. Handle invalid credentials
        if (!$student) {
            RateLimiter::hit($this->throttleKey(), 60); // Lock for 60 seconds

            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Invalid email or ID card number.',
                'status'  => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED));
        }

        // 4. Successful login: clear throttle counter
        RateLimiter::clear($this->throttleKey());

        return $student;
    }
}
