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

Only client configuration is mandatory, default values are mentioned in comments. 

```yaml
# config/packages/api_auth.yaml
api_auth:
    # api client configuration (mandatory)
    client:
        # your entity that implements ApiClientInterface
        class: App\Entity\ApiClient
        # property of the entity that is used as client id
        # you may also make your ApiClientRepository extend UserLoaderInterface and ommit the property setting
        property: clientId
        # if set to true, client scope will be checked before granting access (see `scope access` below) - deafult false
        use_scope_access_model: true
        # the checker used to check scope access - defaults to RouteChecker (see below)
        access_scope_checker: App\Service\MyAccessScopeChecker 
    
    # api user configuration (optional)
    # if you ommit user configuration, you will not be able to use `on-behalf` access mode (see below)
    user:
        # your entity that implements ApiUserInterface
        class: App\Entity\User
        # your entity that implements ApiUserTokenInterface
        token: App\Entity\UserToken
        # the property to use as login when authenticating user - defaults to 'username'
        login_property: email
        # token expiration in seconds - defaults to 2,592,000 (30 days) 
        api_token_expiration_interval: 3600
        # if set to true, user scope will be checked before granting access (see `scope access` below) - deafult false
        use_scope_access_model: true
        # the checker used to check scope access - defaults to RouteChecker (see below)
        access_scope_checker: App\Service\MyAccessScopeChecker

    # list of controllers to target (optional)
    # default 'WernerDweight\ApiAuthBundle\Contrtoller\ApiAuthControllerInterface'
    target_controllers:         
        - '*'   # all controllers or list specific controllers (see next line)
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
    providers:
        wds_api_auth_provider:
            id: WernerDweight\ApiAuthBundle\Security\ApiClientProvider
            
    # ...
    encoders:
        App\Entity\User:    # your user entity
            algorithm: # ...
    
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
            # you MUST set stateless to `true` if you want to use `on behalf` access mode (see below)
            # stateless: true
```

### ApiClient

Create an entity that implements ApiClientInterface:

```php
use WernerDweight\ApiAuthBundle\Entity\ApiClientInterface;

class ApiClient implements ApiClientInterface
{
    // this trait solves the extension of UserInterface
    // (which is required by symfony authentication process, but not necessary for the ApiClient as such)
    use ApiClientTrait;
    
    /**
     * Please implement the following methods by yourself.
     * You will need to store the data in some fields and ApiAuthBundle doesn't want to force you to use exact field names and mappings.
     */

    public function getClientId(): string
    {
        ...
    }

    public function getClientSecret(): string
    {
        ...
    }

    public function getClientScope(): AccessScope
    {
        return new AccessScope($this->clientScope);
    }
}
```

### ApiUser

**OPTIONAL:** If you want to restrict certain actions within your API to certain users ("on behalf" access mode), create an entity that implements ApiUserInterface and also create a UserProvider:

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
    
    public function getUserScope(): AccessScope
    {
        return new AccessScope($this->userScope);
    }
}
```
TODO: repository (extends AbstractApiUserRepository)
TODO: provider

Usage
------------

TODO: scope access (incl. checkers - tag to imoplement your own)
TODO: on-behalf access mode

### Events

The following events are dispatched, so you can hook in the process. For general info on how to use events, please consult the [official Symfony documentation](https://symfony.com/doc/current/event_dispatcher.html).

TODO:

License
-------
This bundle is under the MIT license. See the complete license in the root directiory of the bundle.
