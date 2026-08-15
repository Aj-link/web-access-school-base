<?php
// database/seeders/NstpScheduleSeeder.php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class NstpScheduleSeeder extends Seeder
{
    /**
     * Default NSTP schedule for First Year students, both semesters.
     */
    public function run(): void
    {
        $semesters = [
            '1st Semester' => 'NSTP 1',
        ];

        foreach ($semesters as $semester => $subjectName) {
            Schedule::firstOrCreate(
                [
                    'subject_code' => 'NSTP',
                    'year_level'   => 'First Year',
                    'day_type'     => 'SAT',
                    'semester'     => $semester,
                ],
                [
                    'subject_name' => $subjectName,
                    'teacher'      => 'TBA',
                    'section'      => 'All Sections',
                    'department'   => 'All Departments',
                    'start_time'   => '08:00:00',
                    'end_time'     => '12:00:00',
                    'room'         => 'TBA',
                    'school_year'  => '2026-2027',
                ]
            );
        }
    }
}
