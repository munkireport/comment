<?php

use munkireport\models\MRModel as Eloquent;

class Comment_model extends Eloquent
{
    protected $table = 'comment';

    protected $fillable = [
        'serial_number',
        'section',
        'user',
        'text',
        'html',
        'timestamp',
    ];

    public $timestamps = false;
}
