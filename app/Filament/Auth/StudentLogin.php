<?php

//kena custom namespace ni supaya boleh access directory dia masa kita nak import class
namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\SimplePage;
use Filament\Actions\ActionGroup;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

/**
 * @property Form $form
 */

//untuk buat cutom login aku declare dari sini -> kena pergi vendor yg Login punya by default extend SimplePage dari situ
class StudentLogin extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::pages.auth.login';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        if(app()->environment('local')){
            $this->form->fill([
                'name' => 'Nizar',
                'password' => 'password',
            ]);
        }
    }

    // public function authenticate(): ?LoginResponse
    // {
    //     try {
    //         $this->rateLimit(5);
    //     } catch (TooManyRequestsException $exception) {
    //         $this->getRateLimitedNotification($exception)?->send();

    //         return null;
    //     }

    //     $data = $this->form->getState();

    //     if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
    //         $this->throwFailureValidationException();
    //     }

    //     $user = Filament::auth()->user();

    //     if (
    //         ($user instanceof FilamentUser) &&
    //         (! $user->canAccessPanel(Filament::getCurrentPanel()))
    //     ) {
    //         Filament::auth()->logout();

    //         $this->throwFailureValidationException();
    //     }

    //     session()->regenerate();

    //     return app(LoginResponse::class);
    // }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $data = $this->form->getState();

        // Use name and password for authentication
        if (! Filament::auth()->attempt(['name' => $data['name'], 'password' => $data['password']], $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }


    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]))
            ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.name' => __('filament-panels::pages/auth/login.messages.failed'),
            // 'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        // $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    //Ini baru tambah nak login by Name
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('Student Name'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    // protected function getEmailFormComponent(): Component
    // {
    //     return TextInput::make('email')
    //         ->label(__('filament-panels::pages/auth/login.form.email.label'))
    //         ->email()
    //         ->required()
    //         ->autocomplete()
    //         ->autofocus()
    //         ->extraInputAttributes(['tabindex' => 1]);
    // }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('filament-panels::pages/auth/login.form.remember.label'));
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label(__('filament-panels::pages/auth/login.actions.register.label'))
            ->url(filament()->getRegistrationUrl());
    }

    public function getTitle(): string | Htmlable
    {
        return __('Student Login');
        // return __('filament-panels::pages/auth/login.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('Student Login');
        // return __('filament-panels::pages/auth/login.heading');
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('filament-panels::pages/auth/login.form.actions.authenticate.label'))
            ->submit('authenticate');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'name' => $data['name'],
            'password' => $data['password'],
        ];
    }
}