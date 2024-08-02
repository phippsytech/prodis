<?php
namespace NDISmate\Payroll;

interface PayrollInterface {
    public function listFolders($data);
    public function listFiles($data);
    public function getFile($data);
    public function upload($data);
}
