<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // Table Name
    // Changing the default table name post to posts
    protected $table = 'posts';
    // Primary Key
    public $primarykey = 'id';
    // Timestamps
    public $timestamps = true;


    public function user(){
    /*a post belongs to a user*/ 
        return $this->belongsTo('App\User');
    }
}
