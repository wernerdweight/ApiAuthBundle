ApiAuthBundle
====================================

Symfony bundle providing API authentication and authorization.

[![Build Status](https://app.travis-ci.com/wernerdweight/ApiAuthBundle.svg?branch=master)](https://app.travis-ci.com/wernerdweight/ApiAuthBundle)
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
        # property of the entity that is used as client id - defaults to `clientId`
        # you may also make your ApiClientRepository extend UserLoaderInterface and null the property setting
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

    # if true, requests using the OPTIONS method will be ignored (authentication will be skipped)
    # default false
    exclude_options_requests: true
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

You need to create an entity that implements ApiClientInterface. The easiest option is to extend the existing AbstractApiClient entity like so:

```php
<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use WernerDweight\ApiAuthBundle\Entity\AbstractApiClient;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApiClientRepository")
 */
class ApiClient extends AbstractApiClient
{
    /* put your custom fields and methods here */
}
```

You can also directly implement the `WernerDweight\ApiAuthBundle\Entity\ApiClientInterface` if you want to avoid inheritance.

### ApiUser

**OPTIONAL:** If you want to restrict certain actions within your API to certain users (see 'on behalf' access mode below), create an entity that implements ApiUserInterface and another one that implements ApiUserTokenInterface. The easiest option is to extend AbstractApiUser and AbstractApiUserToken entities like so:

```php
<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use WernerDweight\ApiAuthBundle\Entity\AbstractApiUser;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
final class User extends AbstractApiUser implements UserInterface
{
    /* put your custom fields here */

    /**
     * @var ArrayCollection|PersistentCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserToken", mappedBy="apiUser")
     */
    protected $apiTokens;
    
    /* put your custom methods here */
    /* FYI: AbstractApiUser already has getter, adder and remover for `$apiTokens` */
}
```

```php
<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use WernerDweight\ApiAuthBundle\Entity\AbstractApiUserToken;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserTokenRepository")
 */
class UserToken extends AbstractApiUserToken
{
    /* put your custom fields here */

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="apiTokens")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $apiUser;

    /* put your custom methods here */
    /* FYI: AbstractApiUserToken already has getter adn setter for `$apiUser` */
}
```

**WARNING:** If you implement custom UserRepository (doctrine entity repository for your entity that implements ApiUserInterface), you must extend `AbstractApiUserRepository` or implement `ApiUserRepositoryInterface`!

```php
<?php
namespace App\Repository;

use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use WernerDweight\ApiAuthBundle\Repository\AbstractApiUserRepository;

class UserRepository extends AbstractApiUserRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }
}
```

Usage
------------

### General usage

You simply have to include respective client id and secret in requests that require authentication.

```http request
GET /your/api/path/ HTTP/1.1
X-Client-Id: some-client-id
X-Client-Secret: some-client-secret
Host: your-api-host.com
```

### Scope access

You can optionally enable scope checking (via `api_auth.client.use_scope_access_model` or `api_auth.user.use_scope_access_model` configuration settings).

If enabled, the authenticator will also (apart from api credentials) check the defined client/user scope using configured checker (if no checker is explicitly configured (`api_auth.client.access_scope_checker` or `api_auth.user.access_scope_checker`) the default `RouteChecker` is used). This way, different ApiClients/Users can have different privileges.

The scope is generally a JSON column on ApiClient/ApiUser entities. You can store any information in that column and then use any checker you want to read and evaluate the stored information.

The default RouteChecker expects a structure like this:

```json5
{
  "route_name": true,
  // following line is a no-op, the route doesn't have to be specified if it should not be accessible
  "another_route_name": false,
  // see `on-behalf` access mode below
  "yet_another_route_name": 'on-behalf',
}
```

ApiAuthBundle also by default includes a checker for [DoctrineCrudApiBundle](https://github.com/wernerdweight/DoctrineCrudApiBundle), that expects this structure:
```json5
{
  "entityName": {
    "list": true,
    "length": false,
    "detail": "on-behalf"
  },
  "anotherEntityName": {
    "list": true,
    "detail": "on-behalf"
  }
}
```

You can also implement custom checker (don't forget to tag your checker with `api_auth.access_scope_checker`):

```yaml
# config/packages/api_auth.yaml
api_auth:
    client:
        # ...
        use_scope_access_model: true
        access_scope_checker: App\Service\MyAccessScopeChecker
```

```php
<?php
namespace App\Service;

use WernerDweight\ApiAuthBundle\DTO\AccessScope;
use WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\Checker\AccessScopeCheckerInterface;

final class MyAccessScopeChecker implements AccessScopeCheckerInterface
{
    public function check(AccessScope $scope): string
    {
        if (/* ... */) {
            return ApiAuthEnum::SCOPE_ACCESSIBILITY_ACCESSIBLE;
        }
        
        if (/* ... */) {
            return ApiAuthEnum::SCOPE_ACCESSIBILITY_ON_BEHALF;
        }
        
        return ApiAuthEnum::SCOPE_ACCESSIBILITY_FORBIDDEN;
    }
}
```

```yaml
# services.yaml
App\Service\MyAccessScopeChecker:
    tags:
        - { name: 'api_auth.access_scope_checker' }
```


### on-behalf access mode

If the ApiClient/ApiUser scope is configured to be checked (see above) and the `'on-behalf'` value is set in the scope, another authentication is required.

The request must then contain the `X-Api-User-Token` header with a valid token. To obtain the token, the user must login using Basic Auth - the request should look as follows:

```http request
POST /authenticate/ HTTP/1.1
X-Client-Id: some-client-id
X-Client-Secret: some-client-secret
Authorization: Basic encodedBasicAuthInformation==
Host: your-api-host.com
```

The response contains the token and scope (and optionally any other information returned from your user entity via json serialization):

```json
{
  "token": {
    "token": "aBc37De4FgH_-abC08d7eF",
    "expirationDate": "2019-08-15T22:06:08+02:00"
  },
  "userScope": {
    "someRoute": true,
    "someOtherRoute": "on-behalf"
  }
}
```

**WARNING:** If you overload the default `jsonSerialize` method, don't forget to include the parent return value:

```php
<?php

final class User extends AbstractApiUser implements UserInterface
{
    /* ... */
    
    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'id' => $this->getId(),
                'email' => $this->getEmail(),
                // any other attributes you need to include in the response
            ],
            parent::jsonSerialize()
        );
    }
}
```

You can then use the obtained token in requests that require the `'ob-behalf'` access mode like this:

```http request
GET /your/api/path HTTP/1.1
X-Client-Id: some-client-id
X-Client-Secret: some-client-secret
X-Api-User-Token: aBc37De4FgH_-abC08d7eF
Host: your-api-host.com
```

**FYI:** The `'on-behalf'` value only makes sense for client scope. If you set `'on-behalf'` as value inside the user scope, the value is interpreted in the same way as `true`.

### Events

The following events are dispatched, so you can hook in the process. For general info on how to use events, please consult the [official Symfony documentation](https://symfony.com/doc/current/event_dispatcher.html).

**ApiClientCredentialsCheckedEvent (`wds.api_auth_bundle.api_client_credentials_checked`)** \
Issued after the client credentials have been checked. Contains the client and credentials being checked. You can call `setValid` on the event to change the check result.

**ApiUserAuthenticatedEvent (`wds.api_auth_bundle.api_user_authenticated`)** \
Issued after the user has been authenticated using the `authenticate` endpoint. Contains the authenticated user.

**ApiUserTokenCheckedEvent (`wds.api_auth_bundle.api_user_token_checked`)** \
Issued after the `'on-behalf'` token check. Contains the user and token being checked. You can call `setValid` on the event to change the check result.

**ApiUserTokenRefreshEvent (`wds.api_auth_bundle.api_user_token_refresh`)** \
Issued during the `'on-behalf'` token is generated. Contains the user and generated token. You can call `setToken` on the event to change the token.

License
-------
This bundle is under the MIT license. See the complete license in the root directiory of the bundle.
