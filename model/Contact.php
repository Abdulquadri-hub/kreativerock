<?php

class Contact {
    
    private $db;
    private $segment;
    private $contactsTable = 'contacts';
    private $usersTable = 'users';
    private $segmentsTable = 'segments';

    public function __construct() {
        $this->db = new dbFunctions();
        $this->segment = new Segment();
    }
    
    public function getContacts($request){
        
        $email = isset($request['email']) && $request['email'] !== ""  ?  $request['email'] :  "";
        $type = isset($request['type']) && $request['type'] !== ""  ?  $request['type'] :  "";
        $contactsid = isset($request['contactsid']) && $request['contactsid'] !== ""  ?  $request['contactsid'] :  "";
        $startDate = isset($request['start_date']) && $request['start_date'] !== "" ?  $request['start_date'] :  "";
        $endDate = isset($request['end_date']) && $request['end_date'] !== "" ?  $request['end_date'] :  date("Y-m-d H:i:s");
        
        $importer = $this->db->find($this->usersTable, "email = '$email'");
        if(!$importer){
            return ['status' => false, 'message' => 'User not found'];
        }
        
        if(!empty($contactsid)){
            if(!empty($startDate)){
                $contacts = $this->db->select($this->contactsTable, "*", "contactsid = '$contactsid' AND created_at BETWEEN $startDate  AND $endDate AND user_id = '{$importer['id']}'", 'contact_id ASC');
                if(!empty($contacts)){
                    return $contacts;
                }else{
                    return null;
                }
            }
            elseif(!empty($type)){
                $type = strtoupper($type);
                $contacts = $this->db->select($this->contactsTable, "*", "contactsid = '$contactsid' AND type = '$type' AND user_id = '{$importer['id']}'", 'contact_id ASC');
                if(!empty($contacts)){
                    return $contacts;
                }else{
                    return null;
                }
            }
            else{
                $contacts = $this->db->select($this->contactsTable, "*", "contactsid = '$contactsid' AND user_id = '{$importer['id']}'", 'contact_id ASC');
                if(!empty($contacts)){
                    return $contacts;
                }else{
                    return null;
                }
            }
        }
        else{
            if(!empty($startDate)){
                $contacts = $this->db->select($this->contactsTable, "*", "created_at BETWEEN $startDate AND $endDate AND user_id = '{$importer['id']}'", 'contact_id ASC');
                if(!empty($contacts)){
                    return $contacts;
                }else{
                    return null;
                }
            }
            elseif(!empty($type)){
                $contacts = $this->db->select($this->contactsTable, "*", "type = '$type' AND user_id = '{$importer['id']}'", 'contact_id ASC');
                if(!empty($contacts)){
                    return $contacts;
                }else{
                    return null;
                }
            }
            else{
                $contacts = $this->db->select($this->contactsTable, "*", "user_id = '{$importer['id']}'", 'contact_id ASC');
                if(!empty($contacts)){
                    return $contacts;
                }else{
                    return null;
                }
            }
        }
    }
    
    public function importFromText(array $jsonData, $email) {
        
        $importer = $this->db->find($this->usersTable, "email = '$email'");
        if(!$importer){
            return ['status' => false, 'message' => 'User not found'];
        }
            
        if(!is_array($jsonData['contacts'])){
            return ['status' => 'false', 'message' => 'Invalid request structure: contacts array is required Or No contacts provided for import'];
        }
    
        
        return $this->processImportFromText($jsonData, $importer);
    }

    public function importFromFile(array $jsonData, $email): array {
        
        $importer = $this->db->find($this->usersTable, "email = '$email'");

        if(!$importer){
            return ['status' => false, 'message' => 'User not found'];
        }
        

        if(!is_array($jsonData['contacts'])){
            return ['status' => 'false', 'message' => 'Invalid request structure: contacts array is required Or No contacts provided for import'];
        }
    
        
       return $this->processImportFromFile($jsonData, $importer);
        
    }
    
    public function createFromData(array $contactData, $email){
        
        $importer = $this->db->find($this->usersTable, "email = '$email'");
        if(!$importer){
            return ['status' => false, 'message' => 'User not found'];
        }
        
        $contactsId = $this->generateContactsId(count($contactData));
        
        $data = $this->validateContact($contactData);
        $data['user_id'] = $importer['id'];
        $data['type'] = 'FORM';
        $data['contactsid'] = $contactsId; 
                
        $contactId = $this->insertContact($data); 
        
        // if(!is_null($contactData['segment_id'])){
        //     $this->segment->linkContactsToSegment($contactData['segment_id'], $contactId);
        // }
        
        return [
            "status" => true,
            "code" => 200,
            "message" => "Contact created successfully",
            "data" => $data
        ];
    }
    
    private function processImportFromFile($jsonData, $importer): array {
        
        if (!$this->validateHeaders($jsonData['headers'])) {
            return ['status' => 'failed', 'message' => 'Headers is required', 'headers' => $jsonData['headers']];
        }
    

        $contactsId = $this->generateContactsId(count($jsonData['contacts']));
    
        $importStats = [
            'total' => count($jsonData['contacts']),
            'imported' => 0,
            'failed' => 0,
            'errors' => [],
            'segment_success_count' => 0
        ];
    
            foreach ($jsonData['contacts'] as $index => $contact) {
                try {
                    $data = $this->validateContact($contact);
                    $data['user_id'] = $importer['id'];
                    $data['type'] = "FILE";
                    $data['contactsid'] = $contactsId; 
                    $data['segment_id'] = $jsonData['segment_id']; 
                    
                    $result = $this->insertContact($data);
                    if($result){

                        // $contactIds = $this->db->select($this->contactsTable, "id", "contactsId = '$contactsId'");
    
                        // if(!is_null($jsonData['segment_id'])){
                        //         $this->segment->linkContactsToSegment($jsonData['segment_id'], $contactIds);
                        //         $importStats['segment_success_count']++;
                        // }

                        $importStats['status'] = true;
                        $importStats['message'] = "Contacts imported successfully";
                        $importStats['imported']++;
                    }
                    
                } catch (ValidationException $e) {
                    $importStats['failed']++;
                    $importStats['errors'][] = [
                        'index' => $index,
                        'email' => $contact['email'] ?? 'N/A',
                        'reason' => $e->getErrors()
                    ];
                }
            }

            return $importStats;
    }
    
