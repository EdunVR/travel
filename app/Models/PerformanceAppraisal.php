<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceAppraisal extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'recruitment_id',
        'employee_name',
        'period',
        'appraisal_date',
        'discipline_score',
        'teamwork_score',
        'work_result_score',
        'initiative_score',
        'kpi_score',
        'total_score',
        'average_score',
        'grade',
        'evaluator_notes',
        'employee_notes',
        'improvement_plan',
        'status',
        'evaluator_id',
        'evaluated_at',
        'created_by',
    ];

    protected $casts = [
        'appraisal_date' => 'date',
        'evaluated_at' => 'datetime',
        'total_score' => 'decimal:2',
        'average_score' => 'decimal:2',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function employee()
    {
        return $this->belongsTo(Recruitment::class, 'recruitment_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Auto-calculate scores
    public function autoCalculate()
    {
        $scores = [
            $this->discipline_score,
            $this->teamwork_score,
            $this->work_result_score,
            $this->initiative_score,
            $this->kpi_score,
        ];

        $this->total_score = array_sum($scores);
        $this->average_score = round($this->total_score / 5, 2);
        $this->grade = $this->determineGrade($this->average_score);

        return $this;
    }

    // Determine grade based on average score
    private function determineGrade($average)
    {
        if ($average >= 90) return 'A';
        if ($average >= 80) return 'B';
        if ($average >= 70) return 'C';
        if ($average >= 60) return 'D';
        return 'E';
    }

    // Get grade label with color
    public function getGradeLabel()
    {
        $labels = [
            'A' => ['text' => 'Sangat Baik', 'color' => 'success'],
            'B' => ['text' => 'Baik', 'color' => 'info'],
            'C' => ['text' => 'Cukup', 'color' => 'warning'],
            'D' => ['text' => 'Kurang', 'color' => 'danger'],
            'E' => ['text' => 'Sangat Kurang', 'color' => 'dark'],
        ];

        return $labels[$this->grade] ?? ['text' => '-', 'color' => 'secondary'];
    }

    // Scopes
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    public function scopeByEmployee($query, $recruitmentId)
    {
        return $query->where('recruitment_id', $recruitmentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
