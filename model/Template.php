<?php

class Template {
    private $db;
    private $gupshupApi;
    private $templatesTable = 'templates';
    private $usersTable = 'users';

    public function __construct() {
        $this->db = new dbFunctions();
        $this->gupshupApi =  new GupshupAPI();
    }

    /**
     * Create a new template and store in both Gupshup and local database
     */
    public function createTemplate(array $templateData, string $email, ?string $appId = null): array {
        try {
            $appId = $appId ?? $this->gupshupApi->getCurrentAppId();
    
            $user = $this->db->find($this->usersTable, "email = '$email'");
            if (!$user) {
                return ['message' => 'User not found'];
            }

            if(empty($templateData['sample_media_path'])){
                exit(badRequest(401, "Sample media path is requred"));
            }elseif(empty($templateData['sample_media_type'])){
                exit(badRequest(401, "Sample media type is requred"));
            }
    
            $handleId = null;
            if (!empty($templateData['sample_media_path']) && !empty($templateData['sample_media_type'])) {
                $uploadResponse = $this->gupshupApi->uploadTemplateMedia(
                    $appId,
                    $templateData['sample_media_path'],
                    $templateData['sample_media_type']
                );
    
                if (isset($uploadResponse['handleId'])) {
                    $handleId = $uploadResponse['handleId'];
                } else {
                    exit(badRequest(442, "Failed to upload sample media: Handle ID not found in response."));
                }
            }
    
            // Map template data
            $mappedData = [
                'elementName' => $templateData['elementname'] ?? null,
                'languageCode' => $templateData['languagecode'] ?? null,
                'content' => $templateData['content'] ?? null,
                'footer' => $templateData['footer'] ?? null,
                'category' => $templateData['category'] ?? null,
                'templateType' => $templateData['templatetype'] ?? 'TEXT',
                'vertical' => $templateData['vertical'] ?? null,
                'appId' => $appId ?? $this->gupshupApi->getCurrentAppId(),
                'example' => $templateData['example'] ?? null,
                'handleId' => $handleId, // Include handleId in the template data
            ];
    
            // Create template on Gupshup
            $response = $this->gupshupApi->createTemplate($appId, $mappedData);
    
            if ($response['status'] === "success") {
                $dbData = [
                    'template_id' => $response['template']['id'],
                    'template_type' => $response['template']['templateType'],
                    'template_name' => $mappedData['elementName'],
                    'category' => $mappedData['category'] ?? 'MARKETING',
                    'content' => $mappedData['content'] ?? '',
                    'language' => $mappedData['languageCode'] ?? 'en',
                    'status' => $response['status'] ?? 'PENDING',
                    'user_id' => $user['id'],
                    'app_id' => $mappedData['appId'],
                    'handle_id' => $mappedData['handleId'],
                    'sample_media_path' => $templateData['sample_media_path'],
                    'sample_media_type' => $templateData['sample_media_type'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
    
                $this->db->insert($this->templatesTable, $dbData);
    
                return array_merge($response, ['local_data' => $dbData]);
            }
        } catch (Exception $e) {
            exit(badRequest(500, "Failed to create template: " . $e->getMessage()));
        }
    }
    
    /**
     * Update an existing template
     */
    public function updateTemplate(string $templateId, array $templateData, ?string $appId = null): array {
        try {
            // Update template on Gupshup
            $response = $this->gupshupApi->editTemplate($appId, $templateId, $templateData);
            
            // Update local database
            $dbData = [
                'name' => $templateData['elementName'] ?? null,
                'category' => $templateData['category'] ?? null,
                'content' => $templateData['templateText'] ?? null,
                'language' => $templateData['language'] ?? null,
                'status' => $response['status'] ?? null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Remove null values
            $dbData = array_filter($dbData, function($value) {
                return $value !== null;
            });

            $where = "template_id = '" . $this->db->escape($templateId) . "'";
            $this->db->update($this->templatesTable, $dbData, $where);

            return array_merge($response, ['local_data' => $dbData]);
        } catch (Exception $e) {
            throw new Exception("Failed to update template: " . $e->getMessage());
        }
    }

    /**
     * Delete a template
     */
    public function deleteTemplate(string $templateId): bool {
        try {
            // Note: Assuming Gupshup API doesn't have a delete endpoint
            // Only removing from local database
            $where = "template_id = '" . $this->db->escape($templateId) . "'";
            return $this->db->delete($this->templatesTable, $where);
        } catch (Exception $e) {
            throw new Exception("Failed to delete template: " . $e->getMessage());
        }
    }

    /**
     * Fetch templates from both Gupshup and local database
     */
    public function getTemplates(?string $appId = null, array $filters = []): array {
        try {
            // Fetch from Gupshup
            $gupshupTemplates = $this->gupshupApi->getTemplates($appId);
            
            // Build WHERE clause for local database
            $whereConditions = [];
            if (!empty($filters['user_id'])) {
                $whereConditions[] = "user_id = " . (int)$filters['user_id'];
            }
            if (!empty($filters['status'])) {
                $whereConditions[] = "status = '" . $this->db->escape($filters['status']) . "'";
            }
            if (!empty($filters['category'])) {
                $whereConditions[] = "category = '" . $this->db->escape($filters['category']) . "'";
            }
            
            $where = !empty($whereConditions) ? implode(' AND ', $whereConditions) : '';
            
            // Fetch from local database
            $localTemplates = $this->db->select($this->templatesTable, $where);
            
            // Merge and organize data
            $mergedTemplates = $this->mergeTemplateData($gupshupTemplates, $localTemplates);
            
            return $mergedTemplates;
        } catch (Exception $e) {
            throw new Exception("Failed to fetch templates: " . $e->getMessage());
        }
    }

    /**
     * Get a single template by ID
     */
    public function getTemplate(string $templateId): ?array {
        try {
            $where = "template_id = '" . $this->db->escape($templateId) . "'";
            return $this->db->find($this->templatesTable, $where);
        } catch (Exception $e) {
            throw new Exception("Failed to fetch template: " . $e->getMessage());
        }
    }

    /**
     * Update template status
     */
    public function updateStatus(string $templateId, string $status): bool {
        try {
            $where = "template_id = '" . $this->db->escape($templateId) . "'";
            return $this->db->update($this->templatesTable, ['status' => $status], $where);
        } catch (Exception $e) {
            throw new Exception("Failed to update template status: " . $e->getMessage());
        }
    }

    /**
     * Get templates by user ID
     */
    public function getTemplatesByUser(int $userId): array {
        try {
            $where = "user_id = " . (int)$userId;
            return $this->db->select($this->templatesTable, $where);
        } catch (Exception $e) {
            throw new Exception("Failed to fetch user templates: " . $e->getMessage());
        }
    }

    /**
     * Helper method to merge Gupshup and local template data
     */
    private function mergeTemplateData(array $gupshupTemplates, array $localTemplates): array {
        $mergedTemplates = [];
        $localTemplatesMap = [];

        // Create map of local templates
        foreach ($localTemplates as $template) {
            $localTemplatesMap[$template['template_id']] = $template;
        }

        // Merge data
        foreach ($gupshupTemplates as $template) {
            $templateId = $template['id'];
            $localData = $localTemplatesMap[$templateId] ?? [];
            
            $mergedTemplates[] = array_merge(
                $template,
                ['local_data' => $localData]
            );
        }

        return $mergedTemplates;
    }
}