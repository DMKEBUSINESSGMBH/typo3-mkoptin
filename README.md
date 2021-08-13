# MK Optin

Stellt einen Opt-In Prozess bereit.

* Versendet eine Opt-In-E-Mail.
* Verarbeitet die verifizierung per Aktivierungslink in E-Mail.
* Triggert ein Verifizierungs-Event.
* Stellt Opt-In-Informationen in TCA bereit.

## Starten eines Opt in prozesses:

```php
class MyAwesomeManager
{
    private OptinManager $optinManager;

    public function __construct(
        OptinManager $optinManager
    ) {
        $this->optinManager = $optinManager;
    }
    
    protected function handleOptIn(string $email): void
    {
        $optin = $this->optinManager->createOptinForEmail($email);

        // opt in already done :)
        if ($optin->isValidated()) {
            // opt-in already performed, do your finalize stuff here

            return;
        }

        // opt-in outstanding, send opt-in mail
        // finalize stuff has to be performed by event listener after opt-in validation

        $this->optinManager->sendValidationMailForOptin($optin);
    }
}
```

## Registrieren eines Event-Listeners

Warum? Um dinge zu tun, nachdem die E-Mail verifiziert wurde, beispielsweise umd
en Datensatz zu aktivieren, oder Bestätigungs-E-Mails zu versenden.

```yaml
services:
    DMK\MyAwesomeExtension\Event\EventListener\OptinValidationSuccessEventListener:
        tags:
            -
                name: 'event.listener'
                identifier: 'MyAwesomeOptinValidationSuccessEventListener'
                event: DMK\Optin\Event\OptinValidationSuccessEvent
```

```php
class OptinValidationSuccessEventListener
{
    private MyAwesomeManager $manager;

    public function __construct(
        MyAwesomeManager $manager
    ) {
        $this->manager = $manager;
    }

    public function __invoke(OptinValidationSuccessEvent $event): void
    {
        $this->manager->handleOptinValidation($event->getOptin());
    }
}

class MyAwesomeManager
{
    public function handleOptinValidation(Optin $optin): void
    {
        // opt-in performed, do your finalize stuff here
    }
}
```

## Opt-In Informationen in TCA

Um für einen Datensatz die Opt-In-Informationen mit auszugeben, ist folgendee
TCA-Spalte zu ergänzen:

```php
return [
    'columns' => [
        'optin' => [
            'label' => 'LLL:EXT:mkoptin/Resources/Private/Language/locallang_db.xlf:tx_mkoptin_domain_model_optin',
            'config' => [
                'type' => 'user',
                'renderType' => 'optInStateElement',
            ],
        ],
    ]
];
```

Aktuell muss das Feld im Datensatz immer `email` lauten. @TODO:
Tabellen-Email-Feld-Konfiguration implementieren.
