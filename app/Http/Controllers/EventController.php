<?php

namespace App\Http\Controllers;

use App\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.events.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name' => 'required',
            'address_1' => 'required',
            'city' => 'required',
            'date' => 'required|date|after:yesterday',
            'time' => 'required|date_format:g:ia',
        ]);

        $event = Auth::user()->events()->create([
            'name' => request('name'),
            'address_1' => request('address_1'),
            'address_2' => request('address_2'),
            'address_3' => request('address_3'),
            'city' => request('city'),
            'date' => Carbon::parse(vsprintf('%s %s', [  // vsprintf() - Returns a formatted string
                request('date'),
                request('time')
            ])),
        ]);

        return redirect('/admin/events');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', ['event' => $event]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Event $event)
    {
        $this->validate(request(), [
            'name' => 'required',
            'address_1' => 'required',
            'city' => 'required',
            'date' => 'required|date|after:yesterday',
            'time' => 'required|date_format:g:ia',
        ]);

        $event->update([
            'name' => request('name'),
            'address_1' => request('address_1'),
            'address_2' => request('address_2'),
            'address_3' => request('address_3'),
            'city' => request('city'),
            'date' => Carbon::parse(vsprintf('%s %s', [  // vsprintf() - Returns a formatted string
                request('date'),
                request('time')
            ])),
        ]);

        return redirect()->route('admin.events.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
