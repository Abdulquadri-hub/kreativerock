<?php

session_start();

header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . "/kreativerock/utils/autoload.php";

// if (isset($_SESSION['elfuseremail']) && $_SESSION['elfuseremail'] === null || !isset($_SESSION['elfuseremail'])) {
//     exit(badRequest(204,'Invalid session data. Proceed to login'));
// }

$user = new User();
$userId = $user->getUserIdByEmail($_SESSION['elfuseremail'] ?? "abdulquadri.aq@gmail.com");

if (!$userId) {
    exit(badRequest(404, 'User not found'));
}

$rolePermissions = new RolesAndPermissions();
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'check_permission':
        $permission = $_POST['permission'] ?? '';
        if (empty($permission)) {
            exit(badRequest(400, 'Permission name is required'));
        }
        
        $hasPermission = $rolePermissions->hasPermission($userId, $permission);
        
        if($hasPermission){
            exit(
                success([
                    'has_permission' => $hasPermission
                ], "permission check completed")
            );
        }
        exit(badRequest(400,"User permission check failed"));

        break;
        
    case 'get_user_roles':
        $roles = $rolePermissions->getUserRoles($userId);
        if(empty($roles)){
           exit(badRequest(404, "User Roles not found"));
        }
        echo success($roles);
        break;
        
    case 'assign_role':

        if (!$rolePermissions->hasPermission($userId, 'roles_and_permissions')) {
            exit(badRequest(403, 'You do not have permission to assign roles'));
        }
        
        $targetUserId = $_POST['target_user_id'] ?? 0;
        $roleId = $_POST['role_id'] ?? 0;
        
        if (empty($targetUserId) || empty($roleId)) {
            exit(badRequest(400, 'Target user ID and role ID are required'));
        }
        
        $result = $rolePermissions->assignRole($targetUserId, $roleId);
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'code' => 200,
                'message' => 'Role assigned successfully',
                'data' => [
                    'user_id' => $targetUserId,
                    'role_id' => $roleId,
                    'assignment_id' => $result
                ]
            ]);
        } else {
            exit(badRequest(500, 'Failed to assign role'));
        }
        break;
        
    case 'create_role':
        
        if (!$rolePermissions->hasPermission($userId, 'roles_and_permissions')) {
            exit(badRequest(403, 'You do not have permission to create roles'));
        }
        
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        
        if (empty($name)) {
            exit(badRequest(400, 'Role name is required'));
        }
        
        $result = $rolePermissions->createRole($name, $description);
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'code' => 200,
                'message' => 'Role created successfully',
                'data' => [
                    'role_id' => $result,
                    'name' => $name,
                    'description' => $description
                ]
            ]);
        } else {
            exit(badRequest(500, 'Failed to create role'));
        }
        break;
        
    case 'sync_role_permissions':
        
        if (!$rolePermissions->hasPermission($userId, 'roles_and_permissions')) {
            exit(badRequest(403, 'You do not have permission to manage role permissions'));
        }
        
        $roleId = $_POST['role_id'] ?? 0;
        $permissionIds = $_POST['permission_ids'] ?? [];
        
        if (empty($roleId) || !is_array($permissionIds)) {
            exit(badRequest(400, 'Role ID and permission IDs array are required'));
        }
        
        $result = $rolePermissions->syncRolePermissions($roleId, $permissionIds);
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'code' => 200,
                'message' => 'Role permissions synchronized successfully',
                'data' => [
                    'role_id' => $roleId,
                    'permission_count' => count($permissionIds)
                ]
            ]);
        } else {
            exit(badRequest(500, 'Failed to synchronize role permissions'));
        }
        break;
        
    case 'get_all_permissions':
        // Check if the current user has permission to view permissions
        if (!$rolePermissions->hasPermission($userId, 'roles_and_permissions')) {
            exit(badRequest(403, 'You do not have permission to view permissions'));
        }
        
        $permissions = $rolePermissions->getAllPermissions();
        echo json_encode([
            'status' => 'success',
            'code' => 200,
            'message' => 'Permissions retrieved successfully',
            'data' => [
                'permissions' => $permissions
            ]
        ]);
        break;
        
    default:
        exit(badRequest(400, 'Invalid action specified'));
}
