<?php
namespace NDISmate\Accounting;

interface AccountingInterface {
    public function listFolders($data);
    public function listFiles($data);
    public function getFile($data);
    public function upload($data);
}
