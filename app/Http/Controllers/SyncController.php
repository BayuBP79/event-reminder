<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function syncEvents(Request $request)
    {
        $events = $request->json()->all();

        foreach ($events as $eventData) {
            Event::updateOrCreate(
                ['id' => $eventData['id']],
                $eventData
            );
        }

        return response()->json(['message' => 'Events synced successfully']);
    }

}
