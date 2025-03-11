<?php

namespace App\Enums;

enum BookStatus: string
{
    case Available = 'available';
    case Borrowed = 'borrowed';
}
