<?php

namespace MailRedirector;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MailRedirectorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('mail-redirector')
            ->hasConfigFile();
    }

    public function bootingPackage()
    {
        Event::listen(MessageSending::class, function (MessageSending $event) {
            $redirectTo = config('mail-redirector.redirect_to');

            if ($redirectTo) {
                $event->message->to($redirectTo);
                $event->message->Cc(...[]);
                $event->message->Bcc(...[]);
            }
        });
        
    }

    public function boot()
    {
        parent::boot();

        // Veröffentlichen der Konfiguration
        $this->publishes([
            __DIR__.'/config/mail-redirector.php' => config_path('mail-redirector.php'),
        ], 'config');
    }
}