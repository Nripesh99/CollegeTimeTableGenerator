<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CollegeClassesService;

use App\Models\Room;
use App\Models\Course;
use App\Models\CollegeClass;
use App\Models\AcademicPeriod;

class CollegeClassesController extends Controller
{
 
    protected $service;

    public function __construct(CollegeClassesService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->middleware('activated');
    }

   
    public function index(Request $request)
    {
        $classes = $this->service->all([
            'keyword' => $request->has('keyword') ? $request->keyword : null,
            'filter' => $request->has('filter') ? $request->filter : null,
            'order_by' => 'name',
            'paginate' => 'true',
            'per_page' => 20
        ]);

        $rooms = Room::all();
        $courses = Course::all();
        $academicPeriods = AcademicPeriod::all();


        if ($request->ajax()) {
            return view('classes.table', compact('classes', 'academicPeriods'));
        }

        return view('classes.index', compact('classes', 'rooms', 'courses', 'academicPeriods'));
    }

   
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:classes',
            'size' => 'required'
        ];

        $this->validate($request, $rules);

        $class = $this->service->store($request->all());

        if ($class) {
            return response()->json(['message' => 'Class added'], 200);
        } else {
            return response()->json(['error' => 'A system error occurred'], 500);
        }
    }

   
    public function show($id)
    {
        $class = $this->service->show($id);

        if ($class) {
            return response()->json($class, 200);
        } else {
            return response()->json(['error' => 'Class not found'], 404);
        }
    }

    
    public function update($id, Request $request)
    {
        $rules = [
            'name' => 'required|unique:classes,name,' . $id,
            'size' => 'required'
        ];

        $this->validate($request, $rules);

        $class = CollegeClass::find($id);

        if (!$class) {
            return response()->json(['error' => 'Class not found'], 404);
        }

        $class = $this->service->update($id, $request->all());

        return response()->json(['message' => 'Class updated'], 200);
    }

  
    public function destroy($id)
    {
        $class = CollegeClass::find($id);

        if (!$class) {
            return response()->json(['error' => 'Class not found'], 404);
        }

        if ($this->service->delete($id)) {
            return response()->json(['message' => 'Class has been deleted'], 200);
        } else {
            return response()->json(['error' => 'An unknown system error occurred'], 500);
        }
    }
}
