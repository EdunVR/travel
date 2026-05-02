<?php

namespace App\Services;

use App\Models\TravelPackage;
use App\Models\WorkflowStage;
use App\Models\WorkflowHistory;
use App\Models\WorkflowTask;
use App\Models\HppCalculation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class WorkflowService
{
    protected $notificationService;
    protected $auditService;

    public function __construct(NotificationService $notificationService, AuditService $auditService)
    {
        $this->notificationService = $notificationService;
        $this->auditService = $auditService;
    }
    /**
     * Transition a package to the next workflow stage
     * 
     * @param TravelPackage $package
     * @param string $toStageCode
     * @param string|null $notes
     * @return array
     */
    public function transitionToStage(TravelPackage $package, string $toStageCode, ?string $notes = null)
    {
        DB::beginTransaction();
        
        try {
            // Get current and target stages
            $currentStage = WorkflowStage::where('stage_code', $package->current_workflow_stage)->first();
            $targetStage = WorkflowStage::where('stage_code', $toStageCode)->first();
            
            if (!$targetStage) {
                throw new Exception("Target workflow stage '{$toStageCode}' not found");
            }
            
            // Validate stage requirements before transition
            $validation = $this->validateStageRequirements($package, $currentStage);
            if (!$validation['valid']) {
                throw new Exception("Cannot advance: " . implode(', ', $validation['errors']));
            }
            
            // Lock HPP if transitioning from product_analysis
            if ($currentStage && $currentStage->stage_code === 'product_analysis') {
                $this->lockHppCalculation($package);
            }
            
            // Record the transition in history
            WorkflowHistory::create([
                'id_travel_package' => $package->id,
                'from_stage' => $package->current_workflow_stage,
                'to_stage' => $toStageCode,
                'transitioned_at' => now(),
                'transitioned_by' => Auth::id(),
                'notes' => $notes
            ]);
            
            // Update package workflow stage
            $package->current_workflow_stage = $toStageCode;
            $package->save();
            
            // Log the workflow transition to audit trail
            $this->auditService->logWorkflowTransition(
                $package->id,
                $currentStage ? $currentStage->stage_name : 'Initial',
                $targetStage->stage_name,
                $notes
            );
            
            // Create tasks for the new stage
            $this->createTasksForStage($package, $targetStage);
            
            // Send notification to next responsible team
            $this->notificationService->notifyWorkflowStageCompleted($package, $targetStage);
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => "Package transitioned to {$targetStage->stage_name}",
                'stage' => $targetStage
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate stage requirements before allowing transition
     * 
     * @param TravelPackage $package
     * @param WorkflowStage|null $currentStage
     * @return array
     */
    public function validateStageRequirements(TravelPackage $package, ?WorkflowStage $currentStage)
    {
        $errors = [];
        
        if (!$currentStage) {
            return ['valid' => true, 'errors' => []];
        }
        
        // Check stage-specific requirements
        switch ($currentStage->stage_code) {
            case 'product_analysis':
                // Require HPP calculation to be complete
                if (!$package->hppCalculation || !$package->hppCalculation->total_hpp) {
                    $errors[] = 'HPP calculation must be completed';
                }
                break;
                
            case 'flight_tickets':
                // Require flight bookings to be confirmed
                $keberangkatan = $package->keberangkatan()->first();
                if ($keberangkatan) {
                    $confirmedFlights = $keberangkatan->flightBookings()
                        ->where('status', 'confirmed')
                        ->count();
                    if ($confirmedFlights === 0) {
                        $errors[] = 'At least one flight booking must be confirmed';
                    }
                }
                break;
                
            case 'design_materials':
                // Require all design materials to be complete
                $materials = $package->designMaterials ?? [];
                $requiredMaterials = ['flyer', 'itinerary', 'promotional_video', 'package_information'];
                foreach ($requiredMaterials as $material) {
                    // This will be implemented when design materials are added
                }
                break;
                
            case 'finance':
                // Require invoice to be created
                $bookings = $package->jamaahBookings()->count();
                if ($bookings > 0) {
                    $bookingsWithInvoice = $package->jamaahBookings()
                        ->whereNotNull('id_invoice')
                        ->count();
                    if ($bookingsWithInvoice === 0) {
                        $errors[] = 'At least one invoice must be created';
                    }
                }
                break;
                
            case 'administration':
                // Require all documents to be approved (Requirement 17.8)
                $keberangkatan = $package->keberangkatan()->first();
                if ($keberangkatan) {
                    $totalJamaah = $keberangkatan->jamaahBookings()->count();
                    if ($totalJamaah > 0) {
                        // Check if all jamaah have required documents approved
                        $requiredDocTypes = ['passport', 'visa', 'ticket', 'insurance', 'health_certificate'];
                        
                        foreach ($keberangkatan->jamaahBookings as $booking) {
                            foreach ($requiredDocTypes as $docType) {
                                $doc = $booking->documents()->where('document_type', $docType)->first();
                                if (!$doc || $doc->status !== 'approved') {
                                    $errors[] = "Jamaah {$booking->jamaah->nama} belum memiliki dokumen {$docType} yang disetujui";
                                }
                            }
                        }
                    }
                }
                break;
                
            case 'logistics':
                // Require all equipment to be shipped
                // This will be implemented when logistics management is complete
                break;
        }
        
        // Check if all tasks for current stage are completed
        $incompleteTasks = WorkflowTask::where('id_travel_package', $package->id)
            ->where('id_workflow_stage', $currentStage->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
            
        if ($incompleteTasks > 0) {
            $errors[] = "{$incompleteTasks} task(s) still pending for current stage";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Check if a stage is complete and can advance
     * 
     * @param TravelPackage $package
     * @param WorkflowStage $stage
     * @return bool
     */
    public function isStageComplete(TravelPackage $package, WorkflowStage $stage)
    {
        $validation = $this->validateStageRequirements($package, $stage);
        return $validation['valid'];
    }

    /**
     * Create tasks for a workflow stage
     * 
     * @param TravelPackage $package
     * @param WorkflowStage $stage
     * @return void
     */
    public function createTasksForStage(TravelPackage $package, WorkflowStage $stage)
    {
        // Define default tasks for each stage
        $stageTasks = $this->getDefaultTasksForStage($stage->stage_code);
        
        foreach ($stageTasks as $taskData) {
            $task = WorkflowTask::create([
                'id_travel_package' => $package->id,
                'id_workflow_stage' => $stage->id,
                'task_name' => $taskData['name'],
                'task_description' => $taskData['description'],
                'assigned_to_team' => $stage->responsible_team,
                'due_date' => now()->addDays($taskData['due_days'] ?? 7),
                'status' => 'pending'
            ]);
            
            // If task is assigned to a specific user, send notification
            if ($task->assigned_to_user) {
                $this->notificationService->notifyTaskAssigned($task->assigned_to_user, $task);
            }
        }
    }

    /**
     * Get default tasks for a workflow stage
     * 
     * @param string $stageCode
     * @return array
     */
    private function getDefaultTasksForStage(string $stageCode)
    {
        $tasks = [
            'product_analysis' => [
                ['name' => 'Calculate HPP', 'description' => 'Calculate cost of goods sold for the package', 'due_days' => 3],
                ['name' => 'Determine pricing', 'description' => 'Set package price based on HPP and target margin', 'due_days' => 3],
                ['name' => 'Review package viability', 'description' => 'Assess if package is financially viable', 'due_days' => 5]
            ],
            'flight_tickets' => [
                ['name' => 'Book flights', 'description' => 'Reserve flight seats for the package', 'due_days' => 7],
                ['name' => 'Confirm bookings', 'description' => 'Get confirmation codes for all flight bookings', 'due_days' => 10]
            ],
            'design_materials' => [
                ['name' => 'Create flyer', 'description' => 'Design promotional flyer', 'due_days' => 5],
                ['name' => 'Prepare itinerary', 'description' => 'Create detailed travel itinerary', 'due_days' => 5],
                ['name' => 'Produce video', 'description' => 'Create promotional video', 'due_days' => 10],
                ['name' => 'Compile package info', 'description' => 'Prepare complete package information document', 'due_days' => 5]
            ],
            'finance' => [
                ['name' => 'Create invoices', 'description' => 'Generate invoices for confirmed bookings', 'due_days' => 3],
                ['name' => 'Set payment terms', 'description' => 'Define payment schedule and terms', 'due_days' => 2]
            ],
            'follow_up' => [
                ['name' => 'Contact customers', 'description' => 'Reach out to potential customers', 'due_days' => 7],
                ['name' => 'Track responses', 'description' => 'Log customer responses and interest level', 'due_days' => 14]
            ],
            'closing' => [
                ['name' => 'Confirm commitment', 'description' => 'Get customer commitment confirmation', 'due_days' => 3],
                ['name' => 'Process booking', 'description' => 'Complete booking registration', 'due_days' => 2]
            ],
            'cs_all_divisions' => [
                ['name' => 'Coordinate support', 'description' => 'Ensure all divisions are aligned on customer service', 'due_days' => 5]
            ],
            'social_media' => [
                ['name' => 'Post promotions', 'description' => 'Share package on social media channels', 'due_days' => 3],
                ['name' => 'Collect testimonials', 'description' => 'Gather customer testimonials', 'due_days' => 14]
            ],
            'administration' => [
                ['name' => 'Process documents', 'description' => 'Handle passport, visa, and other documents', 'due_days' => 21],
                ['name' => 'Create manifest', 'description' => 'Generate official jamaah manifest', 'due_days' => 14],
                ['name' => 'Submit siskopatuh', 'description' => 'Submit required government reports', 'due_days' => 10]
            ],
            'logistics' => [
                ['name' => 'Prepare equipment', 'description' => 'Order and prepare all required equipment', 'due_days' => 14],
                ['name' => 'Arrange shipping', 'description' => 'Coordinate equipment shipping', 'due_days' => 7]
            ],
            'save_jamaah_data' => [
                ['name' => 'Validate data', 'description' => 'Verify all jamaah data is complete and accurate', 'due_days' => 5],
                ['name' => 'Final review', 'description' => 'Conduct final review of all jamaah information', 'due_days' => 3]
            ],
            'offer_package' => [
                ['name' => 'Present package', 'description' => 'Present final package details to customers', 'due_days' => 5],
                ['name' => 'Answer questions', 'description' => 'Address any customer questions or concerns', 'due_days' => 7]
            ]
        ];
        
        return $tasks[$stageCode] ?? [];
    }

    /**
     * Lock HPP calculation when transitioning from product analysis
     * 
     * @param TravelPackage $package
     * @return void
     */
    private function lockHppCalculation(TravelPackage $package)
    {
        if ($package->hppCalculation) {
            $package->hppCalculation->update([
                'is_locked' => true,
                'locked_at' => now(),
                'locked_by' => Auth::id()
            ]);
        }
    }

    /**
     * Get workflow progress for a package
     * 
     * @param TravelPackage $package
     * @return array
     */
    public function getWorkflowProgress(TravelPackage $package)
    {
        $allStages = WorkflowStage::active()->ordered()->get();
        $currentStage = WorkflowStage::where('stage_code', $package->current_workflow_stage)->first();
        
        $progress = [];
        foreach ($allStages as $stage) {
            $isPast = $currentStage && $stage->stage_order < $currentStage->stage_order;
            $isCurrent = $currentStage && $stage->stage_code === $currentStage->stage_code;
            
            $progress[] = [
                'stage' => $stage,
                'status' => $isPast ? 'completed' : ($isCurrent ? 'current' : 'pending'),
                'is_complete' => $isPast || ($isCurrent && $this->isStageComplete($package, $stage))
            ];
        }
        
        return $progress;
    }

    /**
     * Get next available stage for a package
     * 
     * @param TravelPackage $package
     * @return WorkflowStage|null
     */
    public function getNextStage(TravelPackage $package)
    {
        $currentStage = WorkflowStage::where('stage_code', $package->current_workflow_stage)->first();
        
        if (!$currentStage) {
            return WorkflowStage::active()->ordered()->first();
        }
        
        return $currentStage->getNextStage();
    }
}
