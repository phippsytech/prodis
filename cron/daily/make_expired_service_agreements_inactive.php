#!/usr/bin/env php
<?php
require '/var/www/prodis/init.php';

use NDISmate\Models\Participant\ServiceAgreement\MakeExpiredServiceAgreementsInactive;

(new MakeExpiredServiceAgreementsInactive)();