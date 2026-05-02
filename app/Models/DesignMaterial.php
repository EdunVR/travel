<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_travel_package',
        'material_type',
        'file_path',
        'file_name',
        'version',
        'is_complete',
        'uploaded_by',
        'uploaded_at',
        'notes'
    ];

    protected $casts = [
        'is_complete' => 'boolean',
        'uploaded_at' => 'datetime',
        'version' => 'integer'
    ];

    /**
     * Material types available
     */
    const MATERIAL_TYPES = [
        'flyer' => 'Flyer',
        'itinerary' => 'Itinerary',
        'promotional_video' => 'Promotional Video',
        'package_information' => 'Package Information'
    ];

    /**
     * Get the travel package that owns the design material
     */
    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'id_travel_package');
    }

    /**
     * Get the user who uploaded the material
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Mark material as complete
     */
    public function markComplete()
    {
        $this->is_complete = true;
        $this->save();
    }

    /**
     * Mark material as incomplete
     */
    public function markIncomplete()
    {
        $this->is_complete = false;
        $this->save();
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    /**
     * Get the material type label
     */
    public function getMaterialTypeLabelAttribute()
    {
        return self::MATERIAL_TYPES[$this->material_type] ?? $this->material_type;
    }

    /**
     * Check if file is an image
     */
    public function isImage()
    {
        if (!$this->file_path) {
            return false;
        }
        
        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Check if file is a video
     */
    public function isVideo()
    {
        if (!$this->file_path) {
            return false;
        }
        
        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm']);
    }

    /**
     * Check if file is a PDF
     */
    public function isPdf()
    {
        if (!$this->file_path) {
            return false;
        }
        
        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return $extension === 'pdf';
    }

    /**
     * Scope to filter by package
     */
    public function scopeForPackage($query, $packageId)
    {
        return $query->where('id_travel_package', $packageId);
    }

    /**
     * Scope to filter by material type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('material_type', $type);
    }

    /**
     * Scope to filter completed materials
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_complete', true);
    }

    /**
     * Scope to filter incomplete materials
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_complete', false);
    }
}
