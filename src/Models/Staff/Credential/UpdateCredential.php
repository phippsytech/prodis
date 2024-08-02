<?php
namespace NDISmate\Models\Staff\Credential;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;
use \NDISmate\GoogleAPI\Drive;


class UpdateCredential{

    function __invoke($data){

        $this->CRUD = new CRUD("staffcredential", [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'credential_id' => [v::numericVal()],
            'details' => [v::stringType()], // eg Cert 4 in Disability support
            'google_drive_file_ref' => [v::stringType()], 
            'credential_date' => [v::stringType()]
        ]);

        // If a file has been uploaded, upload it to google drive and get the file id
        if($data['base64_file']){
            $staffer = R::getRow('SELECT name,google_folder FROM staffs WHERE id=:staff_id', [":staff_id"=>$data['staff_id']]);
            $google_folder = $staffer['google_folder'];
            $staff_name = $staffer['name'];
            $credential_name = R::getCell('SELECT name FROM credentials WHERE id=:id', [":id"=>$data['credential_id']]);

            $file_name = date("Y-m-d")."_".$staff_name . "_" . $credential_name;

            if($data['file_extension']){
                $file_name .= "." . $data['file_extension'];
            }

            $drive = new Drive;
            $result = $drive->upload([
                'base64_file'=>$data['base64_file'],
                'name'=>$file_name,
                'folder_id'=>$google_folder
            ]);

            $data['google_drive_file_ref'] = $result['file_id'];
        }

        $update_result = $this->CRUD->update($data);

        return $update_result;

    }

}