<?php

namespace App\Http\Controllers;

use App\Models\DesignMaterial;
use App\Models\TravelPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\HasOutletFilter;

class DesignMaterialController extends Controller
{
    use HasOutletFilter;
    
    public function __construct()
    {
        $this->middleware('permission:travel.design-material.view')->only(['index', 'show']);
        $this->middleware('permission:travel.design-material.create')->only(['store', 'upload']);
        $this->middleware('permission:travel.design-material.update')->only(['update', 'markComplete']);
        $this->middleware('permission:travel.design-material.delete')->only(['destroy']);
    }
    
    /**
     * Display materials for a package
     */
    public function index($packageId)
    {
        $package = TravelPackage::findOrFail($packageId);
        $materials = DesignMaterial::forPackage($packageId)
            ->orderBy('material_type')
            ->orderBy('version', 'desc')
            ->get()
            ->groupBy('material_type');

        return view('admin.travel.design-materials.index', compact('package', 'materials'));
    }

    /**
     * Upload a design material
     */
    public function upload(Request $request, $packageId)
    {
        $package = TravelPackage::findOrFail($packageId);

        // Validate request
        $validator = $this->validateUpload($request);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file upload
            $file = $request->file('file');
            $materialType = $request->input('material_type');
            
            // Generate file path
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs(
                'design-materials/' . $packageId . '/' . $materialType,
                $fileName,
                'public'
            );

            // Get current version for this material type
            $currentVersion = DesignMaterial::forPackage($packageId)
                ->ofType($materialType)
                ->max('version') ?? 0;

            // Create design material record
            $material = DesignMaterial::create([
                'id_travel_package' => $packageId,
                'material_type' => $materialType,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'version' => $currentVersion + 1,
                'is_complete' => $request->input('is_complete', false),
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
                'notes' => $request->input('notes')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Material uploaded successfully',
                'data' => $material
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload material: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update material details
     */
    public function update(Request $request, $id)
    {
        $material = DesignMaterial::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'is_complete' => 'sometimes|boolean',
            'notes' => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $material->update($request->only(['is_complete', 'notes']));

            return response()->json([
                'success' => true,
                'message' => 'Material updated successfully',
                'data' => $material
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update material: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark material as complete
     */
    public function markComplete($id)
    {
        $material = DesignMaterial::findOrFail($id);

        try {
            $material->markComplete();

            return response()->json([
                'success' => true,
                'message' => 'Material marked as complete'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark material as complete: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark material as incomplete
     */
    public function markIncomplete($id)
    {
        $material = DesignMaterial::findOrFail($id);

        try {
            $material->markIncomplete();

            return response()->json([
                'success' => true,
                'message' => 'Material marked as incomplete'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark material as incomplete: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a material
     */
    public function destroy($id)
    {
        $material = DesignMaterial::findOrFail($id);

        try {
            // Delete file from storage
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }

            $material->delete();

            return response()->json([
                'success' => true,
                'message' => 'Material deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete material: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a material
     */
    public function download($id)
    {
        $material = DesignMaterial::findOrFail($id);

        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($material->file_path, $material->file_name);
    }

    /**
     * Get materials for a package (API endpoint)
     */
    public function getMaterials($packageId)
    {
        $materials = DesignMaterial::forPackage($packageId)
            ->with('uploader')
            ->orderBy('material_type')
            ->orderBy('version', 'desc')
            ->get()
            ->groupBy('material_type');

        // Get completion status
        $completionStatus = [];
        foreach (DesignMaterial::MATERIAL_TYPES as $type => $label) {
            $typeMaterials = $materials->get($type, collect());
            $completionStatus[$type] = [
                'label' => $label,
                'has_material' => $typeMaterials->isNotEmpty(),
                'is_complete' => $typeMaterials->where('is_complete', true)->isNotEmpty(),
                'latest_version' => $typeMaterials->first()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'materials' => $materials,
                'completion_status' => $completionStatus,
                'all_complete' => collect($completionStatus)->every(fn($status) => $status['is_complete'])
            ]
        ]);
    }

    /**
     * Validate file upload
     */
    private function validateUpload(Request $request)
    {
        $rules = [
            'material_type' => 'required|in:flyer,itinerary,promotional_video,package_information',
            'file' => 'required|file|max:102400', // 100MB max
            'is_complete' => 'sometimes|boolean',
            'notes' => 'nullable|string'
        ];

        // Add specific validation based on material type
        $materialType = $request->input('material_type');
        
        if ($materialType === 'flyer') {
            // Flyer must be image or PDF
            $rules['file'] .= '|mimes:jpg,jpeg,png,pdf';
        } elseif ($materialType === 'promotional_video') {
            // Video must be video format
            $rules['file'] .= '|mimes:mp4,avi,mov,wmv,flv,webm';
        } elseif ($materialType === 'itinerary' || $materialType === 'package_information') {
            // Documents can be PDF, Word, or images
            $rules['file'] .= '|mimes:pdf,doc,docx,jpg,jpeg,png';
        }

        return Validator::make($request->all(), $rules, [
            'file.mimes' => 'The file format is not valid for this material type.',
            'file.max' => 'The file size must not exceed 100MB.'
        ]);
    }
}
