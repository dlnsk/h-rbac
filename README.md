# h-rbac

Based on native Laravel's gates and policies. Hierarchical RBAC with callbacks.

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

In the process of creating my own projects I have formed an opinion about the minimum required ability of RBAC. It
should allow:

- roles and permissions
- callbacks for permissions (for passing parameters in permission checking)
- permission's inheritance (to give different abilities to different roles)
- optimal way to manage RBAC

## Install

> Supports Laravel since v5.1 to the latest (11.\* and above).

Via Composer

``` bash
$ composer require dlnsk/h-rbac
```

Publish some cool stuff:

- config file (config/h-rbac.php) to configure role/permission
- example policy (app/Policies/PostPolicy.php) to configure chains and callbacks

with

    php artisan vendor:publish --tag=hrbac-config

Add permissions, its chains and callbacks to policies.

Add roles and its permissions to config file. That's all!

## Overview

This module is wrapper for [authorization logic](https://laravel.com/docs/11.x/authorization#creating-policies) and
control access to resources of Laravel 5.1 and later.

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

That's what you can do with Laravel's policies as it described in docs. But Laravel doesn't support roles.

### Permission's inheritance

As you see callbacks is very useful. But what about a site manager who may edit any posts? Create separate permission?
But which of it we should check?

Answer is to use chained (inherited) permissions. Example:

`edit` -> `editAnyPost` -> `editPostInCategory` -> `editOwnPost`

Each of this permission put in appropriate role, but **we always check the first** (except in very rare cases):

``` php
if (\Gate::can('edit', $post)) {
}
```

These permissions in the chain will be checked one by one until one of it will pass. In other case the ability will be
rejected for this user. So, we have many permissions with different business logic but checking in the code only one.
This is the key!

### The ways to manage RBAC

It is very popular to use database to store roles and permissions. It flexible but hard to support. Managing of roles
and permissions required backend. When we start to use inheritance for permissions it becomes too difficult for direct
changing.

#### Static roles

Most projects aren't large. It needs only a few roles and permissions, so backend becomes economically
inexpedient. Thus, I believe that file driven RBAC is enough for many projects where users can have one or many roles
simultaneously. Defining roles in config is visual and simple for support.

By default, roles applied to user store in database, so you should add an accessor to `User` model to get list of roles.
Name of attribute you can change in `config/h-rbac.php` if you need.

``` php
public function getRolesAttribute() {
    return $this->roles()->pluck('name')->toArray();
}
```

It doesn't matter how you store roles. Just return an array from function. You also can write and bind your
own `RolesProvider` through `DI`.

#### Static roles + overriding permissions

Some projects may suppose that administrator can override some user's permissions (include or exclude). If you have
dozens of such users it's hard to make different roles for each. So overriding is a good choice.

It requires additional database table to store overrides. Publish migration with

    php artisan vendor:publish --tag=hrbac-migrations

and bind `EloquentPermissionProvider` inside your `AppServiceProvider.php` bindings:

``` php
public $bindings = [
    PermissionsProvider::class => EloquentPermissionProvider::class,
    ...
];
```

Now every record in `permissions` table adds or removes one permission from user. You also can store additional value 
with an overridden permission. Here is the example:

Let's add the callback in `PostPolicy`.

``` php
public function editPostInCategory($user, $post, $permission): bool {
    return $post->category_id === $permission->value;
}
```

Now, if we add to `permissions` table next record

``` php
[
    'user_id' => 100,
    'name' => 'editPostInCategory',
    'action' => 'include',
    'value' => 5,
]
```

we'll allow user with `id = 100` edit any post in category with `id = 5`.

#### Different way

Keep in mind that you can bind your own `RolesProvider` or/and `PermissionsProvider` and store roles and permissions
as you wish. It's very flexible.

## Usage

As I said `h-rbac` is wrapper for [authorization logic](https://laravel.com/docs/11.x/authorization#creating-policies)
since Laravel 5.1 to this time. So, you can use any features of it.

```php
if (\Gate::allows('edit', $post)) { /* do something */ }
...
if (\Gate::denies('edit', $post)) { abort(403); }
...
if (\Gate::forUser($user)->allows('edit', $post)) { /* do something */ }
```

From User model:

```php
if ($request->user()->can('edit', $post)) { /* do something */ }
...
if ($request->user()->cannot('edit', $post)) { abort(403); }
```

In controller:

```php
$this->authorize('edit', $post);
```

Within Blade

    @can('edit', $post)
        <!-- The Current User Can Update The Post -->
    @else
        <!-- The Current User Can't Update The Post -->
    @endcan

Also in `h-rbac` we have added directive `@role` which you can combine with `@else`

    @role('user|manager')
        <!-- Current user has any of those roles -->
    @endrole

### Permission without model

Native Laravel's permissions hard linked with models. So the model object or model class are required to check ability, 
because it defines a right Policy that includes callbacks:

```php
$this->authorize('edit', $post);
$this->authorize('create', Post::class);
```

If your permission isn't linked to model you can use policy class as a place to search callback:

```php
$this->authorize('download', ReportPolicy::class);
```

## Configuration

### Permissions

Permissions and callbacks are defining in Policies as it describes in 
[docs](https://laravel.com/docs/11.x/authorization#creating-policies). Innovation is the chains of permissions.

```php
class PostPolicy
{
    public $chains = [
        'edit' => [
            'editAnyPost',
            'editPostInCategory',
            'editOwnPost',
        ],
        'delete' => [
            'deleteAnyPost',
            'deleteOwnPost',
        ],
    ];

    ////////////// Callbacks ///////////////

    public function editOwnPost($user, $post) {
        return $user->id === $post->user_id;
    }

    public function editPostInCategory($user, $post, $permission): bool {
        return $post->category_id === $permission->value;
    }
}
```

You should add callback only if you need additional check for this permission. **The name of callback should be
camelcased name of permission.**

We use next logic for checking permission: checking all permissions in chain one by one and:

- **allow** if user has a permission with no callback
- **allow** if user has a permission and callback return **true** (in this case we don't check any remaining permission
  in chain)
- **deny** if user hasn't any permission in chain
- **deny** if callbacks of all user's permissions return **false**

### Roles

Roles are defining in config file (config/h-rbac.php)

``` php
<?php return [
    /**
     * Built-in application roles and its permissions
     */
    'builtinRoles' => [
        'manager' => [
            'editAnyPost',
            'deleteAnyPost',
            'seeReportsInCategory',
        ],
        'user' => [
            'editOwnPost',
            'seeOwnReports',
        ],
    ],

];
```

If you want to store assigning permissions to roles in database, just make your 
own `RolesProvider` to change this logic.

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
