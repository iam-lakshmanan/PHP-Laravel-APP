<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalKeyDocument extends Model
{
    use HasFactory;

    protected $table = 'digital_key_documents';
    protected $primaryKey = 'document_id';
    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'fid',
        'title',
        'status',
        'created',
        'uid',
        'box_file_id',
        'box_file_url',
        'box_status',
        'box_uploaded',
        'editable_files',
        'printable',
    ];

    protected $casts = [
        'editable_files' => 'boolean',
        'printable' => 'boolean',
    ];
}
