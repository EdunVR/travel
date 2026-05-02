<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an action to the audit trail
     *
     * @param string $actionType The type of action (e.g., 'created', 'updated', 'deleted', 'workflow_transition', 'payment')
     * @param string $description Human-readable description of the action
     * @param string|null $modelType The model class name (optional)
     * @param int|null $modelId The model ID (optional)
     * @param array|null $oldValues The old values before the action (optional)
     * @param array|null $newValues The new values after the action (optional)
     * @return AuditLog
     */
    public function log(
        string $actionType,
        string $description,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action_type' => $actionType,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log a workflow stage transition
     *
     * @param int $packageId
     * @param string $fromStage
     * @param string $toStage
     * @param string|null $notes
     * @return AuditLog
     */
    public function logWorkflowTransition(
        int $packageId,
        string $fromStage,
        string $toStage,
        ?string $notes = null
    ): AuditLog {
        $description = "Workflow stage changed from '{$fromStage}' to '{$toStage}' for package ID {$packageId}";
        if ($notes) {
            $description .= ". Notes: {$notes}";
        }

        return $this->log(
            actionType: 'workflow_transition',
            description: $description,
            modelType: 'App\Models\TravelPackage',
            modelId: $packageId,
            oldValues: ['stage' => $fromStage],
            newValues: ['stage' => $toStage, 'notes' => $notes]
        );
    }

    /**
     * Log a payment transaction
     *
     * @param int $bookingId
     * @param float $amount
     * @param string $paymentMethod
     * @param string $bookingReference
     * @return AuditLog
     */
    public function logPaymentTransaction(
        int $bookingId,
        float $amount,
        string $paymentMethod,
        string $bookingReference
    ): AuditLog {
        $description = "Payment of Rp " . number_format($amount, 0, ',', '.') . 
                      " received for booking {$bookingReference} via {$paymentMethod}";

        return $this->log(
            actionType: 'payment',
            description: $description,
            modelType: 'App\Models\JamaahBooking',
            modelId: $bookingId,
            newValues: [
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'booking_reference' => $bookingReference
            ]
        );
    }

    /**
     * Log a document upload
     *
     * @param int $jamaahBookingId
     * @param string $documentType
     * @param string $fileName
     * @return AuditLog
     */
    public function logDocumentUpload(
        int $jamaahBookingId,
        string $documentType,
        string $fileName
    ): AuditLog {
        $description = "Document '{$documentType}' uploaded: {$fileName} for booking ID {$jamaahBookingId}";

        return $this->log(
            actionType: 'document_upload',
            description: $description,
            modelType: 'App\Models\JamaahBooking',
            modelId: $jamaahBookingId,
            newValues: [
                'document_type' => $documentType,
                'file_name' => $fileName
            ]
        );
    }

    /**
     * Log a data modification
     *
     * @param string $modelType
     * @param int $modelId
     * @param array $oldValues
     * @param array $newValues
     * @param string|null $customDescription
     * @return AuditLog
     */
    public function logDataModification(
        string $modelType,
        int $modelId,
        array $oldValues,
        array $newValues,
        ?string $customDescription = null
    ): AuditLog {
        $modelName = class_basename($modelType);
        $description = $customDescription ?? "Modified {$modelName} ID {$modelId}";

        return $this->log(
            actionType: 'updated',
            description: $description,
            modelType: $modelType,
            modelId: $modelId,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    /**
     * Log a user login event
     *
     * @param int $userId
     * @param bool $success
     * @return AuditLog
     */
    public function logLogin(int $userId, bool $success = true): AuditLog
    {
        $description = $success ? "User logged in successfully" : "Failed login attempt";

        return $this->log(
            actionType: $success ? 'login' : 'login_failed',
            description: $description,
            modelType: 'App\Models\User',
            modelId: $userId
        );
    }

    /**
     * Log a user logout event
     *
     * @param int $userId
     * @return AuditLog
     */
    public function logLogout(int $userId): AuditLog
    {
        return $this->log(
            actionType: 'logout',
            description: "User logged out",
            modelType: 'App\Models\User',
            modelId: $userId
        );
    }

    /**
     * Clean up old audit logs based on retention policy
     * Default retention: 5 years (1825 days)
     *
     * @param int $retentionDays Number of days to retain logs
     * @return int Number of logs deleted
     */
    public function cleanupOldLogs(int $retentionDays = 1825): int
    {
        $cutoffDate = now()->subDays($retentionDays);
        
        // Note: This will fail due to the delete prevention in the model
        // This is intentional - logs should not be deleted programmatically
        // Only database administrators should be able to delete old logs directly
        
        return 0; // Always return 0 as deletion is prevented
    }

    /**
     * Get audit logs with filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getFilteredLogs(array $filters = [])
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if (isset($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        if (isset($filters['action_type'])) {
            $query->byActionType($filters['action_type']);
        }

        if (isset($filters['model_type'])) {
            $query->byModelType($filters['model_type']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->byDateRange($filters['start_date'], $filters['end_date']);
        }

        return $query;
    }

    /**
     * Export audit logs to CSV format
     *
     * @param array $filters
     * @return string CSV content
     */
    public function exportToCsv(array $filters = []): string
    {
        $logs = $this->getFilteredLogs($filters)->get();

        $csv = "Timestamp,User,Action Type,Description,Model Type,Model ID,Old Values,New Values,IP Address\n";

        foreach ($logs as $log) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user ? $log->user->name : 'System',
                $log->action_type,
                str_replace('"', '""', $log->description),
                $log->model_type ?? '',
                $log->model_id ?? '',
                $log->formatted_old_values ?? '',
                $log->formatted_new_values ?? '',
                $log->ip_address ?? ''
            );
        }

        return $csv;
    }
}
