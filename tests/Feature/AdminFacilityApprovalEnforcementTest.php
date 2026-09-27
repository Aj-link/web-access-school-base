<?php

namespace Tests\Feature;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminFacilityApprovalEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBaseData();
    }

    public function test_admin_cannot_approve_overlapping_facility_reservation(): void
    {
        $dept    = $this->createDepartment();
        $admin   = $this->createUser('admin', $dept);
        $studentA = $this->createUser('student', $dept);
        $studentB = $this->createUser('student', $dept);

        $date = now()->addDays(3)->toDateString();

        // Existing approved booking 9:00–10:00
        $approved = ResourceRequest::create([
            'user_id'         => $studentA->id,
            'department_id'   => $dept->id,
            'request_type_id' => 1,
            'purpose'         => 'Already-approved',
            'status'          => 'approved',
        ]);

        RequestItem::create([
            'request_id'   => $approved->id,
            'resource_id'  => null,
            'item_name'    => 'Computer Laboratory 1',
            'quantity'     => 1,
            'request_date' => $date,
            'start_time'   => '09:00',
            'end_time'     => '10:00',
        ]);

        // Pending overlapping booking 9:30–10:30
        $pending = ResourceRequest::create([
            'user_id'         => $studentB->id,
            'department_id'   => $dept->id,
            'request_type_id' => 1,
            'purpose'         => 'Overlapping',
            'status'          => 'pending',
        ]);

        RequestItem::create([
            'request_id'   => $pending->id,
            'resource_id'  => null,
            'item_name'    => 'Computer Laboratory 1',
            'quantity'     => 1,
            'request_date' => $date,
            'start_time'   => '09:30',
            'end_time'     => '10:30',
        ]);

        Livewire::actingAs($admin)
            ->test('pages::admin.reservation-facility')
            ->call('accept', $pending->id);

        $this->assertDatabaseHas('requests', [
            'id'     => $pending->id,
            'status' => 'pending',
        ]);
    }
}
