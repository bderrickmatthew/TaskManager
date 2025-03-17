<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Bdm\TaskManager\System\DatabasePDO;
use PDO;

class DatabaseTest extends TestCase
{
    private PDO $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = DatabasePDO::instance()->getConnection();
        $this->createTestTables();
    }

    protected function tearDown(): void
    {
        $this->dropTestTables();
        parent::tearDown();
    }

    private function createTestTables(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                login VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL
            )
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS tasks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                user_id INT NOT NULL,
                is_concluded TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ");
    }

    private function dropTestTables(): void
    {
        $this->db->exec("DROP TABLE IF EXISTS tasks");
        $this->db->exec("DROP TABLE IF EXISTS users");
    }

    public function testDatabaseConnection(): void
    {
        $this->assertInstanceOf(PDO::class, $this->db);
    }

    public function testCreateUser(): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, login, password)
            VALUES (:name, :login, :password)
        ");

        $result = $stmt->execute([
            'name' => 'Test User',
            'login' => 'testuser',
            'password' => password_hash('password123', PASSWORD_DEFAULT)
        ]);

        $this->assertTrue($result);
        $this->assertEquals(1, $this->db->lastInsertId());
    }

    public function testCreateTask(): void
    {
        // First create a user
        $stmt = $this->db->prepare("
            INSERT INTO users (name, login, password)
            VALUES (:name, :login, :password)
        ");

        $stmt->execute([
            'name' => 'Test User',
            'login' => 'testuser',
            'password' => password_hash('password123', PASSWORD_DEFAULT)
        ]);

        $userId = $this->db->lastInsertId();

        // Then create a task for that user
        $stmt = $this->db->prepare("
            INSERT INTO tasks (title, user_id)
            VALUES (:title, :user_id)
        ");

        $result = $stmt->execute([
            'title' => 'Test Task',
            'user_id' => $userId
        ]);

        $this->assertTrue($result);

        // Verify the task was created
        $stmt = $this->db->prepare("
            SELECT * FROM tasks WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        $task = $stmt->fetch(PDO::FETCH_OBJ);

        $this->assertNotFalse($task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals($userId, $task->user_id);
        $this->assertEquals(0, $task->is_concluded);
    }

    public function testMarkTaskAsCompleted(): void
    {
        // Create user and task first
        $stmt = $this->db->prepare("
            INSERT INTO users (name, login, password)
            VALUES (:name, :login, :password)
        ");

        $stmt->execute([
            'name' => 'Test User',
            'login' => 'testuser',
            'password' => password_hash('password123', PASSWORD_DEFAULT)
        ]);

        $userId = $this->db->lastInsertId();

        $stmt = $this->db->prepare("
            INSERT INTO tasks (title, user_id)
            VALUES (:title, :user_id)
        ");

        $stmt->execute([
            'title' => 'Test Task',
            'user_id' => $userId
        ]);

        $taskId = $this->db->lastInsertId();

        // Mark task as completed
        $stmt = $this->db->prepare("
            UPDATE tasks 
            SET is_concluded = 1 
            WHERE id = :id
        ");

        $result = $stmt->execute(['id' => $taskId]);

        $this->assertTrue($result);

        // Verify task is marked as completed
        $stmt = $this->db->prepare("
            SELECT is_concluded 
            FROM tasks 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $taskId]);
        $task = $stmt->fetch(PDO::FETCH_OBJ);

        $this->assertEquals(1, $task->is_concluded);
    }
}