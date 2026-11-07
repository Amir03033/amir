<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

/** Receives navigator.sendBeacon() clicks from the site (booking and outbound links). */
class EventController extends Controller
{
    public function __invoke(Request $request)
    {
        if (! (new CrawlerDetect)->isCrawler((string) $request->userAgent())) {
            Event::record((string) $request->input('name'), $request->input('label'));
        }

        return response()->noContent();
    }
}
