<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['amount', 'type', 'category', 'payment_type', 'date'];

    public function subTransactions()
    {
        return $this->hasMany(SubTransaction::class);
    }
}