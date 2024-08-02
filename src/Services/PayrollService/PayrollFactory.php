<?php
namespace NDISmate\Payroll;

class PayrollFactory {
    public static function create($data) {
        $settings = getSettings(); // Replace with code to get the settings

        if ($settings['use_xero']) {
            return new Xero();
        } else {
            return new MYOB();
        }
    }
}