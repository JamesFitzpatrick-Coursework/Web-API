<?php
namespace meteor;

use meteor\endpoints;

// Setup endpoints
$endpoints = array();

register_endpoint("", new endpoints\ServerEndpoint());

register_endpoint("handshake", new endpoints\HandshakeEndpoint());
register_endpoint("login", new endpoints\LoginEndpoint());
register_endpoint("refresh", new endpoints\RefreshEndpoint());
register_endpoint("invalidate", new endpoints\InvalidateEndpoint());
register_endpoint("validate", new endpoints\ValidateEndpoint());
register_endpoint("test", new endpoints\TestEndpoint());

// User management
register_endpoint("users", new endpoints\UserListEndpoint());
register_endpoint("users/create", new endpoints\UserCreateEndpoint());
register_endpoint("users/:id/groups/add", null);
register_endpoint("users/:id/groups", new endpoints\UserGroupsListEndpoint());
register_endpoint("users/:id/settings/edit", new endpoints\UserSettingEditEndpoint());
register_endpoint("users/:id/settings/:setting", new endpoints\UserSettingLookupEndpoint());
register_endpoint("users/:id/settings", new endpoints\UserSettingViewEndpoint());
register_endpoint("users/:id/permissions/edit", new endpoints\UserPermissionEditEndpoint());
register_endpoint("users/:id/permissions/:permission", new endpoints\UserPermissionLookupEndpoint());
register_endpoint("users/:id/permissions", new endpoints\UserPermissionViewEndpoint());
register_endpoint("users/:id", new endpoints\UserLookupEndpoint());

// Group management
register_endpoint("groups", new endpoints\GroupListEndpoint());
register_endpoint("groups/create", new endpoints\GroupCreateEndpoint());
register_endpoint("groups/:id/users", new endpoints\GroupUsersEndpoint());
register_endpoint("groups/:id/settings/edit", new endpoints\GroupSettingEditEndpoint());
register_endpoint("groups/:id/settings/:setting", new endpoints\GroupSettingLookupEndpoint());
register_endpoint("groups/:id/settings", new endpoints\GroupSettingViewEndpoint());
register_endpoint("groups/:id/permissions/edit", new endpoints\GroupPermissionEditEndpoint());
register_endpoint("groups/:id/permissions/:permission", new endpoints\GroupPermissionLookupEndpoint());
register_endpoint("groups/:id/permissions", new endpoints\GroupPermissionViewEndpoint());
register_endpoint("groups/:id", new endpoints\GroupLookupEndpoint());

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
