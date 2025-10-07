<?php

namespace App\Policies;

class PermissionPolicy
{
    public static function canView(string $globalRole, ?string $resourcePerm): bool
    {
        if ($globalRole === 'admin' || $globalRole === 'manager') return true;
        if ($resourcePerm === 'read' || $resourcePerm === 'write' || $resourcePerm === 'delete' || $resourcePerm === 'share') return true;
        return $globalRole === 'editor' || $globalRole === 'viewer';
    }

    public static function canWrite(string $globalRole, ?string $resourcePerm): bool
    {
        if ($globalRole === 'admin' || $globalRole === 'manager') return true;
        return $resourcePerm === 'write' || $resourcePerm === 'delete' || $resourcePerm === 'share';
    }
}


