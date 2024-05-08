<?php

class Nobox {
    private $token;
    private $result;
    private $baseUrl = "https://id.nobox.ai/";
    public function __construct($token) {
        $this->token = $token;
        $this->result = new stdClass();
        $this->result->Code = 0;
        $this->result->IsError = true;
        $this->result->Data = null;
        $this->result->Error = "";
    }
    
    public function generateToken($username, $password) {
        // API endpoint
        $url = $this->baseUrl . "AccountAPI/GenerateToken";
        
        // Request data
        $data = array(
            "username" => $username,
            "password" => $password
        );
        
        $payload = json_encode($data);
        // Initialize cURL session
        $curl = curl_init($url);
        
        // Set the request method to POST
        curl_setopt($curl, CURLOPT_POST, true);
        
        // Set the request data
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        
        // Return the response instead of outputting it directly
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        
        // Execute the request
        $response = curl_exec($curl);
        
        // Get the response status code
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        // Close the cURL session
        $error = curl_error($curl);
        curl_close($curl);
        
        // Check if the request was successful (status code 200)
        $this->result->Code = $httpStatusCode;
        if ($httpStatusCode === 200) {
            // Process the response data
            $responseData = json_decode($response);
            $token = $responseData->token;
            $this->result->IsError = false;
            $this->result->Data = $token;
            return $this->result;
        } else {
            $this->result->IsError = true;
            $this->result->Error = $error;
            return $this->result;
        }
    }
    public function uploadFile($file) {
        // API endpoint
        $fileName = $file['name'];
        $fileType = $file['type'];
        $fileSize = $file['size'];
        $fileTmpPath = $file['tmp_name'];
        $url = $this->baseUrl . "Inbox/UploadFile/UploadFile";

        // Initialize cURL session
        $curl = curl_init($url);

        // Set the request method to POST
        curl_setopt($curl, CURLOPT_POST, true);

        // Set the file data
        $fileData = array(
            'file' => new CURLFile($fileTmpPath, $fileType, $fileName)
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fileData);

        // Set the authorization token
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->token
        ));
        
        // Return the response instead of outputting it directly
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($curl);

        // Get the response status code
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the cURL session
        $error = curl_error($curl);
        curl_close($curl);

        $this->result->Code = $httpStatusCode;
        // Check if the request was successful (status code 200)
        if ($httpStatusCode === 200) {
            // Process the response data
            $responseData = json_decode($response);
            if($responseData->Error==null){
                $this->result->IsError = false;
                $this->result->Data = $responseData->Data;
                return $this->result;
            }else{
                $this->result->IsError = true;
                $this->result->Error = $responseData->Error;
                return $this->result;
            }
        } else {
            $this->result->IsError = true;
            $this->result->Error = $error;
            return $this->result;
        }
    }

    public function uploadBase64($filename,$mimetype,$base64) {
        // API endpoint
        $url = $this->baseUrl . "Inbox/UploadFile/UploadBase64";

        // Request data
        $data = array(
            'filename'=>$filename,
            'mimetype'=>$mimetype,
            'data' => $base64
        );

        // Initialize cURL session
        $curl = curl_init($url);

        // Set the request method to POST
        curl_setopt($curl, CURLOPT_POST, true);

        // Set the request data
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        // Set the authorization token
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->token,
            'Content-Type: application/json'
        ));

        // Return the response instead of outputting it directly
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($curl);

        // Get the response status code
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the cURL session
        $error = curl_error($curl);
        curl_close($curl);

        $this->result->Code = $httpStatusCode;
        // Check if the request was successful (status code 200)
        if ($httpStatusCode === 200) {
            // Process the response data
            $responseData = json_decode($response);
            if($responseData->Error==null){
                $this->result->IsError = false;
                $this->result->Data = $responseData->Data;
                return $this->result;
            }else{
                $this->result->IsError = true;
                $this->result->Error = $responseData->Error;
                return $this->result;
            }
        } else {
            $this->result->IsError = true;
            $this->result->Error = $error;
            return $this->result;
        }
    }
    public function sendInboxMessage($linkId, $channelId, $accountIds, $bodyType, $body, $attachment) {
        // API endpoint
        $url = $this->baseUrl . "Inbox/Send";
        
        // Request parameters
        $data = array(
            "LinkId" => $linkId,
            "ChannelId" => $channelId,
            "AccountIds" => $accountIds,
            "BodyType" => $bodyType,
            "Body" => $body,
            "Attachment" => $attachment
        );
        
        // Initialize cURL session
        $curl = curl_init($url);
        
        // Set the request method to POST
        curl_setopt($curl, CURLOPT_POST, true);
        
        // Set the request data
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        // Set the authorization token
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->token,
            'Content-Type: application/json'
        ));
        
        // Return the response instead of outputting it directly
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the request
        $response = curl_exec($curl);
        
        // Get the response status code
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        // Close the cURL session
        $error = curl_error($curl);
        curl_close($curl);
        
        $this->result->Code = $httpStatusCode;
        // Check if the request was successful (status code 200)
        if ($httpStatusCode === 200) {
            // Process the response data
            $responseData = json_decode($response);
            if($responseData->Error!=null){
                $this->result->IsError = true;
                $this->result->Data = $responseData->Error;
            }else{
                $this->result->IsError = false;
                $this->result->Data = $responseData->Data;
            }
            return $this->result;
        } else {
            $this->result->IsError = true;
            $this->result->Error = $error;
            return $this->result;
        }
    }
    public function sendInboxMessageExt($extId, $channelId, $accountIds, $bodyType, $body, $attachment) {
        // API endpoint
        $url = $this->baseUrl . "Inbox/Send";
        
        // Request parameters
        $data = array(
            "ExtId" => $extId,
            "ChannelId" => $channelId,
            "AccountIds" => $accountIds,
            "BodyType" => $bodyType,
            "Body" => $body,
            "Attachment" => $attachment
        );
        
        // Initialize cURL session
        $curl = curl_init($url);
        
        // Set the request method to POST
        curl_setopt($curl, CURLOPT_POST, true);
        
        // Set the request data
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        // Set the authorization token
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->token,
            'Content-Type: application/json'
        ));
        
        // Return the response instead of outputting it directly
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the request
        $response = curl_exec($curl);
        
        // Get the response status code
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        // Close the cURL session
        $error = curl_error($curl);
        curl_close($curl);
        
        $this->result->Code = $httpStatusCode;
        // Check if the request was successful (status code 200)
        if ($httpStatusCode === 200) {
            // Process the response data
            $responseData = json_decode($response);
            if($responseData->Error!=null){
                $this->result->IsError = true;
                $this->result->Data = $responseData->Error;
            }else{
                $this->result->IsError = false;
                $this->result->Data = $responseData->Data;
            }
            return $this->result;
        } else {
            $this->result->IsError = true;
            $this->result->Error = $error;
            return $this->result;
        }
    }
    
    function getChannelList() {
        // API endpoint
        $url = $this->baseUrl . "Services/Master/Channel/List";
        
        // Request data
        $data = array(
            'ColumnSelection' => 1,
            'IncludeColumns' => array('Id', 'Nm')
          );
        // Initialize cURL session
        $curl = curl_init($url);
        
        // Set the request method to POST
        curl_setopt($curl, CURLOPT_POST, true);
        
        // Set the request data
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        
        // Set the authorization token
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ". $this->token,
            'Content-Type: application/json'
        ));
        
        // Return the response instead of outputting it directly
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the request
        $response = curl_exec($curl);
        
        // Get the response status code
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        // Close the cURL session
        $error = curl_error($curl);
        curl_close($curl);
        
        $this->result->Code = $httpStatusCode;
        // Check if the request was successful (status code 200)
        if ($httpStatusCode === 200) {
            // Process the response data
            $responseData = json_decode($response);
            $this->result->IsError = false;
            $this->result->Data = $responseData->Entities;
            return $this->result;
        } else {
            $this->result->IsError = true;
            $this->result->Error = $error;
            return $this->result;
        }
    }
    
    function getAccountList() {
        $url = $this->baseUrl . "Services/Nobox/Account/List";
  
        $data = array(
        'ColumnSelection' => 1,
        'IncludeColumns' => array('Id', 'Name','Channel')
        );
    
        $headers = array(
        'Authorization: Bearer ' . $this->token,
        'Content-Type: application/json'
        );
    
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($curl);
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        $this->result->Code = $httpStatusCode;
        if ($httpStatusCode === 200) {
            $responseData = json_decode($response);
            $this->result->IsError = false;
            $this->result->Data = $responseData->Entities;
            return $this->result;
        } else {
            $this->result->IsError = true; 
            $this->result->Error = $error;
            return $this->result;
        }
    }
    
    function getContactList() {
        $url = $this->baseUrl . "Services/Nobox/Contact/List";
  
        $data = array(
        'ColumnSelection' => 1,
        'IncludeColumns' => array('Id', 'Name')
        );
    
        $headers = array(
        'Authorization: Bearer ' . $this->token,
        'Content-Type: application/json'
        );
    
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($curl);
        $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        $this->result->Code = $httpStatusCode;
        if ($httpStatusCode === 200) {
            $responseData = json_decode($response);
            $this->result->IsError = false;
            $this->result->Data = $responseData->Entities;
            return $this->result;
        } else {
            $this->result->IsError = true; 
            $this->result->Error = $error;
            return $this->result;
        }
    }
    
    function fetchLinkList($channelId = null, $contactId = null) {
        try {
            $request = [
                'IncludeColumns' => ["Id", "Name", "IdExt"],
                'ColumnSelection' => 1
            ];
            
            if ($channelId != null || $contactId != null) {
                $request['EqualityFilter'] = [];
                
                if ($contactId != null) {
                    $request['EqualityFilter']['CtId'] = $contactId;
                }
                
                if ($channelId != null) {
                    $request['EqualityFilter']['ChId'] = $channelId;
                }
            }
            
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer '. $this->token // Replace YOUR_ACCESS_TOKEN with the actual access token
            ];
            
            $ch = curl_init($this->baseUrl . 'Services/Chat/Chatlinkcontacts/List'); // Replace YOUR_BASE_URL with the actual base URL
            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($httpCode != 200) {
                return [
                    'IsErrror' => true,
                    'Code' => $httpCode,
                    'Data' => null,
                    'Error' => $response
                ];
            }
            
            $data = json_decode($response, true);
            
            if ($data['Error'] == null) {
                return [
                    'IsErrror' => false,
                    'Code' => $httpCode,
                    'Data' => $data['Entities'],
                    'Error' => null
                ];
            } else {
                return [
                    'IsErrror' => false,
                    'Code' => $httpCode,
                    'Data' => $data['Entities'],
                    'Error' => $data['Error']
                ];
            }
        } catch (Exception $error) {
            error_log('Error: ' . $error->getMessage());
            return [
                'IsErrror' => true,
                'Code' => 500,
                'Data' => null,
                'Error' => $error->getMessage()
            ];
        }
    }
    
    function getTypeList() {
        
        $bodyTypes = array(
            array("text" => 'Text', "value" => 1),
            array("text" => 'Audio', "value" => 2),
            array("text" => 'Image', "value" => 3),
            array("text" => 'Sticker', "value" => 7),
            array("text" => 'Video', "value" => 4),
            array("text" => 'File', "value" => 5),
            array("text" => 'Location', "value" => 9),
            array("text" => 'Order', "value" => 10),
            array("text" => 'Product', "value" => 11),
            array("text" => 'VCARD', "value" => 12),
            array("text" => 'VCARD_MULTI', "value" => 13)
        );

        $this->result->Code = 200;
        $this->result->IsError = false;
        $this->result->Data = $bodyTypes;
        return $this->result;
    }
}

// Example usage
// $token = "your_token";
// $nobox = new Nobox($token);

// Send Inbox Message
// $linkId = 123;
// $channelId = 456;
// $accountIds = "abc,def,ghi";
// $bodyType = 1;
// $body = "Hello, world!";
// $attachment = "path/to/file";
// $inboxResponse = $nobox->sendInboxMessage($linkId, $channelId, $accountIds, $bodyType, $body, $attachment);
// echo "Send Inbox Message Response: " . $inboxResponse . "\n";

// Get Channel List
// $channelListResponse = $nobox->getChannelList();
// echo "Channel List Response: " . $channelListResponse . "\n";

// Generate Token
// $username = "your_username";
// $password = "your_password";
// $tokenResponse = $nobox->generateToken($username, $password);
// echo "Token Response: " . $tokenResponse . "\n";
