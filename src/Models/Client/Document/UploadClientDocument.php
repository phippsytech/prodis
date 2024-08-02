<?php
namespace NDISmate\Models\Client\Document;

use \RedBeanPHP\R as R;
use \NDISmate\CORE\KeyValue;
use \NDISmate\GoogleAPI\Drive;


class UploadClientDocument {

    
    public function __invoke($data) {

        $folder_id = $data['folder_id'];
        $client_id = $data['client_id'];
        $file_list = $data['file_list'];


        // $data contains an array of files.
        // name, size, type, data (in base64)
        $errors=[];

        // I don't need to look up the folder id if it is passed in.
        // $google_folder = R::getCell('SELECT google_folder FROM clients WHERE id=:client_id', [":client_id"=>$data['client_id']]);

        foreach ($file_list as $file){
            
            // $file['data']=$this->getBinary($file['data']); // do I need to use getBinary to massage this?

            $drive = new Drive;
            $result = $drive->upload([
                'base64_file'=>$file['data'],
                'name'=>$file['name'],
                'folder_id'=>$folder_id,
                'file_properties'=>[
                    'client_id'=>$client_id,
                ]
            ]);

            if($result["http_code"]==400){
                $errors[]=$result["error_message"];
            }

        }

        return ["http_code"=>201, "errors"=>$errors];

    }


    function getBinary($data){
        list($mime_type_array,$base64_array)=explode(";",$data);
        $mime_type_array=explode(":",$mime_type_array);
        $mime_type = $mime_type_array[1];
        $base64_array=explode(",",$base64_array);
        $base64_file = $base64_array[1];
        $binary = base64_decode($base64_file);
        return $binary;
    }


}
