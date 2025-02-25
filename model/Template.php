<?php

class Template {
    private const CATEGORIES = ['AUTHENTICATION', 'MARKETING', 'UTILITY'];
    private const TEMPLATE_TYPES = ['TEXT', 'IMAGE', 'LOCATION', 'VIDEO', 'DOCUMENT', 'PRODUCT', 'CATALOG'];
    
    private $db;
    private $gupshupApi;
    private $templatesTable;
    private $usersTable;

    public function __construct() {
        $this->db = new dbFunctions();
        $this->gupshupApi = new GupshupAPI();
        $this->templatesTable = 'templates';
        $this->usersTable = 'users';
    }

    public function createTemplate(array $templateData, string $email, ?string $appId = null): array {
        try {

            $email =  $this->db->escape($email);
            $user = $this->db->find($this->usersTable, "email = '$email'");
            if (!$user) {
                exit(badRequest(400, 'User not found'));
            }

            $appId = $appId ?? $this->gupshupApi->getCurrentAppId();
            if (empty($appId)) {
                exit(badRequest(400, 'Invalid app ID'));
            }

            $mappedData = $this->normalizeTemplateData($templateData, $appId);

            $validationErrors = $this->validateTemplate($mappedData);
            if (!empty($validationErrors)) {
                exit(validationError(401,$validationErrors));
            }

            $response = $this->gupshupApi->createTemplate($appId, $mappedData);

            if ($response['status'] === 'success') {
                $dbData = $this->prepareDbData($response, $mappedData, $user['id']);
                $this->db->insert($this->templatesTable, $dbData);
                return array_merge($response, ['local_data' => $dbData]);
            }

            exit(badRequest(500, 'Failed to create template on Gupshup'));

        } catch (Exception $e) {
            exit(badRequest("Failed to create template: " . $e->getMessage()));
        }
    }
    
    public function updateTemplate(string $templateId, $email, array $templateData, ?string $appId = null): array {
        $email =  $this->db->escape($email);
        $user = $this->db->find($this->usersTable, "email = '$email'");
        if (!$user) {
            exit(badRequest(400, 'User not found'));
        }

        try {
            if (empty($templateId)) {
                exit(badRequest(400, 'Template ID is required'));
            }

            // Verify template exists
            $existingTemplate = $this->getTemplate($templateId);
            if (!$existingTemplate) {
                exit(badRequest(404, 'Template not found'));
            }

            $appId = $appId ?? $this->gupshupApi->getCurrentAppId();
            if (empty($appId)) {
                exit(badRequest(400, 'Invalid app ID'));
            }

            // Normalize and validate template data
            $mappedData = $this->normalizeTemplateData($templateData, $appId);
            $validationErrors = $this->validateTemplate($mappedData);
            if (!empty($validationErrors)) {
                exit(validationError(401, $validationErrors));
            }

            // Update template on Gupshup
            $response = $this->gupshupApi->editTemplate($appId, $templateId, $mappedData);
            if ($response['status'] !== 'success') {
                exit(badRequest(500, 'Failed to update template on Gupshup'));
            }

            // Update local database
            $dbData = $this->prepareDbData($response, $mappedData, $existingTemplate['user_id']);
            $dbData['updated_at'] = date('Y-m-d H:i:s');
            
            $where = "template_id = '" . $this->db->escape($templateId) . "'";
            $this->db->update($this->templatesTable, $dbData, $where);

            return array_merge($response, ['local_data' => $dbData]);
        } catch (Exception $e) {
            exit(badRequest(500, "Failed to update template: " . $e->getMessage()));
        }
    }

    public function deleteTemplate(string $templateId): array {
        try {
            if (empty($templateId)) {
                exit(badRequest(400, 'Template ID is required'));
            }

            // Verify template exists
            $existingTemplate = $this->getTemplate($templateId);
            if (!$existingTemplate) {
                exit(badRequest(404, 'Template not found'));
            }

            try {
                $response = $this->gupshupApi->deleteTemplate($existingTemplate['app_id'], $existingTemplate['template_name']);
            } catch (Exception $e) {
                // Log the error but continue with local deletion
                error_log("Failed to delete template from Gupshup: " . $e->getMessage());
                exit(badRequest(404, "Failed to delete template from Gupshup: " . $e->getMessage()));
            }

            $where = "template_id = '" . $this->db->escape($templateId) . "'";
            $deleted = $this->db->delete($this->templatesTable, $where);

            if (!$deleted) {
                exit(badRequest(500, 'Failed to delete template from local database'));
            }

            return [
                'status' => 'success',
                'message' => 'Template deleted successfully',
                'template_id' => $templateId
            ];
        } catch (Exception $e) {
            exit(badRequest(500, "Failed to delete template: " . $e->getMessage()));
        }
    }

