<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class ChatPropertiesTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create only the tables we need for this test
        $this->createRequiredTables();
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        // Clean up tables
        Schema::dropIfExists('messages');
        Schema::dropIfExists('users');
        
        parent::tearDown();
    }

    /**
     * Create the minimal tables required for chat testing.
     */
    protected function createRequiredTables(): void
    {
        // Drop tables if they exist
        Schema::dropIfExists('messages');
        Schema::dropIfExists('users');

        // Create users table with all required columns for User factory
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->unsignedBigInteger('current_team_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Create messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('mode', ['superadmin', 'chatbot'])->default('superadmin');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['sender_id', 'receiver_id', 'mode']);
            $table->index(['receiver_id', 'is_read']);
        });
    }

    /**
     * Feature: admin-chat-system, Property 1: Message delivery consistency
     * Validates: Requirements 2.2, 6.1
     * 
     * Property: For any message sent by a user, the message should be stored 
     * in the database with correct sender_id, receiver_id, mode, and content 
     * before broadcasting the event.
     * 
     * This test runs 100 iterations with random message data to verify that
     * message persistence is consistent across all valid inputs.
     */
    public function test_message_delivery_consistency(): void
    {
        // Run 100 iterations with random data
        for ($i = 0; $i < 100; $i++) {
            // Generate random users
            $sender = User::factory()->create();
            $receiver = User::factory()->create();
            
            // Generate random message data
            $mode = fake()->randomElement(['superadmin', 'chatbot']);
            $content = fake()->sentence(rand(5, 50));
            
            // For chatbot mode, receiver should be null
            $expectedReceiverId = $mode === 'chatbot' ? null : $receiver->id;
            
            // Create message
            $message = Message::create([
                'sender_id' => $sender->id,
                'receiver_id' => $expectedReceiverId,
                'mode' => $mode,
                'content' => $content,
            ]);
            
            // Property assertion: Message should be persisted with correct data
            $this->assertDatabaseHas('messages', [
                'id' => $message->id,
                'sender_id' => $sender->id,
                'receiver_id' => $expectedReceiverId,
                'mode' => $mode,
                'content' => $content,
            ]);
            
            // Verify the message can be retrieved
            $retrievedMessage = Message::find($message->id);
            $this->assertNotNull($retrievedMessage, 'Message should be retrievable from database');
            
            // Verify all fields match
            $this->assertEquals($sender->id, $retrievedMessage->sender_id, 
                'Sender ID should match');
            $this->assertEquals($expectedReceiverId, $retrievedMessage->receiver_id, 
                'Receiver ID should match');
            $this->assertEquals($mode, $retrievedMessage->mode, 
                'Mode should match');
            $this->assertEquals($content, $retrievedMessage->content, 
                'Content should match');
            
            // Verify default values
            $this->assertFalse($retrievedMessage->is_read, 
                'New messages should be unread by default');
            $this->assertNull($retrievedMessage->read_at, 
                'Read timestamp should be null for unread messages');
            
            // Verify timestamps are set
            $this->assertNotNull($retrievedMessage->created_at, 
                'Created timestamp should be set');
            $this->assertNotNull($retrievedMessage->updated_at, 
                'Updated timestamp should be set');
            
            // Clean up for next iteration
            $message->delete();
            $sender->delete();
            $receiver->delete();
        }
    }
}
