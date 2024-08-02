<?php
namespace NDISmate\CORE\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validator as v;

final class MonetaryAmount extends AbstractRule
{
    public function validate($input): bool
    {
        // Allow both integers and decimals with exactly two decimal places

        return v::oneOf(
            v::numericVal()->decimal(2),
            v::numericVal()->intVal(),
            v::nullType()
        )->validate($input);
    }
}
