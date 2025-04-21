<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use MailRedirector\MailRedirectorServiceProvider;
use Symfony\Component\Mime\Email;
use Illuminate\Mail\Events\MessageSending;

class MailTestCommand extends Command
{
    protected $signature = 'mail-redirector:test';

    protected $description = 'Testet das Mail-Redirector Event';

    public function handle()
    {
        $this->info('Der Mail Redirector wird jetzt getestet.');

        $this->info('MAIL_REDIRECT_TO aus der .env: ' . env('MAIL_REDIRECT_TO'));

        $this->info('Mail Redirector Redirect To: ' . json_encode(Config::get('mail-redirector.redirect_to')));

        $provider = new MailRedirectorServiceProvider(app());
        $provider->bootingPackage();

        $this->info('package booted');

        $email = (new Email())
            ->to('original@example.com')
            ->subject('Test-Mail');

        $event = new MessageSending($email);
        Event::dispatch($event);

        $redirectedTo = $email->getTo()[0]->getAddress();

        if ($redirectedTo === 'test@test.local') {
            $this->info('Die Mail wurde korrekt umgeleitet an: ' . $redirectedTo);
        } else {
            $this->error('Die Mail wurde NICHT korrekt umgeleitet. Aktuell: ' . $redirectedTo);
        }

        $this->info('Test abgeschlossen!');
    }
}
