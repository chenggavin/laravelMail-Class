<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

class MessageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Inbox";
        $messages = \Auth::user()->received()->get();
        return view('messages.to', compact('messages', 'title'));
    }

    public function starred() {
        $title = "Starred";
        $messages = \Auth::user()->starred()->get();
        return view('messages.to', compact('messages', 'title'));
    }

    public function trash() {
        $title = "Trash";
        $messages = \Auth::user()->trash()->get();
        return view('messages.to', compact('messages', 'title'));
    }

    public function sent() {
        $title = "Sent";
        $messages = \Auth::user()->sent()->get();
        return view('messages.from', compact('messages', 'title'));
    }

    public function drafts() {
        $title = "Drafts";
        $messages = \Auth::user()->drafts()->get();
        return view('messages.from', compact('messages', 'title'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $recipients = \App\User::all();
        return view('messages.create', compact('recipients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $message = new \App\Message;

        $message->sender_id = \Auth::user()->id;
        $message->subject = $request->input('subject');
        $message->body = $request->input('body');

        if ($request->input('button') === 'send') {
            $message->sent_at = Carbon::now();
        }

        $message->save();

        $message->recipients()->sync($request->input('recipients'));

        return redirect('/messages');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        if ( \Auth::user()->sent->contains($id) || \Auth::user()->received->contains($id)) {

            $message = \App\Message::find($id);
            $message->recipients()->sync([\Auth::user()->id => ['is_read' => true]]);
            return view('messages.show', compact('message'));

        }
        else if ( \Auth::user()->drafts->contains($id) ) {

            $message = \App\Message::find($id);
            return view('messages.edit', compact('message'));

        }
        else {
            return redirect('/messages');
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        return view('messages.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $star = \Auth::user();
        if (collect($star->received()->where([
            ['is_starred','=', false],
            ['message_id', '=', $id]])->get())->isEmpty() === false ) {

            $star->received()->where([
            ['is_starred','=', false],
            ['message_id', '=', $id]])->updateExistingPivot($id, ['is_starred'=> true]);
        }

        else {
            $star->received()->where([
                ['is_starred','=', true],
                ['message_id', '=', $id]])->updateExistingPivot($id, ['is_starred'=> false]);
        }

        $star->save();
        $user = \Auth::user()->received()->get();  
        $message = \App\Message::find($id);
        $message->recipients()->sync([\Auth::user()->id => ['is_read' => true]]);      
        return view('/messages.show', compact('star', 'message'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = \App\Message::find($id);
        $message->recipients()->sync([\Auth::user()->id => ['deleted_at' => Carbon::now()]]);
        return redirect('/messages');
    }

}