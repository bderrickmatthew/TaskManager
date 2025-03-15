<?php

use Bdm\TaskManager\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateTable(): void
    {
        $user = new User();
        $this->assertSame('users', $user->table);
    }
}