<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\InvestorCustomer;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Investor extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $guard = 'investor';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'jenis_kelamin',
        'category',
        'status',
        'join_date',
        'initial_investment',
        'total_investment',
        'last_profit_share',
        'last_profit_share_date',
        'address',
        'notes',
        'photo',
        'tempo', 
        'bank',
        'rekening', 
        'atas_nama',
    ];

    protected $dates = [
        'join_date',
        'last_profit_share_date',
        'deleted_at'
    ];

    protected $casts = [
        'initial_investment' => 'decimal:2',
        'total_investment' => 'decimal:2',
        'last_profit_share' => 'decimal:2',
        'join_date' => 'date'
    ];

    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['photo'] 
                ? asset('storage/'.$attributes['photo']) 
                : asset('images/default-user.png')
        );
    }

    public function accounts()
    {
        return $this->hasMany(InvestorAccount::class);
    }

    public function documents()
    {
        return $this->hasMany(InvestorDocument::class);
    }

    public function getTotalInvestmentAttribute()
    {
        return $this->accounts->sum('total_investment');
    }

    public function getTotalProfitBalanceAttribute()
    {
        return $this->accounts->sum('profit_balance');
    }

    public function customers()
    {
        return $this->hasMany(InvestorCustomer::class);
    }

    // Method untuk menghitung jumlah customer yang sudah bayar
    public function getPaidCustomersCountAttribute()
    {
        return $this->customers()->where('status', 'paid')->count();
    }

    // Method untuk menghitung total keuntungan
    public function getTotalProfitAttribute()
    {
        return $this->estimasi_keuntungan * $this->paid_customers_count;
    }

    // Method untuk menghitung bagi hasil
    public function getProfitShareAttribute()
    {
        $averagePercentage = $this->accounts()->active()->avg('profit_percentage');
        return $this->total_profit * ($averagePercentage / 100);
    }

    // Method untuk menghitung keuntungan pengelola
    public function getManagementProfitAttribute()
    {
        return $this->total_profit - $this->profit_share;
    }

    // Method untuk menghitung total keseluruhan
    public function getTotalAmountAttribute()
    {
        return $this->total_investment + $this->profit_share;
    }

    // Method untuk menghitung total transfer ke investor
    public function getTotalWithdrawalAttribute()
    {
        return $this->accounts->sum(function($account) {
            return $account->investments()->where('type', 'withdrawal')->sum('amount');
        });
    }

    // Method untuk menghitung tenor
    public function getTenorAttribute()
    {
        $latestAccount = $this->accounts()->active()->latest()->first();
        if (!$latestAccount || !$this->tempo) {
            return null;
        }
        
        // Pastikan kita menggunakan objek Carbon
        $tempoDate = \Carbon\Carbon::parse($this->tempo);
        $accountDate = \Carbon\Carbon::parse($latestAccount->created_at);
        
        $diff = $tempoDate->diff($accountDate);
        
        $months = $diff->m;
        $days = $diff->d;
        
        $result = [];
        if ($months > 0) {
            $result[] = $months . ' bulan';
        }
        if ($days > 0) {
            $result[] = $days . ' hari';
        }
        
        return implode(' ', $result) ?: '-';
    }

    // Method untuk menghitung persentase rata-rata
    public function getAveragePercentageAttribute()
    {
        return $this->accounts()->active()->avg('profit_percentage');
    }

    public function findForPassport($login)
    {
        return $this->where('email', $login)
                    ->orWhere('phone', $login)
                    ->first();
    }

    public function getAvailableBalanceAttribute()
    {
        // Total semua bagi hasil dari semua akun
        $totalProfit = $this->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'deposit')
                        ->sum('amount');
        });

        // Total semua pencairan yang disetujui dari semua akun
        $totalWithdrawals = $this->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'withdrawal')
                        ->sum('amount');
        });

        return $totalProfit - $totalWithdrawals;
    }

    public function getTotalKeuntunganAttribute()
    {
        $totalWithdrawals = $this->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'deposit')
                        ->sum('amount');
        });

        return $totalWithdrawals;
    }

    public function profitDistributions()
    {
        return $this->hasManyThrough(
            AccountInvestment::class,
            InvestorAccount::class,
            'investor_id',
            'account_id'
        )->where('type', 'deposit');
    }

    public function withdrawals()
    {
        return $this->hasManyThrough(InvestorWithdrawal::class, InvestorAccount::class);
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/'.$this->photo) : asset('img/logo.png');
    }
}