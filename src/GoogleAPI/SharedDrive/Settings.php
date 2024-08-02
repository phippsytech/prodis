<?php
namespace NDISmate\GoogleAPI\SharedDrive;

use \NDISmate\CORE\KeyValue;


class Settings{

    function getSettings(){
        $shared_drive = KeyValue::get('google_shared_drive');		
        $participants_folder = KeyValue::get('google_participants_folder');		
        $staff_folder = KeyValue::get('google_staff_folder');		
        return ["http_code"=>200, "result"=>[
            "shared_drive"=>$shared_drive,
            "participants_folder"=>$participants_folder,
            "staff_folder"=>$staff_folder
        ]];
    }

    function setSettings($data){

        $data= $data['settings'];
        
        if(isset($data['shared_drive'])) KeyValue::set('google_shared_drive', $data['shared_drive']);		
        if(isset($data['participants_folder'])) KeyValue::set('google_participants_folder', $data['participants_folder']);		
        if(isset($data['staff_folder'])) KeyValue::set('google_staff_folder', $data['staff_folder']);		
        return ["http_code"=>200, "result"=>$data];
    }

    // function setSharedDrive($data){
    //     KeyValue::set('google_shared_drive', $data['shared_drive']);		
    //     return ["http_code"=>200];
    // }

    // function setParticipantsFolder($data){
    //     KeyValue::set('google_participants_folder', $data['participants_folder']);		
    //     return ["http_code"=>200];
    // }

    // function setStaffFolder($data){
    //     KeyValue::set('google_staff_folder', $data['staff_folder']);		
    //     return ["http_code"=>200];
    // }

}



    