<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'recruitment_id',
        'period',
        'payment_date',
        'basic_salary',
        'working_days',
        'present_days',
        'absent_days',
        'late_days',
        'overtime_hours',
        'overtime_pay',
        'bonus',
        'allowance',
        'deduction',
        'late_penalty',
        'absent_penalty',
        'loan_deduction',
        'tax',
        'gross_salary',
        'net_salary',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function employee()
    {
        return $this->belongsTo(Recruitment::class, 'recruitment_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Calculate gross salary
    public function calculateGrossSalary()
    {
        return $this->basic_salary + $this->overtime_pay + $this->bonus + $this->allowance;
    }

    // Calculate total deductions
    public function calculateTotalDeductions()
    {
        return $this->deduction + $this->late_penalty + $this->absent_penalty + $this->loan_deduction + $this->tax;
    }

    // Calculate net salary
    public function calculateNetSalary()
    {
        return $this->calculateGrossSalary() - $this->calculateTotalDeductions();
    }

    // Auto calculate and save
    public function autoCalculate()
    {
        $this->gross_salary = $this->calculateGrossSalary();
        $this->net_salary = $this->calculateNetSalary();
        return $this;
    }
}
