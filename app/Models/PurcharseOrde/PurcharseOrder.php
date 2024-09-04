<?php

namespace App\Models\PurcharseOrde;

use App\Models\PaymentMethods\PaymentMethod;
use App\Models\Suppliers\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurcharseOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'purchase_order';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_method_id',
        'project_id',
        'client_id',
        'budget_concept_id',
        'supplier_id',
        'amount',
        'units',
        'quantity',
        'bank_id',
        'shipping_date',
        'sent',
        'note',
    ];
    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function concepto() {
        return $this->belongsTo(\App\Models\Budgets\BudgetConcept::class,'budget_concept_id');
    }
    public function payMethod() {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }
    public function Proveedor() {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
}
