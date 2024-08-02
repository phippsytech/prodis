<?php
namespace NDISmate\Models\Staff;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use NDISmate\GoogleAPI\Drive;
use \RedBeanPHP\R as R;
// use NDISmate\CORE\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Document extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("staffdocument", [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'name' => [v::stringType()], // eg Cert 4 in Disability support
            'google_drive_file_ref' => [v::stringType()], 

        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["uploadStafferDocument", "upload", true, [] ],
            ["listStafferDocuments", "getAllDocuments", true, [] ],
            ["deleteStafferDocument", "deleteDocument", true, [] ],
        ];

        return $this->invoke($request, $response, $args, $this);
    }


    // Additional Methods and overrides here:

    function getAllDocuments($data){
        
        $beans = R::findAll( 'staffdocuments', 'staff_id = :staff_id', [':staff_id'=>$data['staff_id']] );
        $beans = R::exportAll($beans);
        if(!count($beans)) {
            return ["http_code"=>404, "error_message"=>"Not Found"];
        } else{
            return ["http_code"=>200, "result"=>$beans];  //R::exportAll($beans, TRUE)
        }
        
    }


    function upload($data){
        
        $drive = new Drive;
        $result = $drive->upload($data);

        $this->create([
            "staff_id" => $data['staff_id'],
            "name" => $data['name'],
            "google_drive_file_ref" => $result['file_id']
        ]);

        return ["http_code"=>201];

    }

    function deleteDocument($data){
        $document = $this->getOne($data);
        $file_id = $document["result"]["google_drive_file_ref"];
        $drive = new Drive;
        $result = $drive->deleteFile(["file_id"=>$file_id]);

        $this->delete($data);

        return ["http_code"=>200];
    }

}