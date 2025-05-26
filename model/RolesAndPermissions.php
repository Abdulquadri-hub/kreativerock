<?php

class RolesAndPermissions extends dbFunctions {
    
    private $connection;
    private $logger;
    private $userRoles = [];
    private $userPermissions = [];

    public function __construct() {
        parent::__construct();
        $this->logger = new Logger("roles_permissions_log");
    }
    
    public function assignRole($userId, $roleId) {
        $data = [
            'user_id' => $userId,
            'role_id' => $roleId,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert('user_roles', $data);
    }
    

    public function removeRole($userId, $roleId) {
        $where = "user_id = $userId AND role_id = $roleId";
        return $this->delete('user_roles', $where);
    }

    public function createRole($name, $description = '') {
        $data = [
            'name' => $name,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert('roles', $data);
    }

    public function updateRole($roleId, $data) {
        $where = "id = $roleId";
        return $this->update('roles', $data, $where);
    }
    

    public function deleteRole($roleId) {
        $where = "id = $roleId";
        return $this->delete('roles', $where);
    }
    

    public function createPermission($name, $description = '') {
        $data = [
            'name' => $name,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert('permissions', $data);
    }

    public function assignPermissionToRole($roleId, $permissionId) {
        $data = [
            'role_id' => $roleId,
            'permission_id' => $permissionId,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert('role_permissions', $data);
    }
    

    public function removePermissionFromRole($roleId, $permissionId) {
        $where = "role_id = $roleId AND permission_id = $permissionId";
        return $this->delete('role_permissions', $where);
    }

    public function getUserRoles($userId) {
        $sql = "SELECT r.* FROM roles r 
                JOIN user_roles ur ON r.id = ur.role_id 
                WHERE ur.user_id = ?";
        
        return $this->query($sql, [$userId]);
    }
    

    public function getUserPermissions($userId) {
        $sql = "SELECT DISTINCT p.* FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                JOIN user_roles ur ON rp.role_id = ur.role_id 
                WHERE ur.user_id = ?";
        
        return $this->query($sql, [$userId]);
    }
    
    public function hasRole($userId, $roleName) {
        if (empty($this->userRoles)) {
            $this->userRoles = $this->getUserRoles($userId);
        }
        
        if (is_array($roleName)) {
            foreach ($roleName as $name) {
                foreach ($this->userRoles as $role) {
                    if ($role['name'] === $name) {
                        return true;
                    }
                }
            }
            return false;
        } else {
            foreach ($this->userRoles as $role) {
                if ($role['name'] === $roleName) {
                    return true;
                }
            }
            return false;
        }
    }

    public function hasPermission($userId, $permissionName) {
        if (empty($this->userPermissions)) {
            $this->userPermissions = $this->getUserPermissions($userId);
        }
        
        if (is_array($permissionName)) {
            foreach ($permissionName as $name) {
                foreach ($this->userPermissions as $permission) {
                    if ($permission['name'] === $name) {
                        return true;
                    }
                }
            }
            return false;
        } else {
            foreach ($this->userPermissions as $permission) {
                if ($permission['name'] === $permissionName) {
                    return true;
                }
            }
            return false;
        }
    }
    
    public function getAllRoles() {
        return $this->select('roles');
    }
    
    public function getAllPermissions() {
        return $this->select('permissions');
    }
    
    public function getRolePermissions($roleId) {
        $sql = "SELECT p.* FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                WHERE rp.role_id = ?";
        
        return $this->query($sql, [$roleId]);
    }
    
    public function can($userId, $permission) {
        return $this->hasPermission($userId, $permission);
    }
    
    public function cannot($userId, $permission) {
        return !$this->can($userId, $permission);
    }
    
    public function getRole($roleId) {
        return $this->find('roles', "id = $roleId");
    }

    public function getRoleByName($roleName) {
        return $this->find('roles', "name = '$roleName'");
    }
    
    public function getPermission($permissionId) {
        return $this->find('permissions', "id = $permissionId");
    }

    public function syncUserRoles($userId, $roleIds) {
        $this->beginTransaction();
        
        try {
            // Remove all existing roles
            $this->delete('user_roles', "user_id = $userId");
            
            // Assign new roles
            foreach ($roleIds as $roleId) {
                $this->assignRole($userId, $roleId);
            }
            
            $this->commitTransaction();
            return true;
        } catch (Exception $e) {
            $this->rollbackTransaction();
            $this->logger->error('Error syncing user roles: ' . $e->getMessage());
            return false;
        }
    }
    
    public function syncRolePermissions($roleId, $permissionIds) {
        $this->beginTransaction();
        
        try {
            // Remove all existing permissions
            $this->delete('role_permissions', "role_id = $roleId");
            
            // Assign new permissions
            foreach ($permissionIds as $permissionId) {
                $this->assignPermissionToRole($roleId, $permissionId);
            }
            
            $this->commitTransaction();
            return true;
        } catch (Exception $e) {
            $this->rollbackTransaction();
            $this->logger->error('Error syncing role permissions: ' . $e->getMessage());
            return false;
        }
    }

    public function getUserWithRolesAndPermissions($userId) {
        $sql = "
            SELECT 
                u.*,
                r.id as role_id,
                r.name as role_name,
                r.description as role_description,
                p.id as permission_id,
                p.name as permission_name,
                p.description as permission_description
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            LEFT JOIN role_permissions rp ON r.id = rp.role_id
            LEFT JOIN permissions p ON rp.permission_id = p.id
            WHERE u.id = ?
            ORDER BY r.id, p.id
        ";
        
        $result = $this->query($sql, [$userId]);
        
        if(!$result) {
            return null;
        }
        
        $userData = null;
        $roles = [];
        $permissions = [];
        $processedRoles = [];
        $processedPermissions = [];
        
        foreach($result as $row) {

            if(!$userData) {
                $userData = [
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'firstname' => $row['firstname'],
                    'lastname' => $row['lastname'],
                    'status' => $row['status'],
                    'address' => $row['address'],
                    'class' => $row['class'],
                    'role' => $row['role'],
                    'dateofbirth' => $row['dateofbirth'],
                    'referralcode' => $row['referralcode']
                ];
            }
            
            if($row['role_id'] && !in_array($row['role_id'], $processedRoles)) {
                $roles[] = [
                    'id' => $row['role_id'],
                    'name' => $row['role_name'],
                    'description' => $row['role_description']
                ];
                $processedRoles[] = $row['role_id'];
            }
            
            if($row['permission_id'] && !in_array($row['permission_id'], $processedPermissions)) {
                $permissions[] = [
                    'id' => $row['permission_id'],
                    'name' => $row['permission_name'],
                    'description' => $row['permission_description']
                ];
                $processedPermissions[] = $row['permission_id'];
            }
        }
        
        if($userData) {
            $userData['roles'] = $roles;
            $userData['permissions'] = $permissions;
        }
        
        return $userData;
    }
    
    public function getAllUsersWithRolesAndPermissions() {
        $sql = "
            SELECT 
                u.*,
                r.id as role_id,
                r.name as role_name,
                r.description as role_description,
                p.id as permission_id,
                p.name as permission_name,
                p.description as permission_description
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            LEFT JOIN role_permissions rp ON r.id = rp.role_id
            LEFT JOIN permissions p ON rp.permission_id = p.id
            ORDER BY u.id, r.id, p.id
        ";
        
        $result = $this->query($sql);
        
        if(!$result) {
            return [];
        }
        
        $users = [];
        $processedUsers = [];
        
        foreach($result as $row) {
            $userId = $row['id'];
            
            // Initialize user if not processed
            if(!isset($processedUsers[$userId])) {
                $processedUsers[$userId] = [
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'firstname' => $row['firstname'],
                    'lastname' => $row['lastname'],
                    'status' => $row['status'],
                    'address' => $row['address'],
                    'class' => $row['class'],
                    'role' => $row['role'],
                    'dateofbirth' => $row['dateofbirth'],
                    'referralcode' => $row['referralcode'],
                    'roles' => [],
                    'permissions' => [],
                    'processedRoles' => [],
                    'processedPermissions' => []
                ];
            }
            
            // Add unique roles
            if($row['role_id'] && !in_array($row['role_id'], $processedUsers[$userId]['processedRoles'])) {
                $processedUsers[$userId]['roles'][] = [
                    'id' => $row['role_id'],
                    'name' => $row['role_name'],
                    'description' => $row['role_description']
                ];
                $processedUsers[$userId]['processedRoles'][] = $row['role_id'];
            }
            
            // Add unique permissions
            if($row['permission_id'] && !in_array($row['permission_id'], $processedUsers[$userId]['processedPermissions'])) {
                $processedUsers[$userId]['permissions'][] = [
                    'id' => $row['permission_id'],
                    'name' => $row['permission_name'],
                    'description' => $row['permission_description']
                ];
                $processedUsers[$userId]['processedPermissions'][] = $row['permission_id'];
            }
        }
        
        // Clean up helper arrays and return indexed array
        foreach($processedUsers as &$user) {
            unset($user['processedRoles']);
            unset($user['processedPermissions']);
        }
        
        return array_values($processedUsers);
    }
}