    public function getTemplates(?string $appId = null, array $filters = []): array {
        try {
            $appId = $appId ?? $this->gupshupApi->getCurrentAppId();
            if (empty($appId)) {
                exit(badRequest(400, 'Invalid app ID'));
            }

            // Fetch from Gupshup
            $gupshupTemplates = $this->gupshupApi->getTemplates($appId);
            
            // Build WHERE clause for local database
            $whereConditions = ["app_id = '" . $this->db->escape($appId) . "'"];
            
            if (!empty($filters['user_id'])) {
                $whereConditions[] = "user_id = " . (int)$filters['user_id'];
            }
            if (!empty($filters['status'])) {
                $whereConditions[] = "status = '" . $this->db->escape($filters['status']) . "'";
            }
            if (!empty($filters['category'])) {
                $whereConditions[] = "category = '" . $this->db->escape($filters['category']) . "'";
            }
            
            $where = implode(' AND ', $whereConditions);
            
            $localTemplates = $this->db->select($this->templatesTable, $where);

            if(empty($localTemplates)){
                exit(badRequest(400, "template not found"));
            }
            
            $localTemplatesMap = [];
            foreach ($localTemplates as $template) {
                $localTemplatesMap[$template['template_id']] = $template;
            }

            $mergedTemplates = [];
            foreach ($gupshupTemplates as $gupshupTemplate) {
                $templateId = $gupshupTemplate['id'];
                $localData = $localTemplatesMap[$templateId] ?? [];
                $mergedTemplates[] = array_merge(
                    $localData,
                    [
                        'template_id' => $templateId,
                        'template_name' => $gupshupTemplate['elementName'],
                        'category' => $gupshupTemplate['category'],
                        'status' => $gupshupTemplate['status'],
                        'gupshup_data' => $gupshupTemplate
                    ]
                );
            }

            return $mergedTemplates;
        } catch (Exception $e) {
            exit(badRequest(500, "Failed to fetch templates: " . $e->getMessage()));
        }
    }

    public function getTemplate(string $templateId): ?array {
        try {
            if (empty($templateId)) {
                exit(badRequest(400, 'Template ID is required'));
            }

            $where = "template_id = '" . $this->db->escape($templateId) . "'";
            $localTemplate = $this->db->find($this->templatesTable, $where);
            
            if (!$localTemplate) {
                return null;
            }
            
            return $localTemplate;

        } catch (Exception $e) {
            exit(badRequest(500, "Failed to fetch template: " . $e->getMessage()));
        }
    }

    public function updateStatus(string $templateId, string $status): array {
        try {
            if (empty($templateId)) {
                exit(badRequest(400, 'Template ID is required'));
            }

            if (empty($status)) {
                exit(badRequest(400, 'Status is required'));
            }

            $existingTemplate = $this->getTemplate($templateId);
            if (!$existingTemplate) {
                exit(badRequest(404, 'Template not found'));
            }

            $where = "template_id = '" . $this->db->escape($templateId) . "'";
            $updated = $this->db->update($this->templatesTable, [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ], $where);

            if (!$updated) {
                exit(badRequest(500, 'Failed to update template status'));
            }

            return [
                'status' => 'success',
                'message' => 'Template status updated successfully',
                'template_id' => $templateId,
                'new_status' => $status
            ];
        } catch (Exception $e) {
            exit(badRequest(500, "Failed to update template status: " . $e->getMessage()));
        }
    }

    public function getTemplatesByUser(int $userId): array {
        try {
            if (empty($userId)) {
                exit(badRequest(400, 'User ID is required'));
            }

            // Verify user exists
            $user = $this->db->find($this->usersTable, "id = " . (int)$userId);
            if (!$user) {
                exit(badRequest(404, 'User not found'));
            }

            $where = "user_id = " . (int)$userId;
            $templates = $this->db->select($this->templatesTable, $where);

            // Enrich with Gupshup data where possible
            // foreach ($templates as &$template) {
            //     try {
            //         $gupshupTemplate = $this->gupshupApi->getTemplate($template['app_id'], $template['template_id']);
            //         $template['gupshup_data'] = $gupshupTemplate;
            //     } catch (Exception $e) {
            //         // Skip if Gupshup data can't be fetched
            //         $template['gupshup_data'] = null;
            //     }
            // }

            return $templates;
        } catch (Exception $e) {
            exit(badRequest(500, "Failed to fetch user templates: " . $e->getMessage()));
        }
    }

