<?php
namespace NDISmate\CORE\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

final class MonetaryAmountException extends ValidationException
{
    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'A valid monetary value is required.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Do not enter a monetary value.',
        ],
    ];
}
