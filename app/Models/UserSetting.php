<?php

namespace App\Models;

class UserSetting
{
    public int $id;
    public int $user_id;
    public string $setting_key;
    public ?string $setting_value = null;
    public string $created_at;
    public string $updated_at;
}
