<?php

namespace App\Services;

use App\Models\JamaahBooking;
use App\Models\Member;
use App\Models\TravelPackage;
use App\Models\WorkflowTask;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Perform global search across jamaah names, passport numbers, and booking references
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function globalSearch(string $query, int $limit = 10): Collection
    {
        $results = collect();

        // Search jamaah by name
        $jamaahByName = Member::where('is_jamaah', true)
            ->where(function ($q) use ($query) {
                $q->where('nama_member', 'LIKE', "%{$query}%")
                  ->orWhere('nama_lengkap', 'LIKE', "%{$query}%");
            })
            ->with(['jamaahBookings.travelPackage'])
            ->limit($limit)
            ->get()
            ->map(function ($member) {
                return [
                    'type' => 'jamaah',
                    'id' => $member->id_member,
                    'title' => $member->nama_member,
                    'subtitle' => $member->no_passport ?? 'No passport',
                    'url' => route('admin.inventaris.booking.index', ['jamaah' => $member->id_member]),
                    'icon' => 'fas fa-user',
                ];
            });

        // Search jamaah by passport number
        $jamaahByPassport = Member::where('is_jamaah', true)
            ->where('no_passport', 'LIKE', "%{$query}%")
            ->with(['jamaahBookings.travelPackage'])
            ->limit($limit)
            ->get()
            ->map(function ($member) {
                return [
                    'type' => 'jamaah',
                    'id' => $member->id_member,
                    'title' => $member->nama_member,
                    'subtitle' => "Passport: {$member->no_passport}",
                    'url' => route('admin.inventaris.booking.index', ['jamaah' => $member->id_member]),
                    'icon' => 'fas fa-passport',
                ];
            });

        // Search bookings by booking code
        $bookings = JamaahBooking::where('booking_code', 'LIKE', "%{$query}%")
            ->with(['jamaah', 'travelPackage'])
            ->limit($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'type' => 'booking',
                    'id' => $booking->id,
                    'title' => $booking->booking_code,
                    'subtitle' => "{$booking->jamaah->nama_member} - {$booking->travelPackage->package_name}",
                    'url' => route('admin.inventaris.booking.show', $booking->id),
                    'icon' => 'fas fa-ticket-alt',
                ];
            });

        // Merge results
        $results = $results->merge($jamaahByName)
            ->merge($jamaahByPassport)
            ->merge($bookings)
            ->unique('id')
            ->take($limit);

        return $results;
    }

    /**
     * Get autocomplete suggestions for search
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function autocomplete(string $query, int $limit = 10): Collection
    {
        $suggestions = collect();

        // Jamaah names
        $jamaahNames = Member::where('is_jamaah', true)
            ->where(function ($q) use ($query) {
                $q->where('nama_member', 'LIKE', "%{$query}%")
                  ->orWhere('nama_lengkap', 'LIKE', "%{$query}%");
            })
            ->limit($limit)
            ->pluck('nama_member')
            ->map(function ($name) {
                return [
                    'value' => $name,
                    'type' => 'jamaah_name',
                    'label' => $name,
                ];
            });

        // Passport numbers
        $passportNumbers = Member::where('is_jamaah', true)
            ->whereNotNull('no_passport')
            ->where('no_passport', 'LIKE', "%{$query}%")
            ->limit($limit)
            ->pluck('no_passport')
            ->map(function ($passport) {
                return [
                    'value' => $passport,
                    'type' => 'passport',
                    'label' => "Passport: {$passport}",
                ];
            });

        // Booking references
        $bookingCodes = JamaahBooking::where('booking_code', 'LIKE', "%{$query}%")
            ->limit($limit)
            ->pluck('booking_code')
            ->map(function ($code) {
                return [
                    'value' => $code,
                    'type' => 'booking',
                    'label' => "Booking: {$code}",
                ];
            });

        $suggestions = $suggestions->merge($jamaahNames)
            ->merge($passportNumbers)
            ->merge($bookingCodes)
            ->unique('value')
            ->take($limit);

        return $suggestions;
    }

    /**
     * Filter packages based on criteria
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filterPackages(array $filters)
    {
        $query = TravelPackage::query();

        if (!empty($filters['departure_date_from'])) {
            $query->where('departure_date', '>=', $filters['departure_date_from']);
        }

        if (!empty($filters['departure_date_to'])) {
            $query->where('departure_date', '<=', $filters['departure_date_to']);
        }

        if (!empty($filters['destination'])) {
            $query->where('package_name', 'LIKE', "%{$filters['destination']}%");
        }

        if (!empty($filters['package_type'])) {
            $query->where('package_type', $filters['package_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['workflow_stage'])) {
            $query->where('current_workflow_stage', $filters['workflow_stage']);
        }

        return $query;
    }

    /**
     * Filter jamaah based on criteria
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filterJamaah(array $filters)
    {
        $query = Member::where('is_jamaah', true);

        if (!empty($filters['keberangkatan_id'])) {
            $query->whereHas('jamaahBookings', function ($q) use ($filters) {
                $q->where('id_keberangkatan', $filters['keberangkatan_id']);
            });
        }

        if (!empty($filters['payment_status'])) {
            $query->whereHas('jamaahBookings', function ($q) use ($filters) {
                $q->where('payment_status', $filters['payment_status']);
            });
        }

        if (!empty($filters['document_status'])) {
            if ($filters['document_status'] === 'complete') {
                $query->whereHas('jamaahBookings.documents', function ($q) {
                    $q->where('status', 'approved');
                }, '>=', 5); // All 5 document types
            } elseif ($filters['document_status'] === 'incomplete') {
                $query->whereHas('jamaahBookings', function ($q) {
                    $q->whereDoesntHave('documents', function ($subQ) {
                        $subQ->where('status', 'approved');
                    });
                });
            }
        }

        if (!empty($filters['package_id'])) {
            $query->whereHas('jamaahBookings', function ($q) use ($filters) {
                $q->where('id_travel_package', $filters['package_id']);
            });
        }

        return $query;
    }

    /**
     * Filter tasks based on criteria
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filterTasks(array $filters)
    {
        $query = WorkflowTask::query();

        if (!empty($filters['team'])) {
            $query->where('assigned_to_team', $filters['team']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['due_date_from'])) {
            $query->where('due_date', '>=', $filters['due_date_from']);
        }

        if (!empty($filters['due_date_to'])) {
            $query->where('due_date', '<=', $filters['due_date_to']);
        }

        if (!empty($filters['assigned_to_user'])) {
            $query->where('assigned_to_user', $filters['assigned_to_user']);
        }

        if (!empty($filters['package_id'])) {
            $query->where('id_travel_package', $filters['package_id']);
        }

        return $query;
    }

    /**
     * Save a filter configuration for a user
     *
     * @param int $userId
     * @param string $filterName
     * @param string $filterType
     * @param array $filterData
     * @return bool
     */
    public function saveFilter(int $userId, string $filterName, string $filterType, array $filterData): bool
    {
        try {
            DB::table('saved_filters')->insert([
                'user_id' => $userId,
                'filter_name' => $filterName,
                'filter_type' => $filterType,
                'filter_data' => json_encode($filterData),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get saved filters for a user
     *
     * @param int $userId
     * @param string|null $filterType
     * @return Collection
     */
    public function getSavedFilters(int $userId, ?string $filterType = null): Collection
    {
        $query = DB::table('saved_filters')
            ->where('user_id', $userId);

        if ($filterType) {
            $query->where('filter_type', $filterType);
        }

        return $query->get()->map(function ($filter) {
            $filter->filter_data = json_decode($filter->filter_data, true);
            return $filter;
        });
    }

    /**
     * Delete a saved filter
     *
     * @param int $filterId
     * @param int $userId
     * @return bool
     */
    public function deleteSavedFilter(int $filterId, int $userId): bool
    {
        return DB::table('saved_filters')
            ->where('id', $filterId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }
}
