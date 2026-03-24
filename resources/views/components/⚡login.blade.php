<?php

use Livewire\Component;

new class extends Component
{
    public string $username = '';
    public string $password = '';
    public string $errorMessage = '';

    public function authenticate(): void
    {
        // Fetch credentials directly from .env (fallback to 'admin'/'password' if not set)
        $validUsername = env('ADMIN_USERNAME', 'admin');
        $validPassword = env('ADMIN_PASSWORD', 'password');

        if ($this->username === $validUsername && $this->password === $validPassword) {
            // Set the session gimmick
            session()->put('is_admin_logged_in', true);

            // Redirect to the main raffle page (defaulting to trip)
            $this->redirectRoute('raffle.show', ['prize' => 'trip'], navigate: true);
            return;
        }

        // Handle failure
        $this->errorMessage = 'Invalid username or password.';
        $this->password = ''; // Clear the password field
    }
};
?>

<div class="flex min-h-screen items-center justify-center bg-gray-50 font-sans">
    <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-xl">

        <div class="mb-8 text-center">
            <h1 class="text-3xl font-black uppercase tracking-tight text-gray-900">
                Access
            </h1>
            <p class="mt-2 text-sm font-medium text-gray-500">
                Please enter username and password to enter
            </p>
        </div>

        <form wire:submit="authenticate" class="space-y-6 text-black">

            @if($errorMessage)
                <div class="rounded-lg bg-red-50 p-3 text-center text-sm font-bold text-red-600">
                    {{ $errorMessage }}
                </div>
            @endif

            <div>
                <label class="mb-2 block text-sm font-bold text-gray-700">Username</label>
                <input
                    type="text"
                    wire:model="username"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all"
                    required
                    autofocus
                >
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-gray-700">Password</label>
                <input
                    type="password"
                    wire:model="password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all"
                    required
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-blue-600 px-4 py-3 text-lg font-bold text-white shadow-md transition-colors hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 active:scale-[0.98]"
            >
                <span wire:loading.remove>Secure Login</span>
                <span wire:loading>Verifying...</span>
            </button>
        </form>

    </div>
</div>
