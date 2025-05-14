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
    
    public function getPermission($permissionId) {
        return $this->find('permissions', "id = $permissionId");
    }
    
    /**
     * Synchronize roles for a user (remove all existing roles and assign new ones)
     */
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
    
    /**
     * Synchronize permissions for a role (remove all existing permissions and assign new ones)
     */
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
}