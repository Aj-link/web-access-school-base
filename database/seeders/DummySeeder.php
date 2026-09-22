<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ReportType;
use App\Models\Request;
use App\Models\RequestApproval;
use App\Models\RequestItem;
use App\Models\Resource;
use App\Models\ResourceAllLocation;
use App\Models\ResourceUsage;
use App\Models\ResponsibilityCenter;
use App\Models\Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Independent / lookup tables first
        ResponsibilityCenter::factory(3)->create();
        ReportType::factory(4)->create();

        // Depends on Department (already seeded) + ResourceType (already seeded)
        $resources = Resource::factory(10)->create();

        // Depends on users (already seeded via your role loop) + departments
        $requests = Request::factory(15)->create();

        // Depends on requests
        RequestItem::factory(20)->create();
        RequestApproval::factory(15)->create();
        Notification::factory(20)->create();

        // Depends on resources + users
        ResourceUsage::factory(20)->create();
        ResourceAllLocation::factory(10)->create();
        Stock::factory(15)->create();

        // Depends on report types + users
        Report::factory(5)->create();

        // Depends on users
        AuditLog::factory(15)->create();
    }
}
