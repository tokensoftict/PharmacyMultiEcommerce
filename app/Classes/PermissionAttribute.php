<?php

namespace App\Classes;
#[\Attribute]
class PermissionAttribute
{

    public string $description;
    public string $permissionName;

    public string $roleName;

    public function __construct(string $description, string $permissionName, string $roleName)
    {
        $this->description = $description;
        $this->permissionName = $permissionName;
        $this->roleName = $roleName;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getPermissionName(): string
    {
        return $this->permissionName;
    }


    /**
     * @return string
     */
    public function getRoleName(): string
    {
        return $this->roleName;
    }

}
