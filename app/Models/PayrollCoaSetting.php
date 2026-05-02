<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollCoaSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'salary_expense_account_id',
        'overtime_expense_account_id',
        'bonus_expense_account_id',
        'allowance_expense_account_id',
        'tax_payable_account_id',
        'loan_receivable_account_id',
        'salary_payable_account_id',
        'cash_account_id',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function salaryExpenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'salary_expense_account_id');
    }

    public function overtimeExpenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'overtime_expense_account_id');
    }

    public function bonusExpenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'bonus_expense_account_id');
    }

    public function allowanceExpenseAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'allowance_expense_account_id');
    }

    public function taxPayableAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'tax_payable_account_id');
    }

    public function loanReceivableAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'loan_receivable_account_id');
    }

    public function salaryPayableAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'salary_payable_account_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'cash_account_id');
    }
}
