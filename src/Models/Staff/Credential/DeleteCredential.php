<?php
namespace NDISmate\Models\Staff\Credential;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;
use \NDISmate\GoogleAPI\Drive;
use \NDISmate\CORE\TransactionalDatabase;


class DeleteCredential{

    function __invoke($data){

        // first get the staff credential
        $bean =  R::load('staffcredentials', $data['id']);

        if (!$bean->id) return ["http_code"=>404, "error_message"=>"Not found."];

        
        // if google_drive_file_ref exists then we need to delete that file.
        if (isset($bean['google_drive_file_ref'])){
            try {
                $drive = new Drive;
                $drive->deleteFile(["file_id"=>$bean['google_drive_file_ref']]);
            } catch (\Google\Service\Exception $e) {
                // Handle the exception here
                // For example, log the error or display a user-friendly message
                // return ["http_code"=>400,"error_message"=>"Error deleting file: " . $e->getMessage()];
                
            }
        }

        // now delete the staff credential
        TransactionalDatabase::trash($bean);
        return ["http_code"=>200];

    }

}