<x-filament-panels::form wire:submit="updatePassword">
    {{ $this->form }}

    <div class="fi-form-actions">
        <div class="flex flex-row-reverse flex-wrap items-center gap-3 fi-ac">
            <x-filament::button type="submit">
                {{ __('filament-edit-profile::default.save') }}
            </x-filament::button>

            <div wire:loading class="ml-3">
                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zM12 24a8 8 0 01-8-8H0c0 6.627 5.373 12 12 12v-4z"></path>
                </svg>
            </div>
        </div>
    </div>
</x-filament-panels::form>
