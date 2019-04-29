<?php
//declare(strict_types=1);

namespace App\Enum;


final class DoctorStatus extends Enum
{
    const NEW = 1;
    const DRAFT = 2;
    const PUBLISHED = 3;
    const UNPUBLISHED = 4;
    const DELETED = 5;
}
