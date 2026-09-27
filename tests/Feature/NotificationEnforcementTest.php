<?php

namespace Tests\Feature;

use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBaseData();
    }

    public function test_admin_facility_approval_creates_notification_for_requester(): void
    {
        $dept     = $this->createDepartment();
        $admin    = $this->createUser('admin', $dept);
        $student  = $this->createUser('student', $dept);

        $date = now()->addDays(3)->toDateString();

        $pending = ResourceRequest::create([
            'user_id'         => $student->id,
            'department_id'   => $dept->id,
            'request_type_id' => 1,
            'purpose'         => 'Approval notification test',
            'status'          => 'pending',
        ]);

        RequestItem::create([
            'request_id'   => $pending->id,
            'resource_id'  => null,
            'item_name'    => 'Computer Laboratory 1',
            'quantity'     => 1,
            'request_date' => $date,
            'start_time'   => '09:00',
            'end_time'     => '10:00',
        ]);

        Livewire::actingAs($admin)
            ->test('pages::admin.reservation-facility')
            ->call('accept', $pending->id);

        // The student should have received a notification
        $this->assertDatabaseHas('notifications', [
            'user_id'    => $student->id,
            'request_id' => $pending->id,
        ]);
    }

    public function test_admin_facility_rejection_creates_notification_for_requester(): void
    {
        $dept     = $this->createDepartment();
        $admin    = $this->createUser('admin', $dept);
        $student  = $this->createUser('student', $dept);

        $date = now()->addDays(3)->toDateString();

        $pending = ResourceRequest::create([
            'user_id'         => $student->id,
            'department_id'   => $dept->id,
            'request_type_id' => 1,
            'purpose'         => 'Rejection notification test',
            'status'          => 'pending',
        ]);

        RequestItem::create([
            'request_id'   => $pending->id,
            'resource_id'  => null,
            'item_name'    => 'Computer Laboratory 1',
            'quantity'     => 1,
            'request_date' => $date,
            'start_time'   => '09:00',
            'end_time'     => '10:00',
        ]);

        Livewire::actingAs($admin)
            ->test('pages::admin.reservation-facility')
            ->call('reject', $pending->id);

        $this->assertDatabaseHas('notifications', [
            'user_id'    => $student->id,
            'request_id' => $pending->id,
        ]);
    }

    public function test_admin_material_approval_creates_notification_for_requester(): void
    {
        $dept     = $this->createDepartment();
        $admin    = $this->createUser('admin', $dept);
        $student  = $this->createUser('student', $dept);

        $material = $this->createMaterial('Bond Paper A4', 100);

        \DB::table('resource_all_locations')->insert([
            'resource_id'        => $material->id,
            'department_id'      => $dept->id,
            'allocated_quantity' => 100,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $pending = ResourceRequest::create([
            'user_id'         => $student->id,
            'department_id'   => $dept->id,
            'request_type_id' => 2,
            'purpose'         => 'Material approval notification test',
            'status'          => 'pending',
        ]);

        RequestItem::create([
            'request_id'   => $pending->id,
            'resource_id'  => $material->id,
            'item_name'    => $material->resource_name,
            'quantity'     => 10,
            'request_date' => now()->toDateString(),
        ]);

        Livewire::actingAs($admin)
            ->test('pages::admin.reservation-material')
            ->call('accept', $pending->id);

        $this->assertDatabaseHas('notifications', [
            'user_id'    => $student->id,
            'request_id' => $pending->id,
        ]);
    }
}
