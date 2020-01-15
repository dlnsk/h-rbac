# h-rbac

Based on native Laravel's 5.\*/6.\* abilities. Hierarchical RBAC with callbacks.

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

In the process of creating my own projects I have formed an opinion about the minimum required ability of RBAC. It should allow:

 - roles and permissions
 - callbacks for permissions (for passing parameters in permission checking)
 - permission's inheritance
 - the way RBAC is served

## Install

> Keep in mind it's only for Laravel 5.1 and later (6.\* also supported).

Via Composer

``` bash
$ composer require dlnsk/h-rbac
```

Add the service provider to `config/app.php`. We use auto-discovering feature since Laravel 5.5, so you may don't touch `app.php`.

    Dlnsk\HierarchicalRBAC\HRBACServiceProvider::class,

Publish some cool stuff:

 - config file (config/h-rbac.php)
 - migration (adding field `role` to `users` table), only if you suppose to use one role per user
 - role/permission/callbacks configuration class (app/Classes/Authorization/AuthorizationClass.php)

with

    php artisan vendor:publish --provider="Dlnsk\HierarchicalRBAC\HRBACServiceProvider"

Add roles, permissions which you need and callbacks where it needs and have fun!

### Many To Many with roles

If you want to use "Many To Many" relationship between users and roles add an accessor to `User` model. Name of attribute you can change in `config/h-rbac.php` if you need:

``` php
public function getOwnRolesAttribute() {
    return $this->roles()->pluck('name')->toArray();
}
```

## Overview

This module is wrapper for [authorization logic](https://laravel.com/docs/5.2/authorization#checking-abilities) and control access to resources of Laravel 5.1 and later. Except you shouldn't define abilities, they will define automatically.

**Let's describe the minimum required ability of RBAC** (in my opinion).

### Roles and permissions

It's clear.

### Callbacks for permissions

Very common situation is to allow user to change only his own posts. With this package it's simple:

``` php
public function editOwnPost($user, $post) {
    return $user->id === $post->user_id;
}
```

and use as

``` php
if (\Gate::can('editOwnPost', $post)) {
}
```
You can pass any number of parameters in callback as array.

### Permission's inheritance

As you see callbacks is very useful. But what about site manager who may edit any posts? Create separate permission? But which of it we should check?

Answer is use chained (inherited) permissions. Example:

`editPost` -> `editPostInCategory` -> `editOwnPost`

Each of this permission put in appropriate role but **we always check the first** (except in very rare cases):
``` php
if (\Gate::can('editPost', $post)) {
}
```
These permissions will be checked one by one until one of it will pass. In other case ability will be rejected for this user. So, we have many permissions with different buisnes logic but checking in code only one.

### The way RBAC is served

Very popular is to use database for store roles and permissions. It flexible but hard to support. Managing of roles and permissions required backend (but stil available to change directly in DB). When we start to use inheritance for permissions it becomes too difficult for direct changing.

In other case most projects aren't large. It need only few roles and permissions, so backend becomes economically inexpedient. Thus, I believe that file driven RBAC is enough for many projects. It's visual and simple for support.

Storage of roles and permissions is on another level of logic, so DB support may be added later.

## Usage

As I said `h-rbac` is wrapper for [authorization logic](https://laravel.com/docs/5.2/authorization#checking-abilities) of Laravel 5.1 and later. So, you can use any features of it.

```php
if (\Gate::allows('editPost', $post)) { /* do something */ }
...
if (\Gate::denies('editPost', $post)) { abort(403); }
...
if (\Gate::forUser($user)->allows('editPost', $post)) { /* do something */ }
```

From User model:

```php
if ($request->user()->can('editPost', $post)) { /* do something */ }
...
if ($request->user()->cannot('editPost', $post)) { abort(403); }
```

In controller:

```php
$this->authorize('editPost', $post);
```

Within Blade

    @can('editPost', $post)
        <!-- The Current User Can Update The Post -->
    @else
        <!-- The Current User Can't Update The Post -->
    @endcan

Also in `h-rbac` we add directive `@role` which you can combine with `@else`


    @role('user|manager')
        <!-- The current user has any role -->
    @endrole

## Configuration

When you publish configuration with `artisan` you'll have configuration class `app/Classes/Authorization/AuthorizationClass.php` where you should define permissions, roles and callbacks. You are free to move this file anywhere you want. Don't forget update `config/h-rbac.php` in this case.

Structure of configuration class:

```php
<?php
namespace App\Classes\Authorization;
use Dlnsk\HierarchicalRBAC\Authorization;

class AuthorizationClass extends Authorization
{
    public function getPermissions() {
        return [
            'editPost' => [
                    'description' => 'Edit any posts',  // optional property
                    'next' => 'editOwnPost',            // used for making chain (hierarchy) of permissions
                ],
            'editOwnPost' => [
                    'description' => 'Edit own post',
                ],
            'deletePost' => [
                    'description' => 'Delete any posts',
                ],
        ];
    }

    public function getRoles() {
        return [
            'manager' => [
                    'editPost',
                    'deletePost',
                ],
            'user' => [
                    'editOwnPost',
                ],
        ];
    }

    ////////////// Callbacks ///////////////

    public function editOwnPost($user, $post) {
        $post = $this->getModel(\App\Post::class, $post);  // helper method for geting model

        return $user->id === $post->user_id;
    }

}
```

You should add callback only if you need additional check for this permission. **The name of callback should be camelcased name of permission.**

We use next logic for checking permission: starting from the current permission, we check all of the following in chain one by one and:

 - **allow** if role has a permission with no callback
 - **allow** if role has a permission and callback return **true**
 - **deny** if role hasn't any permission in chain
 - **deny** if role has a permission but callback return **false** (in this case we don't check any remaining permission in chain)

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Credits

- [Dmitry Pupinin][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/dlnsk/h-rbac.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/dlnsk/h-rbac/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/dlnsk/h-rbac.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/dlnsk/h-rbac.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dlnsk/h-rbac.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/dlnsk/h-rbac
[link-travis]: https://travis-ci.org/dlnsk/h-rbac
[link-scrutinizer]: https://scrutinizer-ci.com/g/dlnsk/h-rbac/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/dlnsk/h-rbac
[link-downloads]: https://packagist.org/packages/dlnsk/h-rbac
[link-author]: https://github.com/dlnsk
[link-contributors]: ../../contributors
