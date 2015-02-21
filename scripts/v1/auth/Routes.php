<?php
namespace meteor;

use meteor\endpoints;
use meteor\endpoints\assessments;
use meteor\endpoints\assignments;
use meteor\endpoints\groups;
use meteor\endpoints\users;
use meteor\endpoints\login;

// Setup endpoints
$endpoints = array();

register_endpoint("", new endpoints\ServerEndpoint());
register_endpoint("test", new endpoints\TestEndpoint());

register_endpoint("handshake", new login\HandshakeEndpoint());
register_endpoint("login", new login\LoginEndpoint());
register_endpoint("refresh", new login\RefreshEndpoint());
register_endpoint("invalidate", new login\InvalidateEndpoint());
register_endpoint("validate", new login\ValidateEndpoint());

// User management
register_endpoint("users", new users\UserListEndpoint());
register_endpoint("users/create", new users\UserCreateEndpoint());
register_endpoint("users/:id/groups/add", new users\UserGroupAddEndpoint());
register_endpoint("users/:id/groups", new users\UserGroupsListEndpoint());
register_endpoint("users/:id/assignments/add", null);
register_endpoint("users/:id/assignments/complete", null);
register_endpoint("users/:id/assignments", null);
register_endpoint("users/:id/settings/edit", new users\UserSettingEditEndpoint());
register_endpoint("users/:id/settings/:setting", new users\UserSettingLookupEndpoint());
register_endpoint("users/:id/settings", new users\UserSettingViewEndpoint());
register_endpoint("users/:id/permissions/edit", new users\UserPermissionEditEndpoint());
register_endpoint("users/:id/permissions/:permission", new users\UserPermissionLookupEndpoint());
register_endpoint("users/:id/permissions", new users\UserPermissionViewEndpoint());
register_endpoint("users/:id", new users\UserLookupEndpoint());

// Group management
register_endpoint("groups", new groups\GroupListEndpoint());
register_endpoint("groups/create", new groups\GroupCreateEndpoint());
register_endpoint("groups/:id/users", new groups\GroupUsersEndpoint());
register_endpoint("groups/:id/settings/edit", new groups\GroupSettingEditEndpoint());
register_endpoint("groups/:id/settings/:setting", new groups\GroupSettingLookupEndpoint());
register_endpoint("groups/:id/settings", new groups\GroupSettingViewEndpoint());
register_endpoint("groups/:id/permissions/edit", new groups\GroupPermissionEditEndpoint());
register_endpoint("groups/:id/permissions/:permission", new groups\GroupPermissionLookupEndpoint());
register_endpoint("groups/:id/permissions", new groups\GroupPermissionViewEndpoint());
register_endpoint("groups/:id", new groups\GroupLookupEndpoint());

register_endpoint("assessments", new assessments\AssessmentsListEndpoint());
register_endpoint("assessments/create", new assessments\AssessmentCreateEndpoint());
register_endpoint("assessments/:id", new assessments\AssessmentLookupEndpoint());

register_endpoint("assignments", new assignments\AssignmentsListEndpoint());
register_endpoint("assignments/create", new assignments\AssignmentCreateEndpoint());
register_endpoint("assignments/:id", new assignments\AssignmentLookupEndpoint());

// Asset Management
//register_endpoint("assets", new endpoints\ImageViewEndpoint());
//register_endpoint("assets/upload", new endpoints\ImageUploadEndpoint());

function register_endpoint($pattern, $handler)
{
    global $endpoints;
    if (ends_with($pattern, "/")) {
        $pattern = substr($pattern, 0, strlen($pattern) - 1);
    }
    $pattern = preg_quote($pattern, "/");
    while (preg_match("/\\:([^\\/\\\\]*)/", $pattern, $matches)) {
        $pattern = preg_replace("/\\\\:([^\\/\\\\]*)/", "(?<" . substr($matches[0], 1) . ">[^\\/]+)", $pattern, 1);
    }
    $endpoints["/^" . $pattern . "$/"] = $handler;
}
