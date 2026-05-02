<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\CustomerCommunication;
use App\Models\Member;
use App\Models\TravelPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommunicationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $member;
    protected $package;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create();
        
        // Create test member (jamaah)
        $this->member = Member::factory()->create([
            'is_jamaah' => true
        ]);
        
        // Create test package
        $this->package = TravelPackage::factory()->create();
    }

    /** @test */
    public function it_can_create_communication_record()
    {
        $data = [
            'id_member' => $this->member->id_member,
            'id_travel_package' => $this->package->id,
            'communication_method' => 'phone_call',
            'communication_date' => now(),
            'notes' => 'Test communication',
            'follow_up_status' => 'contacted',
            'next_follow_up_date' => now()->addDays(7)->toDateString()
        ];

        $this->actingAs($this->user)
            ->postJson(route('travel.communication.store'), $data)
            ->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('customer_communications', [
            'id_member' => $this->member->id_member,
            'communication_method' => 'phone_call',
            'follow_up_status' => 'contacted'
        ]);
    }

    /** @test */
    public function it_can_retrieve_member_communication_history()
    {
        // Create multiple communications
        CustomerCommunication::factory()->count(3)->create([
            'id_member' => $this->member->id_member,
            'contacted_by' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('travel.communication.member-history', $this->member->id_member))
            ->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_get_pending_followups()
    {
        // Create communication with pending follow-up
        CustomerCommunication::factory()->create([
            'id_member' => $this->member->id_member,
            'follow_up_status' => 'pending',
            'next_follow_up_date' => now()->subDays(1), // Overdue
            'contacted_by' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('travel.communication.pending-followups'))
            ->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertGreaterThan(0, count($response->json('data')));
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->actingAs($this->user)
            ->postJson(route('travel.communication.store'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['id_member', 'communication_method', 'communication_date', 'follow_up_status']);
    }

    /** @test */
    public function it_can_update_communication_record()
    {
        $communication = CustomerCommunication::factory()->create([
            'id_member' => $this->member->id_member,
            'follow_up_status' => 'pending',
            'contacted_by' => $this->user->id
        ]);

        $this->actingAs($this->user)
            ->putJson(route('travel.communication.update', $communication->id), [
                'id_member' => $this->member->id_member,
                'communication_method' => 'whatsapp',
                'communication_date' => now(),
                'follow_up_status' => 'responded',
                'notes' => 'Updated notes'
            ])
            ->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('customer_communications', [
            'id' => $communication->id,
            'follow_up_status' => 'responded',
            'notes' => 'Updated notes'
        ]);
    }

    /** @test */
    public function it_can_delete_communication_record()
    {
        $communication = CustomerCommunication::factory()->create([
            'id_member' => $this->member->id_member,
            'contacted_by' => $this->user->id
        ]);

        $this->actingAs($this->user)
            ->deleteJson(route('travel.communication.destroy', $communication->id))
            ->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseMissing('customer_communications', [
            'id' => $communication->id
        ]);
    }

    /** @test */
    public function it_orders_communication_history_chronologically()
    {
        // Create communications with different dates
        $comm1 = CustomerCommunication::factory()->create([
            'id_member' => $this->member->id_member,
            'communication_date' => now()->subDays(3),
            'contacted_by' => $this->user->id
        ]);

        $comm2 = CustomerCommunication::factory()->create([
            'id_member' => $this->member->id_member,
            'communication_date' => now()->subDays(1),
            'contacted_by' => $this->user->id
        ]);

        $comm3 = CustomerCommunication::factory()->create([
            'id_member' => $this->member->id_member,
            'communication_date' => now()->subDays(2),
            'contacted_by' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('travel.communication.member-history', $this->member->id_member));

        $data = $response->json('data');
        
        // Verify chronological order (oldest first)
        $this->assertEquals($comm1->id, $data[0]['id']);
        $this->assertEquals($comm3->id, $data[1]['id']);
        $this->assertEquals($comm2->id, $data[2]['id']);
    }
}
