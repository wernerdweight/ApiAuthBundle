ApiAuthBundle
====================================

Symfony bundle providing API authentication and authorization.

[![Build Status](https://travis-ci.org/wernerdweight/ApiAuthBundle.svg?branch=master)](https://travis-ci.org/wernerdweight/ApiAuthBundle)
[![Latest Stable Version](https://poser.pugx.org/wernerdweight/api-auth-bundle/v/stable)](https://packagist.org/packages/wernerdweight/api-auth-bundle)
[![Total Downloads](https://poser.pugx.org/wernerdweight/api-auth-bundle/downloads)](https://packagist.org/packages/wernerdweight/api-auth-bundle)
[![License](https://poser.pugx.org/wernerdweight/api-auth-bundle/license)](https://packagist.org/packages/wernerdweight/api-auth-bundle)


Installation
------------

### 1. Download using composer

```bash
composer require wernerdweight/api-auth-bundle
```

### 2. Enable the bundle

Enable the bundle in your kernel:

```php
    <?php
    // config/bundles.php
    return [
        // ...
        WernerDweight\ApiAuthBundle\ApiAuthBundle::class => ['all' => true],
    ];
```

Configuration
------------

None of the configuration keys are mandatory, default values are mentioned in comments. 

```yaml
# config/packages/api_auth.yaml
api_auth:
    # list of controllers to target
    target_controllers:         # default 'WernerDweight\ApiAuthBundle\Contrtoller\ApiAuthControllerInterface'
        - '*'                   # all controllers or list specific controllers (see next line)
        - 'My\Controller\SomeInterface'
        - 'Vendor\Bundle\Controller\SomeOtherInterface'
```

### Target controllers

All controllers that implement `WernerDweight\ApiAuthBundle\Controller\ApiAuthControllerInterface` will be targeted automatically (no configuration required).

If you can't modify the controller (e.g. it's vendor code), you can specify an interface implemented by the vendor controller (be aware that it may also be implemented by some other controllers), or specify the class of the controller itself.

If you want to target all controllers, use `*` as configuration value for `target_controllers`.

### Firewall

Configure your firewall:

```yaml
# config/packages/security.yaml
security:
    # TODO: provider/user class (api client)
    # ...
    firewalls:
        # ...
        main:
            # ...
            guard:
                authenticators:
                    - WernerDweight\ApiAuthBundle\Security\ApiClientAuthenticator

            # you should probably entirely disable anonymous access to the api
            # anonymous: false
            
            # if you want, disable storing the client in the session
            # stateless: true
```

### ApiClient

Create an entity that implements ApiClientInterface:

```php
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;

class ApiClient implements ApiClientInterface
{
    public function getClientId(): string
    {
        ...
    }

    public function getClientSecret(): string
    {
        ...
    }

    public function getClientScope(): array
    {
        ...
    }
}
```

### ApiUser

**OPTIONAL:** If you want to restrict certain actions within your API to certain users, create an entity that implements ApiUserInterface and also create a UserProvider:

```php
use WernerDweight\ApiAuthBundle\Entity\ApiUserInterface;

class ApiUser implements ApiUserInterface
{
    public function setApiToken(string $apiToken): ApiUserInterface
    {
        ...
    }
    
    public function getApiToken(): string
    {
        ...
    }
    
    public function setApiTokenExpirationDate(\DateTime $apiTokenExpirationDate): ApiUserInterface
    {
        ...
    }
    
    public function getApiTokenExpirationDate(): ?\DateTime
    {
        ...
    }
    
    public function getUserScope(): array
    {
        ...
    }
}
```

TODO: provider

Usage
------------

TODO:

### Events

The following events are dispatched, so you can hook in the process. For general info on how to use events, please consult the [official Symfony documentation](https://symfony.com/doc/current/event_dispatcher.html).

TODO:

License
-------
This bundle is under the MIT license. See the complete license in the root directiory of the bundle.
