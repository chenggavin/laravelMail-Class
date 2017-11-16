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
        $inboxTrash = \Auth::user()->inboxTrash()->get();
        $sentTrash = \Auth::user()->sentTrash()->get();
        return view('messages.trash', compact('inboxTrash', 'sentTrash', 'title'));
    }

    public function sent() {
        $title = "Sent";
        $messages = \Auth::user()->sent()->get();
        foreach ($messages as $message) {
            $message->link = '/messages/' . $message->id; 
        }
        return view('messages.from', compact('messages', 'title'));
    }

    public function drafts() {
        $title = "Drafts";
        $messages = \Auth::user()->drafts()->get();
        foreach ($messages as $message) {
            $message->link = '/messages/' . $message->id . '/edit'; 
        }
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

        // Ack! Deleted records don't show!
        
        if ( \Auth::user()->sent->contains($id) ) {

            // The logged-in user sent the message

            $message = \App\Message::find($id);
            $show_star = true;
            $star_class = '';
            $trash_class = '';

            if ( \Auth::user()->received->contains($id) ) {
                $message->recipients()->updateExistingPivot(\Auth::user()->id, ['is_read' => true]);

                $recipient = $message->recipients->find(\Auth::user()->id);
                if ($recipient->pivot->is_starred) {
                    $star_class = 'starred';
                }

            }
            else {
                $show_star = false;
            }

            return view('messages.show', compact('message', 'show_star', 'star_class', 'trash_class'));

        }
        else if ( \Auth::user()->received->contains($id) ) {

            // The logged-in user received the message

            $message = \App\Message::find($id);
            $message->recipients()->updateExistingPivot(\Auth::user()->id, ['is_read' => true]);
            $show_star = true;
            $star_class = '';
            $trash_class = '';

            $recipient = $message->recipients->find(\Auth::user()->id);
            if ($recipient->pivot->is_starred) {
                $star_class = 'starred';
            }

            return view('messages.show', compact('message', 'show_star', 'star_class', 'trash_class'));

        }
        else if ( \Auth::user()->drafts->contains($id) ) {

            // The logged-in user is writing the message

            $message = \App\Message::find($id);
            return view('messages.edit', compact('message'));

        }
        else if ( \Auth::user()->received->contains($id) == false ) {
             $message = \Auth::user()->inboxTrash()->orderBy('id', 'desc')->get();
             $message = \App\Message::find($id);
             $show_star = false;

             return view('messages.show', compact('message', 'show_star'));
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
        //
        return "I should be saving an existing message now";
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

        $sentMessage = \App\Message::find($id);

        if ($sentMessage->is_deleted == false) {
            $sentMessage->is_deleted = true;
        }

        else {
            $sentMessage->is_deleted = false;
        }




        $sentMessage->save();


        $test = $message->recipients()->first()->pivot->deleted_at;


        if ($test === null) {

        return 'it is null';
            $message->recipients()->updateExistingPivot(\Auth::user()->id, ['deleted_at' => Carbon::now()]);
        
        }
        else {
            $message->recipients()->updateExistingPivot(\Auth::user()->id, ['deleted_at' => null]);
        }
        $message->save();

        return redirect('/messages');
    }

    public function star($id) 
    {
        $message = \App\Message::find($id);
        $recipient = $message->recipients->find(\Auth::user()->id);
        $message->recipients()->updateExistingPivot(\Auth::user()->id, ['is_starred' => !$recipient->pivot->is_starred]);
        return redirect('/messages/' . $id);

    }

}
