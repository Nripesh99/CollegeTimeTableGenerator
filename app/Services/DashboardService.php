<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Course;
use App\Models\Professor;
use App\Models\CollegeClass;

class DashboardService
{
    /**
     * Get data for display on the dashboard
     *
     * @return array Data
     */
    public function getData()
    {
        $roomsCount = Room::count();
        $coursesCount = Course::count();
        $professorsCount = Professor::count();
        $classesCount = CollegeClass::count();

        $data = [
            'cards' => [
                [
                    'title' => 'Lecture Rooms',
                    'icon' => 'home',
                    'value' => $roomsCount,
                    'route' => route('rooms.index'),
                ],
                [
                    'title' => 'Courses',
                    'icon' => 'book',
                    'value' => $coursesCount,
                    'route' => route('courses.index'),
                ],
                [
                    'title' => 'Professors',
                    'icon' => 'graduation-cap',
                    'value' => $professorsCount,
                    'route' => route('professors.index'),
                ],
                [
                    'title' => 'Classes',
                    'icon' => 'users',
                    'value' => $classesCount,
                    'route' => route('classes.index'),
                ]
            ]
        ];

        return $data;
    }
}