<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
//use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\VisitorRecord;
use App\Models\GuestBookNote;

use DateTime;

//use App\Mail\Welcome;
use Illuminate\Support\Facades\Mail;

class GuestbookController extends Controller
{
	
	//registers guest visit to guestbook
	public function recordGuest(Request $request)
	{	
		try {
			$ipAddress = $request->ip();
			$visitorRecord = new VisitorRecord();
			$visitorRecord->setAttribute('ip_address', $ipAddress);
			$visitorRecord->save();
			$guestCount = VisitorRecord::count();
			return response(['guestName' => $visitorRecord->ip_address, 'guestCount' => $guestCount], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['error' => 'Record could not be created. Please report to admin.'], 422);
		}	
	}
	
	//gets guestbook notes
	public function getGuestbookNotes(Request $request)
	{	
		try {
			$guestBookNotes = GuestBookNote::all();	
			return response(['guestBookNotes' => $guestBookNotes], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['error' => 'Record could not be retrieved. Please report to admin.'], 422);
		}	
	}
	
	//make guestbook note
	public function newGuestbookNote(Request $request)
	{	
		try {
			$guestBookNote = new GuestBookNote();
			$guestBookNote->setAttribute('name', $request->name);
			$guestBookNote->setAttribute('note', $request->note);
			$guestBookNote->setAttribute('email', $request->name);			
			$guestBookNote->save();
			return response(['message' => 'Guestbook record added.'], 200);
		}
		catch(Throwable $e) {
			report($e);
			return response(['error' => 'Record could not be saved. Please report to admin.'], 422);
		}	
	}
}