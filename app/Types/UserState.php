<?php
/**
 * Created by PhpStorm.
 * User: burt
 * Date: 21.11.2019
 * Time: 11:42
 */

namespace App\Types;

class UserState
{
    public const NEW = 1;
    public const DRAFT = 2;
    public const PUBLISHED = 3;
    public const UNPUBLISHED = 4;
    public const DELETED = 5;
}
