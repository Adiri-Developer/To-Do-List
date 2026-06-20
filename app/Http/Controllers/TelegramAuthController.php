<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramAuthController extends Controller
{
    /**
     * Handle the Telegram authentication callback.
     */
    public function handleCallback(Request $request)
    {
        $botToken = config('services.telegram.bot_token');

        if (!$botToken) {
            return redirect()->route('login')->withErrors([
                'telegram' => 'Telegram Bot Token is not configured.'
            ]);
        }

        // Verify Telegram signature
        if (!$this->verifyTelegramHash($request->all(), $botToken)) {
            return redirect()->route('login')->withErrors([
                'telegram' => 'Telegram login verification failed. Invalid hash.'
            ]);
        }

        $telegramId = $request->input('id');
        $telegramUsername = $request->input('username');

        // Case 1: User is already logged in (Linking Telegram to existing account)
        if (Auth::check()) {
            $user = Auth::user();

            // Check if this Telegram ID is already linked to another user
            $existing = User::where('telegram_id', $telegramId)
                            ->where('id', '!=', $user->id)
                            ->first();

            if ($existing) {
                return redirect()->route('tasks.index')->withErrors([
                    'telegram' => 'This Telegram account is already linked to another user.'
                ]);
            }

            // Link the Telegram ID and Username
            $user->telegram_id = $telegramId;
            $user->telegram_username = $telegramUsername;
            $user->save();

            return redirect()->route('tasks.index')->with('success', 'Telegram account successfully linked!');
        }

        // Case 2: User is not logged in (Logging in via Telegram)
        $user = User::where('telegram_id', $telegramId)->first();

        if ($user) {
            Auth::login($user, true); // Log in and remember the user
            return redirect()->intended('/tasks')->with('success', 'Successfully logged in via Telegram.');
        }

        // If no user matches the Telegram ID, redirect back to login page
        return redirect()->route('login')->withErrors([
            'telegram' => 'This Telegram account is not linked to any user. Please log in with your email first, then link your Telegram account in the settings.'
        ]);
    }

    /**
     * Unlink the Telegram account from the currently logged in user.
     */
    public function unlink(Request $request)
    {
        $user = Auth::user();
        
        $user->telegram_id = null;
        $user->telegram_username = null;
        $user->save();

        return redirect()->route('tasks.index')->with('success', 'Telegram account successfully unlinked.');
    }

    /**
     * Verify the hash received from Telegram login callback.
     */
    private function verifyTelegramHash(array $data, string $botToken): bool
    {
        if (!isset($data['hash'])) {
            return false;
        }

        $checkHash = $data['hash'];
        unset($data['hash']);

        // Only keep valid Telegram auth data attributes to prevent issues with other query params
        $validKeys = ['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date'];
        $filteredData = array_intersect_key($data, array_flip($validKeys));

        $dataCheckArr = [];
        foreach ($filteredData as $key => $value) {
            $dataCheckArr[] = $key . '=' . $value;
        }
        sort($dataCheckArr);
        $dataCheckString = implode("\n", $dataCheckArr);

        // Secret key is the SHA256 of the Bot Token
        $secretKey = hash('sha256', $botToken, true);
        
        // HMAC-SHA256 signature
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (!hash_equals($hash, $checkHash)) {
            return false;
        }

        // Check if auth data is older than 24 hours to prevent replay attacks
        if (isset($filteredData['auth_date']) && (time() - $filteredData['auth_date']) > 86400) {
            return false;
        }

        return true;
    }
}
