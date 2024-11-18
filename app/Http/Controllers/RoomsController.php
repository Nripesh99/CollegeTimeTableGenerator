<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;

use App\Models\Room;
use App\Services\RoomsService;

class RoomsController extends Controller
{
    
    protected $service;

  
    public function __construct(RoomsService $service)
    {
        $this->middleware('auth');
        $this->middleware('activated');
        $this->service = $service;
    }

  
    public function index(Request $request)
    {
        $rooms = $this->service->all([
            'keyword' => $request->has('keyword') ? $request->keyword : null,
            'order_by' => 'name',
            'paginate' => 'true',
            'per_page' => 20
        ]);

        if ($request->ajax()) {
            return view('rooms.table', compact('rooms'));
        }

        return view('rooms.index', compact('rooms'));
    }

   
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:rooms,name',
            'capacity' => 'required|numeric'
        ];

        $messages = [
            'name.unique' => 'This room already exists'
        ];

        $this->validate($request, $rules, $messages);

        $room = $this->service->store($request->all());

        if ($room) {
            return response()->json(['message' => 'Room added'], 200);
        } else {
            return response()->json(['error' => 'A system error occurred'], 500);
        }
    }

    
    public function show($id, Request $request)
    {
        $room = Room::find($id);

        if ($room) {
            return response()->json($room, 200);
        } else {
            return response()->json(['error' => 'Room not found'], 404);
        }
    }

    
    public function update($id, Request $request)
    {
        $rules = [
            'name' => 'required|unique:rooms,name,' . $id,
            'capacity' => 'required|numeric'
        ];

        $messages = [
            'name.unique' => 'This room already exists'
        ];

        $this->validate($request, $rules, $messages);

        $room = $this->service->show($id);

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        $room = $this->service->update($id, $request->all());

        return response()->json(['message' => 'Room updated'], 200);
    }

    public function destroy($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['error' => 'Room not found'], 404);
        }

        if ($this->service->delete($id)) {
            return response()->json(['message' => 'Room has been deleted'], 200);
        } else {
            return response()->json(['error' => 'An unknown system error occurred'], 500);
        }
    }
}
