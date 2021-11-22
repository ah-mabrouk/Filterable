<?php

namespace Mabrouk\Filterable\Tests\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Mabrouk\Filterable\Traits\Filterable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Filterable, Authorizable, Authenticatable;

    protected $guarded = [];

    protected $table = 'users';
}