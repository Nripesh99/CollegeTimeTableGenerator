<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use App\Events\TimeslotsUpdated;
use App\Services\TimeslotsService;

use App\Models\Day;
use App\Models\Timeslot;

class TimeslotsController extends Controller
{
    protected $service;

 
    public function __construct(TimeslotsService $service)
    {
        $this->middleware('auth');
        $this->middleware('activated');
        $this->service = $service;
    }

 
    public function index(Request $request)
    {
        $timeslots = $this->service->all([
            'keyword' => $request->has('keyword') ? $request->keyword : null,
            'order_by' => 'rank',
            'paginate' => 'true',
            'per_page' => 20
        ]);

        if ($request->ajax()) {
            return view('timeslots.table', compact('timeslots'));
        }

        return view('timeslots.index', compact('timeslots'));
    }

  
    public function store(Request $request)
    {
        $rules = [
            'from' => 'required|before:to',
            'to' => 'required|after:from'
        ];

        $messages = [
            'from.before' => 'From time must be before To time',
            'to.after' => 'To time must be after From time'
        ];

        $this->validate($request, $rules, $messages);

        $exists = Timeslot::where('time', Timeslot::createTimePeriod($request->from, $request->to))->first();

        if ($exists) {
            return response()->json(['errors' => ['This timeslot already exists']], 422);
        }

        $data = $request->all();
        $data['time'] = Timeslot::createTimePeriod($data['from'], $data['to']);

        $timeslots = Timeslot::all();

        foreach ($timeslots as $timeslot) {
            if ($timeslot->containsPeriod($data['time'])) {
                $errors = [$data['time'] . ' falls within another timeslot (' . $timeslot->time
                    . ').Please adjust timeslots'];
                return response()->json(['errors' => $errors], 422);
            }
        }

        $timeslot = $this->service->store($data);

        if ($timeslot) {
            event(new TimeslotsUpdated());
            return response()->json(['message' => 'Timeslot has been added'], 200);
        } else {
            return response()->json(['error' => 'A system error occurred'], 500);
        }
    }

    public function show($id)
    {
        $timeslot = Timeslot::find($id);

        if ($timeslot) {
            $timeParts = explode("-", $timeslot->time);
            $timeslot->from = trim($timeParts[0]);
            $timeslot->to = trim($timeParts[1]);

            return response()->json($timeslot, 200);
        } else {
            return response()->json(['error' => 'Timeslot not found'], 404);
        }
    }

  
    public function update($id, Request $request)
    {
        $timeslot = Timeslot::find($id);

        if (!$timeslot) {
            return response()->json(['errors' => ['Timeslot not found']], 404);
        }

        $rules = [
            'from' => 'required|before:to',
            'to' => 'required|after:from'
        ];

        $messages = [
            'from.before' => 'From time must be before To time',
            'to.after' => 'To time must be after From time'
        ];

        $this->validate($request, $rules, $messages);

        $exists = Timeslot::where('time', Timeslot::createTimePeriod($request->from, $request->to))
            ->where('id', '<>', $id)
            ->first();

        if ($exists) {
            return response()->json(['errors' => ['This timeslot already exists']], 422);
        }

        $data = $request->all();
        $data['time'] = Timeslot::createTimePeriod($data['from'], $data['to']);

        $timeslots = Timeslot::all();

        foreach ($timeslots as $timeslot) {
            if (($timeslot->id != $id) && $timeslot->containsPeriod($data['time'])) {
                $errors = [$data['time'] . ' falls within another timeslot (' . $timeslot->time
                    . ').Please adjust timeslots'];
                return response()->json(['errors' => $errors], 422);
            }
        }

        if ($this->service->update($id, $data)) {
            event(new TimeslotsUpdated());
            return response()->json(['message' => 'Timeslot updated'], 200);
        }

        return response()->json(['error' => 'A system error occurred'], 500);
    }

   
    public function destroy($id)
    {
        $timeslot = Timeslot::find($id);

        if (!$timeslot) {
            return response()->json(['error' => 'Timeslot not found'], 404);
        }

        if ($this->service->delete($id)) {
            event(new TimeslotsUpdated());
            return response()->json(['message' => 'Timeslot has been deleted'], 200);
        } else {
            return response()->json(['error' => 'An unknown system error occurred'], 500);
        }
    }
}
