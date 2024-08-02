<?php
namespace NDISmate\Accounting;

class AccountingFactory {
    public static function create($data) {
        $settings = getSettings(); // Replace with code to get the settings

        if ($settings['use_xero']) {
            return new Xero();
        } else {
            return new MYOB();
        }
    }
}