    private function processImportFromText($jsonData, $importer): array {
        
        if (!$this->validateHeaders($jsonData['headers'])) {
            return ['status' => 'failed', 'message' => 'Headers is required', 'headers' => $jsonData['headers']];
        }

        $contactsId = $this->generateContactsId(count($jsonData['contacts']));
    
        $importStats = [
            'total' => count($jsonData['contacts']),
            'imported' => 0,
            'failed' => 0,
            'errors' => [],
            'segment_success_count' => 0
        ];
    
            foreach ($jsonData['contacts'] as $index => $contact) {
                try {
                    $data = $this->validateContact($contact);
                    $data['user_id'] = $importer['id'];
                    $data['type'] = "TEXT";
                    $data['contactsid'] = $contactsId;  
                    $data['segment_id'] = $jsonData['segment_id'];  
                    
                    $result = $this->insertContact($data);
                    if($result){

                        // $contactIds = $this->db->select($this->contactsTable, "id", "contactsId = '$contactsId'");
    
                        // if(!is_null($jsonData['segment_id'])){
                        //         $this->segment->linkContactsToSegment($jsonData['segment_id'], $contactIds);
                        //         $importStats['segment_success_count']++;
                        // }

                        $importStats['status'] = true;
                        $importStats['message'] = "Contacts imported successfully";
                        $importStats['imported']++;
                    }
                    
                } catch (ValidationException $e) {
                    $importStats['failed']++;
                    $importStats['errors'][] = [
                        'index' => $index,
                        'email' => $contact['email'] ?? 'N/A',
                        'reason' => $e->getErrors()
                    ];
                }
            }

            return $importStats;
    }
    
    private function validateContact(array &$contact) {
        $errors = [];

        if (empty($contact['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($contact['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (!$this->isValidNumber($contact['landline_number'])) {
            $errors[] = 'Invalid landline number format';
        }
        
        if (empty($contact['sms']) && !preg_match('/^\+?[\d\s-]{10,}$/', $contact['sms'])) {
            $errors[] = 'Invalid sms number format';
        }
        
        if (empty($contact['whatsapp']) && !preg_match('/^\+?[\d\s-]{10,}$/', $contact['whatsapp'])) {
            $errors[] = 'Invalid whatsapp number format';
        }
        
        if (empty($contact['firstname']) || $contact['firstname'] == "") {
            $errors[] = 'firstname is required';
        }
        
        if (empty($contact['lastname']) || $contact['lastname'] == "") {
            $errors[] = 'lastname is required';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $data['contact_id'] = !empty($contact['contact_id']) ? trim($contact['contact_id']) : null;
        $data['landline'] = !empty($contact['landline_number']) ? trim($contact['landline_number']) : null;
        $data['email'] = strtolower(trim($contact['email']));
        $data['firstname'] = !empty($contact['firstname']) ? trim($contact['firstname']) : null;
        $data['lastname'] = !empty($contact['lastname']) ? trim($contact['lastname']) : null;
        $data['sms'] = !empty($contact['sms']) ? trim($contact['sms']) : null;
        $data['whatsapp'] = !empty($contact['whatsapp']) ? trim($contact['whatsapp']) : null;
    
        return $data;
    }
    
    private function isValidNumber(?string $number): bool {
        if (empty($number)) {
            return false;
        }
        $cleanNumber = preg_replace('/[\s\-\(\)\.]/', '', $number);
        return preg_match('/^\+?\d{7,15}$/', $cleanNumber) === 1;
    }
    
    private function validateHeaders(array $headers) {
        $requiredHeaders = ['CONTACT ID','EMAIL','FIRSTNAME','LASTNAME','LANDLINE_NUMBER','SMS','WHATSAPP', 'INTERESTS'];
        return $requiredHeaders;
        foreach ($requiredHeaders as $required) {
            if (!in_array(strtolower($required), array_map('strtolower', $headers))) {
                return false;
            }
        }
    }
    
    private function insertContact(array $contactData){
       return $this->db->insert($this->contactsTable, $contactData);
    }
    
    private function updateContact(array $data, string $contactsId): bool {
        try {
            return $this->db->update($this->contactsTable, $data, "contactsid = '$contactsId'");
        } catch (Exception $e) {
            //  throw new ValidationException("Failed to update contact: " . $e->getMessage());
        }
    }
    
    private function generateContactsId(int $numberOfContacts): string {
        $date = date('Y-m-d');  
        $time = date('H:i:s'); 
        
        return "{$date}|{$time}|{$numberOfContacts}";
    }
    
}


class UploadException extends Exception {}

class ValidationException extends Exception {
    private  $errors;

    public function __construct(array $errors) {
        $this->errors = $errors;
        parent::__construct(implode('; ', $errors));
    }

    public function getErrors(): array {
        return $this->errors;
    }
}


