<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GuestBookNote extends Model{
	
	protected $table = 'visitor_guestbook_notes';
	protected $primaryKey = 'id';
	protected $dateFormat = 'dd/mm/yyyy';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'name', 'note', 'date', 'email', 'message'
    ];
	
	
}