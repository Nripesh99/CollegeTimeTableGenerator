<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use App\Services\CoursesService;

use App\Models\Course;
use App\Models\Professor;

class CoursesController extends Controller
{
    
    protected $service;

    public function __construct(CoursesService $service)
    {
        $this->middleware('auth');
        $this->middleware('activated');
        $this->service = $service;
    }

  
    public function index(Request $request)
    {
        $courses = $this->service->all([
            'keyword' => $request->has('keyword') ? $request->keyword : null,
            'filter' => $request->has('filter') ? $request->filter : null,
            'order_by' => 'course_code',
            'paginate' => 'true',
            'per_page' => 15
        ]);

        $professors = Professor::all();

        if ($request->ajax()) {
            return view('courses.table', compact('courses'));
        }

        return view('courses.index', compact('courses', 'professors'));
    }

    
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'course_code' => 'required|unique:courses,course_code',
        ];

        $messages = [
            'name.unique' => 'This course already exists',
        ];

        $this->validate($request, $rules, $messages);

        $course = $this->service->store($request->all());

        if ($course) {
            return response()->json(['message' => 'Course added'], 200);
        } else {
            return response()->json(['error' => 'A system error occurred'], 500);
        }
    }

 
    public function show($id, Request $request)
    {
        $course = $this->service->show($id);

        if ($course) {
            return response()->json($course, 200);
        } else {
            return response()->json(['error' => 'Course not found'], 404);
        }
    }

   
    public function update($id, Request $request)
    {
        $rules = [
            'name' => 'required',
            'course_code' => 'required|unique:courses,course_code,' . $id
        ];

        $messages = [
            'name.unique' => 'This course already exists'
        ];

        $this->validate($request, $rules, $messages);

        $course = $this->service->show($id);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        $course = $this->service->update($id, $request->all());

        return response()->json(['message' => 'Course updated'], 200);
    }

   
    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        if ($this->service->delete($id)) {
            return response()->json(['message' => 'Course has been deleted'], 200);
        } else {
            return response()->json(['error' => 'An unknown system error occurred'], 500);
        }
    }
}
