<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use League\Flysystem\Util;

use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.event.index', [
            'events' => User::find(Auth::user()->id)->events()->paginate(6)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return view('admin.event.show', [
            'event' => $event
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.event.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'registration_fee' => 'required|numeric|max:999.99',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after:start_date'
        ]);
        $user = User::findOrFail($request->user()->id);
        $event = $user->events()->create([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'registration_fee' => $request->registration_fee,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
        $name = Util::normalizePath($event->id . 'image.' . $request->file('image')->getClientOriginalExtension());
        $path = $request->file('image')->storeAs('public/event/' . $event->id, $name);
        $event->images()->create([
            'name' => $name,
            'path' => $path
        ]);
        return redirect()->route('events.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        return view('admin.event.edit', [
            'event' => $event
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'registration_fee' => 'required|numeric',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after:start_date'
        ]);

        $event->update([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'registration_fee' => $request->registration_fee,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
        if($request->file('image')) {
            $event->images()->delete();
            Storage::deleteDirectory('public/event/' . $event->id);
            $name = Util::normalizePath($event->id . 'image.' . $request->file('image')->getClientOriginalExtension());
            $path = $request->file('image')->storeAs('public/event/' . $event->id, $name);
            $event->images()->create([
                'name' => $name,
                'path' => $path
            ]);
        }
        return redirect()->route('events.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $event->delete();
        $event->images()->delete();
        Storage::deleteDirectory('public/event' . $event->id);
        return redirect()->route('events.index');
    }

    /**
     * Search for event
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        // Eventos atuais (data não passou)
        //$events = Event::where('end_date', '<=', date('Y-m-d'))->where('name', 'like', '%' . $request->search . '%')->get();
        $search = $request->query('event');
        $events = Event::where('name', 'like', '%' . $search . '%')->get();
        return view('public.events.search', [
            'events' => $events,
            'search' => $search
        ]);
    }

    /**
     * Show event details - public
     */
    public function detail(Event $event)
    {
        return view('public.events.detail', [
            'event' => $event
        ]);
    }

    /**
     * Show subscription page - public
     */
    public function subscribe(Event $event)
    {
        return view('public.events.subscribe', [
            'event' => $event
        ]);
    }
}