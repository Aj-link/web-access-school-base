<?php

namespace App\Livewire\Admin;

use App\Models\Request as ResourceRequest;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.admin')] class extends Component
{
    public int $year;
    public int $month;
    public string $selectedDay = '';
    public string $search = '';

    // Days of week for schedule view
    public string $scheduleDay = 'MW';

    public function mount()
    {
        $this->year  = now()->year;
        $this->month = now()->month;
    }

    public function previousMonth()
    {
        if ($this->month === 1) {
            $this->month = 12;
            $this->year--;
        } else {
            $this->month--;
        }
    }

    public function nextMonth()
    {
        if ($this->month === 12) {
            $this->month = 1;
            $this->year++;
        } else {
            $this->month++;
        }
    }

    public function goToToday()
    {
        $this->year  = now()->year;
        $this->month = now()->month;
    }

    public function selectDay(string $day)
    {
        $this->selectedDay = $day;
    }

    #[Computed]
    public function schedules()
    {
        // Parse the uploaded Excel schedule data
        // We'll return structured schedule data from the Excel
        return $this->parseScheduleData();
    }

    private function parseScheduleData(): array
    {
        $file = storage_path('app/schedules/SCHEDULE_OF_CLASSES_1ST_SEM__AY_2026-2027.xlsx');

        if (!file_exists($file)) {
            return [];
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $schedules   = [];

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $data  = $sheet->toArray();

            foreach ($data as $row) {
                foreach ($row as $cell) {
                    if (empty($cell) || $cell === 'NaN') continue;

                    // Parse cell like: "GECC 113   DR. EFREN   RM. 207"
                    if (preg_match('/([A-Z]+\s+\d+[A-Z]*)\s+(.*?)\s+RM[.\s]+([A-Z0-9\s]+)/i', $cell, $matches)) {
                        $schedules[] = [
                            'subject'  => trim($matches[1]),
                            'teacher'  => trim($matches[2]),
                            'room'     => trim($matches[3]),
                            'sheet'    => $sheetName,
                        ];
                    }
                }
            }
        }

        return $schedules;
    }

    #[Computed]
    public function approvedReservations()
    {
        return ResourceRequest::with(['user.department', 'items'])
            ->where('request_type_id', 1)
            ->where('status', 'approved')
            ->whereHas('items', function ($q) {
                $q->whereYear('request_date', $this->year)
                  ->whereMonth('request_date', $this->month);
            })
            ->get()
            ->groupBy(function ($r) {
                return \Carbon\Carbon::parse($r->items->first()->request_date)->format('Y-m-d');
            });
    }

    #[Computed]
    public function calendarDays()
    {
        $start       = \Carbon\Carbon::create($this->year, $this->month, 1);
        $startDay    = $start->dayOfWeek;
        $daysInMonth = $start->daysInMonth;

        $days = [];

        for ($i = 0; $i < $startDay; $i++) {
            $days[] = null;
        }

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $days[] = $d;
        }

        return $days;
    }

    #[Computed]
    public function staticSchedules(): array
    {
        // Static schedule data parsed from the Excel
        // Time → Room → [subject, teacher, section, department]
        return [
            'MW' => [
                '7:30-9:00' => [
                    ['subject' => 'GECC 113', 'teacher' => 'DR. EFREN', 'room' => 'RM. 207', 'section' => 'BPED1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'KEYD 111', 'teacher' => 'MS. TUPAL', 'room' => 'LAB3', 'section' => 'BSOA 1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'TMPC 111', 'teacher' => 'MS. MEDALLA', 'room' => 'RM 406', 'section' => 'BSTM 1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'CRIM 111', 'teacher' => 'MS. FERNANDEZ', 'room' => 'RM. 406', 'section' => 'CRIM 1-A', 'dept' => 'CRIM'],
                    ['subject' => 'ELEC 212', 'teacher' => 'MR. BERNARES', 'room' => 'RM. 408', 'section' => 'CRIM 1-B', 'dept' => 'CRIM'],
                ],
                '9:00-10:30' => [
                    ['subject' => 'PFIT 111', 'teacher' => 'MS. PONELES', 'room' => 'IS FIELD', 'section' => 'BEED-1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'GECC 216', 'teacher' => 'MR. GOTEL', 'room' => 'RM IS F2', 'section' => 'BEED-1B', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'RLWW 311', 'teacher' => 'MS. JABIL', 'room' => 'RM. 206', 'section' => 'CRIM 1-A', 'dept' => 'CRIM'],
                    ['subject' => 'CRIM 111', 'teacher' => 'MS. FERNANDEZ M.', 'room' => 'RM. 406', 'section' => 'CRIM 1-B', 'dept' => 'CRIM'],
                ],
                '10:30-12:00' => [
                    ['subject' => 'GECC 112', 'teacher' => 'MR. BERNARES', 'room' => 'IS G2', 'section' => 'BEED-1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'GECC 113', 'teacher' => 'MS. PAMA', 'room' => 'RM. 307', 'section' => 'BEED-1B', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'GECC 11', 'teacher' => 'DR. EFREN', 'room' => 'RM. 209', 'section' => 'CRIM 1-A', 'dept' => 'CRIM'],
                    ['subject' => 'RLWW 311', 'teacher' => 'MS. JABIL', 'room' => 'RM. 206', 'section' => 'CRIM 1-B', 'dept' => 'CRIM'],
                ],
                '1:30-3:00' => [
                    ['subject' => 'EDUC 1111', 'teacher' => 'MR. BERNARES', 'room' => 'RM 207', 'section' => 'BPED1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'THEO 111', 'teacher' => 'MS. JUNCIA', 'room' => 'RM 208', 'section' => 'BSA', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'RLWW 311', 'teacher' => 'MS. JABIL', 'room' => 'RM. 103', 'section' => 'CRIM1-C', 'dept' => 'CRIM'],
                    ['subject' => 'CRIM 111', 'teacher' => 'MS. FERNANDEZ M.', 'room' => 'RM. IS G1', 'section' => 'CRIM 1-D', 'dept' => 'CRIM'],
                ],
                '3:00-4:30' => [
                    ['subject' => 'THEO 111', 'teacher' => 'MS. JUNCIA', 'room' => 'RM 208', 'section' => 'BEED-1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'PFIT 111', 'teacher' => 'MS. PONELES', 'room' => 'IS FIELD', 'section' => 'BEED-1B', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'ELEC 212', 'teacher' => 'MR. BERNARES', 'room' => 'RM. 408', 'section' => 'CRIM 1-A', 'dept' => 'CRIM'],
                    ['subject' => 'CRIM 111', 'teacher' => 'MS. FERNANDEZ M.', 'room' => 'RM. IS G1', 'section' => 'CRIM1-E', 'dept' => 'CRIM'],
                ],
            ],
            'TTH' => [
                '7:30-9:00' => [
                    ['subject' => 'GECC 113', 'teacher' => 'DR. EFREN', 'room' => 'RM. 208', 'section' => 'BEED-1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'EDUC 111', 'teacher' => 'MR. BERNARES', 'room' => 'RM 209', 'section' => 'BPED1B', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'ENGL 100', 'teacher' => 'MS. PAMA', 'room' => 'RM 404', 'section' => 'CRIM 1-A', 'dept' => 'CRIM'],
                    ['subject' => 'GECC 112', 'teacher' => 'MR. BAUTISTA', 'room' => 'RM. 208', 'section' => 'CRIM 1-B', 'dept' => 'CRIM'],
                ],
                '9:00-10:30' => [
                    ['subject' => 'GECC 111', 'teacher' => 'MS. JIELYNE', 'room' => 'RM. 101', 'section' => 'BPED1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'THEO 111', 'teacher' => 'MS. DELA CRUZ', 'room' => 'RM. 102', 'section' => 'CRIM 1-A', 'dept' => 'CRIM'],
                    ['subject' => 'GECC 113', 'teacher' => 'MR. DELA CERNA', 'room' => 'RM. IS F2', 'section' => 'CRIM 1-B', 'dept' => 'CRIM'],
                ],
                '10:30-12:00' => [
                    ['subject' => 'GECC 111', 'teacher' => 'MS. JIELYNE', 'room' => 'RM 101', 'section' => 'BEED-1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'ENGL 100', 'teacher' => 'MS. PAMA', 'room' => 'RM 103', 'section' => 'BEED-1B', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'GECC 112', 'teacher' => 'MS. CALDERON', 'room' => 'RM 407', 'section' => 'CRIM 1-A', 'dept' => 'CRIM'],
                    ['subject' => 'THEO 111', 'teacher' => 'MS. DELA CRUZ', 'room' => 'RM. 102', 'section' => 'CRIM 1-B', 'dept' => 'CRIM'],
                ],
                '1:30-3:00' => [
                    ['subject' => 'GECC 111', 'teacher' => 'MS. BATERNA', 'room' => 'RM. 307', 'section' => 'CRIM 1-A', 'dept' => 'CRIM'],
                    ['subject' => 'GECC 113', 'teacher' => 'MR. ANG', 'room' => 'RM IS F3', 'section' => 'BSME', 'dept' => 'OTHER COURSES'],
                ],
                '3:00-4:30' => [
                    ['subject' => 'ENGL 100', 'teacher' => 'MS. RODRIGUEZ', 'room' => 'RM 305', 'section' => 'BEED-1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'ENGL 100', 'teacher' => 'MS. PAMA', 'room' => 'IS G1', 'section' => 'CRIM 1-B', 'dept' => 'CRIM'],
                    ['subject' => 'THEO 111', 'teacher' => 'MS. DELA CRUZ', 'room' => 'RM 103', 'section' => 'CRIM1-C', 'dept' => 'CRIM'],
                ],
            ],
            'F' => [
                '7:30-9:00' => [
                    ['subject' => 'THEO 111', 'teacher' => 'MR. ANGELES', 'room' => 'RM 405', 'section' => 'BSIS 1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'PHED 213', 'teacher' => 'MS. PONELES', 'room' => 'IS FIELD', 'section' => 'BSME', 'dept' => 'OTHER COURSES'],
                ],
                '9:00-10:30' => [
                    ['subject' => 'PETC 111', 'teacher' => 'MR. GABUYA', 'room' => 'RM. 101', 'section' => 'BPED1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'THCC 111', 'teacher' => 'MR. ANDO', 'room' => 'RM 305', 'section' => 'BSTM 1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'CRIM 111', 'teacher' => 'MS. FERNANDEZ', 'room' => 'RM. 406', 'section' => 'CRIM 1F', 'dept' => 'CRIM'],
                ],
                '1:30-3:00' => [
                    ['subject' => 'GECC 216', 'teacher' => 'MS. TOLENTINO', 'room' => 'RM B102', 'section' => 'BEED-1A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'THEO 111', 'teacher' => 'MR. PEDROLA', 'room' => 'RM. 405', 'section' => 'CRIM 1F', 'dept' => 'CRIM'],
                    ['subject' => 'THEO 111', 'teacher' => 'MR. LIM', 'room' => 'RM 103', 'section' => 'CRIM 1H', 'dept' => 'CRIM'],
                ],
            ],
            'SAT' => [
                '8:00-10:00' => [
                    ['subject' => 'THEO 213', 'teacher' => 'MR. TOLENTINO', 'room' => 'RM 102', 'section' => 'BSTM 2B', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'ESCI 312', 'teacher' => 'DR. YORAC', 'room' => 'RM 101', 'section' => 'BEED', 'dept' => 'OTHER COURSES'],
                ],
                '10:00-12:00' => [
                    ['subject' => 'GECC 217', 'teacher' => 'MR. UBAMOS', 'room' => 'RM 208', 'section' => 'BSOA 2A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'MEHT 311', 'teacher' => 'ENGR. ESCOBAR', 'room' => 'RM. 306', 'section' => 'BSME', 'dept' => 'OTHER COURSES'],
                ],
                '1:00-2:00' => [
                    ['subject' => 'THEO 213', 'teacher' => 'MR. TOLENTINO', 'room' => 'RM 103', 'section' => 'BSTM 2A', 'dept' => 'OTHER COURSES'],
                    ['subject' => 'CHEM 211', 'teacher' => 'MS. DE JOSE', 'room' => 'RM 307', 'section' => 'CRIM 3A', 'dept' => 'CRIM'],
                ],
            ],
        ];
    }
};
