<?php
namespace Bdm\TaskManager\Models;

use Bdm\TaskManager\System\Model;

class User extends Model
{
    public string $table = "users";
    public bool $softDelete = true;
}