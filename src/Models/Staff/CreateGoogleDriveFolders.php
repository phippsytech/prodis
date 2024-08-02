<?php
namespace NDISmate\Models\Staff;

use NDISmate\GoogleAPI\Drive;
use \NDISmate\CORE\TransactionalDatabase;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


class CreateGoogleDriveFolders{

    function __invoke($data){

        $staff_folder = KeyValue::get('google_staff_folder');
        if(!$staff_folder) return ["http_code"=>400, "error"=>"No staff folder set"];

        $staffs = R::getAll( 
            'SELECT 
                id,
                name
            FROM staffs 
            WHERE (archived is null or archived!=1) 
                AND google_folder is null
            ');

            

        $drive = new Drive;
            
        foreach ($staffs as $staff) {
            
            $folder_id = $drive->createFolder([
                "folder_name"=>$staff['name'],
                "parent_folder_id"=>$staff_folder
            ]);

            // update staff record with reference to the folder.
            $bean = R::load("staffs", $staff['id']);
            $bean->google_folder = $folder_id;
            $db_result =TransactionalDatabase::store($bean);

            $created[] =[
                "name"=>$staff['name'],
                "folder_id"=>$folder_id
            ];

        }

        return ["http_code"=>200, "result"=>$created];

    }
}