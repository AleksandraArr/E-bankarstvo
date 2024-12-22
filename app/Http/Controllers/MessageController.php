<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\MessageResource;

class MessageController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
    
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $message = Message::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => $user->id, 
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Message created successfully!', 'data' => $message]);
    }

    public function showForUser()
    {
        $user = Auth::user();
        $messages = Message::where('user_id', $user->id)->get();
        return response()->json(['messages' => MessageResource::collection($messages)]);
    }

    public function index()
    {
        $messages = Message::all();
        return response()->json([
            'messages' => MessageResource::collection($messages)
        ]);
    }

    public function showUnsolved(){
        $messages = Message::where('status', 'pending')->get();
        return response()->json(['messages' => MessageResource::collection($messages)]);
    }

    public function markAsSolved(Request $request, Message $message)
    {
        $user = Auth::user();

        $message->update([
            'reviewed_by' => $user->id,
            'status' => 'solved',
        ]);

        return response()->json(['message' => 'Message marked as solved!', 'data' => new MessageResource($message)]);
    }
}
