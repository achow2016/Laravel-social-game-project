<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VisitorRecord extends Model{
	
	protected $table = 'visitor_records';
	protected $primaryKey = 'id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'ip_address'
    ];
	
	public function guestBookNote()
	{
		return $this->hasOne('App\Models\GuestBookNote', 'visitor_id', 'id');
	}
}