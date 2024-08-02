<?php
namespace NDISmate\Services\ValidationService;

use NDISmate\CORE\JsonResponse;
use NDISmate\Postcode\Lookup;

final class Postcode
{
    public function __invoke($data)
    {
        $postcode = trim($data['postcode']);
        return (new Lookup)($postcode);
    }
}
