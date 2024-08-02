<?php
namespace NDISmate\GoogleAPI;

use \NDISmate\CORE\KeyValue;
use NDISmate\CORE\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 

/*

I'm hoping this short cut will get you to the appropriate google cloud management page faster in future.
https://console.cloud.google.com/apis/credentials/oauthclient/279431134437-0bdbhr738jdhad28s4u92jj095da24lf.apps.googleusercontent.com?project=bpmb-sales&supportedpurview=project

    Store RefreshToken in a database key/value store for the app `keyvalue`


    google_refresh_token
    google_drive_staff_folder
    google_drive_client_folder

    App owner connects their google drive account to the app
    App owner specifies the folders they will use to store files

    now users can upload


*/


class Drive{

    var $service;
    var $client;
    var $token;


    public function __construct(){
        
        $oauth_credentials =BASE_PATH.'/oauth-credentials.json';

        $redirect_uri = API_URL.'/Google';

        $this->client = new \Google\Client();
        $this->client->setAuthConfig($oauth_credentials);
        $this->client->setRedirectUri($redirect_uri);
        $this->client->addScope("https://www.googleapis.com/auth/drive");
        ## these two lines is important to get refresh token from google api
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force'); # this line is important when you revoke permission from your app, it will prompt google approval dialogue box forcefully to user to grant offline access
        
        
    }


    function connect(){
        return $this->client->createAuthUrl();
    }

    function checkConnected(){
        $refresh_token = KeyValue::get("google_drive_refresh_token");
        if(!empty($refresh_token)) return true;
        return false;
    }
 

    function authorise($data){
        
        // print($data['code']);
        $this->token = $this->client->fetchAccessTokenWithAuthCode($data['code']);
        // print_r($this->token);
        $this->client->setAccessToken($this->token);
        $refreshToken = $this->client->getRefreshToken();
        KeyValue::set("google_drive_refresh_token",$refreshToken);
    }


    function revoke(){
        $refresh_token = KeyValue::get("google_drive_refresh_token");
        $token = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
        $this->client->revokeToken($token);
        KeyValue::delete("google_drive_refresh_token");
    }



    function getBreadcrumbs($data){

        $folder_id = $data['folder_id'] ?? null;
        $parent_folder_id = $data['parent_folder_id'] ?? null;

        if ($folder_id == null) return "No folder id specified";

        $refresh_token = KeyValue::get("google_drive_refresh_token");
        $token = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
        $this->client->setAccessToken($token);
        $service = new \Google\Service\Drive($this->client);

        $breadcrumbs = $this->getBreadcrumbPath($folder_id, $service, $parent_folder_id);

        return array_reverse($breadcrumbs);
    }



    function getBreadcrumbPath($folderId, $service, $parent_folder_id, $breadcrumb=[])
    {

        

        $folder = $service->files->get($folderId, ['fields' => 'id, name, parents','supportsAllDrives' => true,]);
        
        $breadcrumb[] = [
            "folder_id"=>$folder->id,
            "folder_name"=>$folder->name,
            "parent_folder_id"=>$parent_folder_id
        ];
    
        if ($folderId == $parent_folder_id) return $breadcrumb;

        if (!empty($folder->parents)) {
            $parent_folder_id = $folder->parents[0];
        
            if ($folderId == $parent_folder_id) return $breadcrumb;
            // $parentFolder = $service->files->get($parent_folder_id, ['fields' => 'id, name, parents']);
            $breadcrumb = $this->getBreadcrumbPath($parent_folder_id, $service, $parent_folder_id, $breadcrumb);
        }
    
        return $breadcrumb;
    }




    function listDrives(){
        
        try {
            $refresh_token = KeyValue::get("google_drive_refresh_token");
            
            $token = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
            $this->client->setAccessToken($token);
            
            // Initialize the Google Drive service
            $service = new \Google\Service\Drive($this->client);
        
            // $optParams = array(
            //     'useDomainAdminAccess'=> false
            // );
            
            $drives = $service->drives->listDrives();
            foreach ($drives["drives"] as $drive) {
                    $return_drives[]=[
                        "name"=>$drive['name'],
                        "id"=>$drive['id']
                    ];
                }
                return $return_drives;
        
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        
        

        // $return_folders=[];
        // // return $folders["files"];
        // foreach ($folders["files"] as $folder) {
        //     $return_folders[]=[
        //         "name"=>$folder['name'],
        //         "id"=>$folder['id']
        //     ];
        // }
        // return $return_folders;
    }


    function listFolders($data){

        $folder_id = $data['folder_id'] ?? null;
        $drive_id = $data['drive_id'] ?? null;

        $refresh_token = KeyValue::get("google_drive_refresh_token");
        
        $token = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);

        $this->client->setAccessToken($token);
        $service = new \Google\Service\Drive($this->client);

        $pageToken = null;
        $return_folders = [];

        $query = "mimeType='application/vnd.google-apps.folder' and trashed = false ";
        if(!empty($folder_id)){ 
            $query .= " and '".$folder_id."' in parents";
        }else{
            if(!empty($drive_id)){ 
                $query .= " and '$drive_id' in parents";    
            }else{
                $query .= " and 'root' in parents";                
            }
        }
    
        do {
            try {
                $parameters = [
                    'driveId' => $drive_id,
                    'corpora' => 'drive',
                    'includeItemsFromAllDrives' => true,
                    'supportsAllDrives' => true,
                    'q' => $query,
                    'fields' => 'nextPageToken, files(id, name)',
                    'orderBy' => 'name',
                ];
    
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
    
                $folders = $service->files->listFiles($parameters);
    
                foreach ($folders->getFiles() as $folder) {
                    $return_folders[] = [
                        "name" => $folder->getName(),
                        "id" => $folder->getId(),
                    ];
                }
    
                $pageToken = $folders->getNextPageToken();
            } catch (Exception $e) {
                print "An error occurred: " . $e->getMessage();
                $pageToken = null;
            }
        } while ($pageToken);
    
        return $return_folders;
    }



