<?php
namespace NDISmate\Models\SIL\House\Roster;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 
use \RedBeanPHP\R as R;


class Template extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("housetemplate", [
            'id' => [v::numericVal()],
            'house_id' => [v::numericVal()],
            'template' => [v::stringType()],          
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["createTemplate" , "createTemplate", false, ["admin"] ],
            ["getTemplate", "getTemplate", false, [] ],
            ["updateTemplate", "updateTemplate", true, ["admin"] ],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function createTemplate($data){
        $template = (new \NDISmate\Models\SIL\House\Roster\CreateTemplate)($this, $data);
        return $this->getTemplate($data);
        // return ["http_code"=>200, "result"=>$template];
    }

    function getTemplate($data){
        $bean = R::findOne('housetemplates', 'house_id=:house_id', [':house_id'=>$data['house_id']]);
        if(!$bean){ // no existing record.
            return ["http_code"=>404, "error_message"=>"Template not found"]; // return 201 to indicate created
        }
        $result = $bean->export();
        // $bean["template"] = json_decode($bean["template"], true);
        $template =  json_decode(stripslashes($bean["template"]), true);
        $result['template'] = $template;
        return ["http_code"=>200, "result"=>$result];
    }

    
    function updateTemplate($data){
        $data['template'] = json_encode($data['template']);
        $result = $this->update($data);
        return ["http_code"=>200];
    }

}