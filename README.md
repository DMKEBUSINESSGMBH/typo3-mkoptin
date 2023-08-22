# MK Optin

![TYPO3 compatibility](https://img.shields.io/badge/TYPO3-10.4-orange?maxAge=3600&style=flat-square&logo=typo3)
[![Latest Stable Version](https://img.shields.io/packagist/v/dmk/mkoptin.svg?maxAge=3600&style=flat-square&logo=composer)](https://packagist.org/packages/dmk/mkoptin)
[![Total Downloads](https://img.shields.io/packagist/dt/dmk/mkoptin.svg?maxAge=3600&style=flat-square)](https://packagist.org/packages/dmk/mkoptin)
[![Build Status](https://img.shields.io/github/actions/workflow/status/DMKEBUSINESSGMBH/typo3-mkoptin/phpci.yml?branch=10.4&maxAge=3600&style=flat-square&logo=github-actions)](https://github.com/DMKEBUSINESSGMBH/typo3-mkoptin/actions/workflows/phpci.yml)
[![Code Coverage](https://img.shields.io/badge/coverage-%3E%3D%2090%25-green?maxAge=3600&style=flat-square&logo=codecov)](https://github.com/DMKEBUSINESSGMBH/typo3-mkoptin/actions?query=workflow%3A%22PHP+Checks%22)
[![License](https://img.shields.io/packagist/l/dmk/mkoptin.svg?maxAge=3600&style=flat-square&logo=gnu)](https://packagist.org/packages/dmk/mkoptin)

This TYPO3 extension provides an opt-in process via fluid mails.

What it does in short:

* Can be triggered to send an Opt-In-E-Mail
* Processes the verification via activation link in email.
* triggers a opt-in validation success event.
* Adds opt-in information to tca.

## Installation

Install TYPO3 via composer.  
From project root you need to run

```
composer require dmk/mkoptin
```

## Start a new opt-in process

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

## register opt-in validation success event listeners

Why we need this?  
To do things after the email has been verified, such as activate the record or
send confirmation emails.

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

## Add opt-in information to TCA

In order to output the opt-in information for a data record, the following TCA
column must be added:

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

![OptIn State Element][OptInStateElement]

WARNING: Currently the field in the data record must always be `email`!

## Templates

```typo3_typoscript
plugin {
    tx_mkoptin {
        view {
            templateRootPath = EXT:myawesomeextension/Resources/Private/Templates/Optin
            partialRootPath = EXT:myawesomeextension/Resources/Private/Partials/Optin
            layoutRootPath = EXT:myawesomeextension/Resources/Private/Layouts/Optin
        }
    }
}
```

## @TODOs

* Implement table email field configuration

[OptInStateElement]: Documentation/Images/OptInStateElement.png "OptIn State Element"
