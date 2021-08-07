<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiAuthGroups
{
       public function __construct(public $groups)
       {
       }
}