    private function normalizeTemplateData(array $rawData, string $appId): array {
        $rawData['elementname'] = str_replace(" ", "_", strtolower($rawData['elementname']));
        return [
            'elementName' => $rawData['elementname'] ?? null,
            'languageCode' => $rawData['languagecode'] ?? 'en',
            'content' => $rawData['content'] ?? null,
            'footer' => $rawData['footer'] ?? null,
            'category' => $rawData['category'] ?? 'MARKETING',
            'templateType' => $rawData['templatetype'] ?? 'TEXT',
            'vertical' => $rawData['vertical'] ?? null,
            'example' => $rawData['example'] ?? null,
            'enableSample' => $rawData['enableSample'] ?? 'true',
            'allowTemplateCategoryChange' => $rawData['allowTemplateCategoryChange'] ?? 'false',
            'appId' => $appId
        ];
    }

    private function prepareDbData(array $response, array $mappedData, int $userId): array {
        return [
            'template_id' => $response['template']['id'],
            'template_type' => $response['template']['templateType'],
            'template_name' => $mappedData['elementName'],
            'category' => $mappedData['category'],
            'content' => $mappedData['content'],
            'language' => $mappedData['languageCode'],
            'status' => $response['status'] ?? 'PENDING',
            'user_id' => $userId,
            'app_id' => $mappedData['appId'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    public function validateTemplate(array $data): array {
        $errors = [];
        
        $requiredFields = ['elementName', 'category', 'templateType', 'vertical', 'content', 'example', 'enableSample'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[] = "Missing required field: {$field}";
            }
        }
        
        if (isset($data['category']) && !in_array($data['category'], self::CATEGORIES)) {
            $errors[] = "Invalid category. Must be one of: " . implode(', ', self::CATEGORIES);
        }
        
        if (isset($data['templateType']) && !in_array($data['templateType'], self::TEMPLATE_TYPES)) {
            $errors[] = "Invalid template type. Must be one of: " . implode(', ', self::TEMPLATE_TYPES);
        }
        
        if (isset($data['vertical']) && strlen($data['vertical']) > 180) {
            $errors[] = "Vertical exceeds 180 character limit";
        }
        
        if (isset($data['content']) && strlen($data['content']) > 1028) {
            $errors[] = "Content exceeds 1028 character limit";
        }
        
        if (isset($data['header'])) {
            if ($data['templateType'] === 'TEXT' && strlen($data['header']) > 60) {
                $errors[] = "Header exceeds 60 character limit for TEXT template type";
            }
            if (isset($data['category']) && $data['category'] === 'AUTHENTICATION') {
                $errors[] = "Header not applicable for AUTHENTICATION category";
            }
            if (isset($data['templateType']) && $data['templateType'] === 'CATALOG') {
                $errors[] = "Header not applicable for CATALOG template type";
            }
        }
        
        if (isset($data['footer'])) {
            if (strlen($data['footer']) > 60) {
                $errors[] = "Footer exceeds 60 character limit";
            }
            if (isset($data['category']) && $data['category'] === 'AUTHENTICATION') {
                if (!isset($data['codeExpirationMinutes'])) {
                    $errors[] = "Footer for AUTHENTICATION category must be set based on code_expiration_minutes";
                }
            }
        }
        
        // Authentication category specific validations
        if (isset($data['category']) && $data['category'] === 'AUTHENTICATION') {
            if (isset($data['content']) && !str_starts_with($data['content'], '{{1}} is your verification code')) {
                $errors[] = "Authentication template must start with '{{1}} is your verification code'";
            }
            
            if (isset($data['codeExpirationMinutes'])) {
                $minutes = intval($data['codeExpirationMinutes']);
                if ($minutes < 1 || $minutes > 90) {
                    $errors[] = "Code expiration minutes must be between 1 and 90";
                }
            }
            
            if (isset($data['message_send_ttl_seconds'])) {
                $ttl = intval($data['message_send_ttl_seconds']);
                if ($ttl < 30 || $ttl > 900) {
                    $errors[] = "Message TTL for Authentication must be between 30 and 900 seconds";
                }
            }
        }
        
        // TTL validations for other categories
        if (isset($data['message_send_ttl_seconds']) && isset($data['category'])) {
            $ttl = intval($data['message_send_ttl_seconds']);
            switch ($data['category']) {
                case 'UTILITY':
                    if ($ttl < 30 || $ttl > 43200) {
                        $errors[] = "Message TTL for Utility must be between 30 and 43200 seconds";
                    }
                    break;
                case 'MARKETING':
                    if ($ttl < 43200 || $ttl > 2592000) {
                        $errors[] = "Message TTL for Marketing must be between 43200 and 2592000 seconds";
                    }
                    break;
            }
        }
        
        // Boolean validations
        // $booleanFields = ['enableSample', 'allowTemplateCategoryChange', 'addSecurityRecommendation'];
        // foreach ($booleanFields as $field) {
        //     if (isset($data[$field]) && !is_bool($data[$field])) {
        //         $errors[] = "{$field} must be a boolean value";
        //     }
        // }
        
        return $errors;
    }

}