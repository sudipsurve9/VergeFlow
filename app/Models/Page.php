<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     * Pages are global and should always use the main database.
     *
     * @var string
     */
    protected $connection = 'main';
}
