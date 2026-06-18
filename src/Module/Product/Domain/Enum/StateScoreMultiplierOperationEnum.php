<?php

namespace App\Module\Product\Domain\Enum;

enum StateScoreMultiplierOperationEnum: string
{
    case PLUS = 'plus';
    case MINUS = 'minus';
}
