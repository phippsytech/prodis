<?php
namespace NDISmate\Models\Client;

use NDISmate\GoogleAPI\Drive;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class Document extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('clientdocument', [
            'id' => [v::numericVal()],
            'client_id' => [v::numericVal()],
            'name' => [v::stringType()],  // eg Behaviour Management Plan
            'google_drive_file_ref' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['uploadClientDocument', 'uploadClientDocument', true, []],
            ['listClientDocuments', 'getAllDocuments', true, []],
            ['deleteClientDocument', 'deleteDocument', true, []],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function uploadClientDocument($data)
    {
        return (new \NDISmate\Models\Client\Document\UploadClientDocument)($data);
    }

    function getAllDocuments($data)
    {
        $beans = R::findAll('clientdocuments', 'client_id = :client_id', [':client_id' => $data['client_id']]);
        $beans = R::exportAll($beans);
        if (!count($beans)) {
            return ['http_code' => 404, 'error_message' => 'Not Found'];
        } else {
            return ['http_code' => 200, 'result' => $beans];  // R::exportAll($beans, TRUE)
        }
    }

    function deleteDocument($data)
    {
        $document = $this->getOne($data);
        $file_id = $document['result']['google_drive_file_ref'];
        $drive = new Drive;
        $result = $drive->deleteFile(['file_id' => $file_id]);

        $this->delete($data);

        return ['http_code' => 200];
    }
}
