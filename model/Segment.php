<?php

class Segment {

    private $db;
    private $segmentsTable = 'segments';
    private $usersTable = 'users';

    public function __construct() {
        $this->db = new dbFunctions();
    }

    public function create(array $segmentData, $email){

        if($this->validateSegment($segmentData)){
            $user = $this->db->find($this->usersTable, "email = '$email'");
            if (!$user) {
                return ['status' => false, 'message' => 'User not found'];
            }
    
            $segmentData['user_id'] = $user['id'];
            $result = $this->db->insert($this->segmentsTable, $segmentData);

            if(!is_null($result)){
                return [
                    "status" => true,
                    "code" => 200,
                    "message" => "Segment created successfully",
                    "data" => $segmentData
                ];
            }
            else{
                return [
                    "status" => false,
                    "code" => 500,
                    "message" => "Segment creation failed",
                    "data" => []
                ];
            }
        }
    }
    
    public function edit(array $segmentData, $email, $segment_id){

        if($this->validateSegment($segmentData)){
            $user = $this->db->find($this->usersTable, "email = '$email'");
            if (!$user) {
                return ['status' => false, 'message' => 'User not found'];
            }
    
            $segmentData['user_id'] = $user['id'];
            $result = $this->db->update($this->segmentsTable, $segmentData, "id = '$segment_id' AND  user_id = '{$segmentData['user_id']}'");

            if(!is_null($result)){
                return [
                    "status" => true,
                    "code" => 200,
                    "message" => "Segment edited successfully",
                    "data" => $segmentData
                ];
            }
            else{
                return [
                    "status" => false,
                    "code" => 500,
                    "message" => "Segment edition failed",
                    "data" => []
                ];
            }
        }
    }

    public function delete($segmentId, $email){

        $user = $this->db->find($this->usersTable, "email = '$email'");
        if (!$user) {
            return ['status' => false, 'message' => 'User not found'];
        }
    
        $segmentData['user_id'] = $user['id'];
        $where = "id = $segmentId AND user_id = {$segmentData['user_id']} ";
        $result = $this->db->delete($this->segmentsTable, $where);

        if($result){
            return [
                "status" => true,
                "code" => 200,
                "message" => "Segment deleted successfully",
                "data" => $segmentData
            ];
        }
        else{
            return [
                "status" => false,
                "code" => 500,
                "message" => "Segment deletion failed",
                "data" => []
            ];
        }
    }

    public function getSegments($request){
        
        $email = isset($request['email']) && $request['email'] !== ""  ?  $request['email'] :  "";
        $name = isset($request['name']) && $request['name'] !== ""  ?  $request['name'] :  "";
        $startDate = isset($request['start_date']) && $request['start_date'] !== "" ?  $request['start_date'] :  "";
        $endDate = isset($request['end_date']) && $request['end_date'] !== "" ?  $request['end_date'] :  date("Y-m-d H:i:s");
        
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if(!$user){
            return ['status' => false, 'message' => 'User not found'];
        }
            if(!empty($startDate)){
                $segments = $this->db->select($this->segmentsTable, "*", "created_at BETWEEN $startDate AND $endDate AND user_id = '{$user['id']}'", 'id ASC');
                if(!empty($segments)){
                    return [
                        'status' => true,
                        'code' => 200,
                        'message' => "Segments fetched successfully",
                        'data' => $segments,
                    ];
                }else{
                    return [
                        'status' => false,
                        'code' => 400,
                        'message' => "Segments not found",
                        'data' => $segments,
                    ];
                }
            }
            elseif(!empty($name)){
                $segments = $this->db->select($this->segmentsTable, "*", "name = '$name' AND user_id = '{$user['id']}'", 'id ASC');
                if(!empty($segments)){
                    return [
                        'status' => true,
                        'code' => 200,
                        'message' => "Segments fetched successfully",
                        'data' => $segments,
                    ];
                }else{
                    return [
                        'status' => false,
                        'code' => 400,
                        'message' => "Segments not found",
                        'data' => $segments,
                    ];
                }
            }
            else{
                $segments = $this->db->select($this->segmentsTable, "*", "user_id = '{$user['id']}'", 'id ASC');
                if(!empty($segments)){
                    return [
                        'status' => true,
                        'code' => 200,
                        'message' => "Segments fetched successfully",
                        'data' => $segments,
                    ];
                }else{
                    return [
                        'status' => false,
                        'code' => 400,
                        'message' => "Segments not found",
                        'data' => $segments,
                    ];
                }
            }
    }
    
    public function linkContactsToSegment($segmentId,  $contactIds) {
        if (is_array($contactIds)) {
            $contactIdsArray = [];
            foreach ($contactIds as $contactId) {
                $contactIdsArray[] = $contactId['id']; 
            }
        
            $contactIdsJson = json_encode($contactIdsArray);
            $this->db->update($this->segmentsTable, ['contact_ids' => $contactIdsJson], "id = '$segmentId'");
        }
        else{
            $contactIdsJson = json_encode([$contactIds]);
            $this->db->update($this->segmentsTable, ['contact_ids' => $contactIdsJson], "id = '$segmentId'");
        }
    }

    public function getSegmentContacts($segmentId) {        
        return $this->db->select('contacts c', '*', "EXISTS (SELECT 1 FROM {$this->segmentsTable} s  WHERE s.contact_id = c.id AND s.id = $segmentId)");
    }

    public function removeContactsFromSegment($segmentId, array $contactIds) {
        $contactIdsStr = implode(',', $contactIds);
        $where = "id = $segmentId AND contact_id IN ($contactIdsStr)";
        return $this->db->delete($this->segmentsTable, $where);
    }

    private function validateSegment(array &$contact) {
        $errors = [];
        
        if (empty($contact['name']) || $contact['name'] == "") {
            $errors[] = 'name is required';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return true;
    }
}