    function createFolder($data){

        try {
            $refresh_token = KeyValue::get("google_drive_refresh_token");
        
            $token = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
            $this->client->setAccessToken($token);
        
            // Initialize the Google Drive service
            $service = new \Google_Service_Drive($this->client);
        
            // ID of the parent folder

            $parent_folder_id = $data['parent_folder_id'] ;
            $folder_name = $data['folder_name'];

            if(empty($parent_folder_id)) $parent_folder_id = 'root';
            if(empty($folder_name)) return "error: no folder name specified";


            // Metadata for the new folder
            $folderMetadata = new \Google_Service_Drive_DriveFile([
                'name' => $folder_name,  // Replace with the desired name of the new folder
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => array($parent_folder_id)
            ]);
        
            // Create the new folder
            $newFolder = $service->files->create($folderMetadata, array(
                'fields' => 'id, name',
                'supportsAllDrives' => true,
            ));
        
            // echo "Folder created with ID: ", $newFolder->getId(), " and Name: ", $newFolder->getName(), "\n";
            return $newFolder->getId();
        
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        
    }


    function listFiles($data){
        $drive_id = $data['drive_id'] ?? null;
        $folder_id = $data['folder_id'] ?? null;

        $refresh_token = KeyValue::get("google_drive_refresh_token");
        
        $token = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
        $this->client->setAccessToken($token);
        $service = new \Google\Service\Drive($this->client);
        $query = "mimeType!='application/vnd.google-apps.folder'";
        if(!empty($folder_id)) $query .= " and '".$folder_id."' in parents";
        $files = $service->files->listFiles([
            'driveId' => $drive_id,
            'corpora' => 'drive',
            'includeItemsFromAllDrives' => true,
            'supportsAllDrives' => true,
            'q' => $query,
            'fields' => 'nextPageToken, files(id,name,mimeType,description,createdTime,modifiedTime,properties,appProperties)',
            'orderBy' => 'name',
        ]);

        // Print the names of the files
        $return_files=[];
        foreach ($files["files"] as $file) {
            $return_files[]=[
                "name"=>$file['name'],
                "id"=>$file['id'],
                "mimeType"=>$file['mimeType'],
                "description"=>$file['description'],
                "createdTime"=>$file['createdTime'],
                "modifiedTime"=>$file['modifiedTime'],
                "properties"=>$file['properties'],
                "appProperties"=>$file['appProperties'],
            ];
        }
        return $return_files;
    }


    function getFile($data){

        $drive_id = $data['drive_id'] ?? null;

        $refresh_token = KeyValue::get("google_drive_refresh_token");
        $token = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
        $this->client->setAccessToken($token);
        $service = new \Google\Service\Drive($this->client);
        $file_id = $data['file_id'];

        $permission = new \Google\Service\Drive\Permission([
            "type"=>"anyone",
            "role"=>"reader",
            'supportsAllDrives' => true
        ]);

        $service->permissions->create($file_id, $permission, ['supportsAllDrives' => true]);
        $file  = $service->files->get($file_id, [
            'fields' => 'webViewLink',
            'supportsAllDrives' => true
        ]);
        
        return $file->getWebViewLink();

    }





    function deleteFile($data){
        $refresh_token = KeyValue::get("google_drive_refresh_token");
        $token = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
        $this->client->setAccessToken($token);
        $service = new \Google\Service\Drive($this->client);

        try {
            $service->files->delete($data['file_id'], array('supportsAllDrives' => true));
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    function upload($data){
        $folder_id = $data['folder_id'] ?? null;
        $file_properties = $data['file_properties'] ?? null;

        $refresh_token = KeyValue::get("google_drive_refresh_token");
        $token = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
        $this->client->setAccessToken($token);

        // Convert the base64_file data received into something usable
        list($mime_type_array,$base64_array)=explode(";",$data['base64_file']);
        $mime_type_array=explode(":",$mime_type_array);
        $mime_type = $mime_type_array[1];
        $base64_array=explode(",",$base64_array);
        $base64_file = $base64_array[1];

        $file_name = $data['name'];
        $binary = base64_decode($base64_file);

        // $file = new \Google\Service\Drive\DriveFile();
        // $file->setName($file_name);

        $file_options=[
            'name' => $file_name,
        ];

        if ($folder_id){
            $file_options['parents']=[$folder_id];    
        }

        $file_options['properties']=[
            'status' => 'draft',
            'reviewer' => 'John Doe',
        ];

        $file_options['appProperties']=[
            'test' => 'test',
        ];

        $file = new \Google\Service\Drive\DriveFile($file_options);


        $service = new \Google\Service\Drive($this->client);
        $result = $service->files->create(
            $file,
            [
                'data' => $binary,
                'mimeType' => $mime_type,
                'uploadType' => 'multipart',
                'supportsAllDrives' => true,
            ]
        );
   
        return ["file_id"=>$result->id, "file_url"=>"https://drive.google.com/open?id=".$result->id];
        
    }


}