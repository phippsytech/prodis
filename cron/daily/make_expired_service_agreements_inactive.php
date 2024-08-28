#!/usr/bin/env php
<?php
require '/var/www/prodis/init.php';

(new \NDISmate\Models\Participant\ServiceAgreement\MakeExpiredServiceAgreementsInactive)();
(new \NDISmate\Models\Client\HoldClientsWithoutActiveAgreements)();