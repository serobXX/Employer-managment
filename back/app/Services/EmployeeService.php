<?php

namespace App\Services;
use App\Enums\JobTitleEnum;
use App\Models\Relation;
use App\Models\User;

class EmployeeService
{
    public static function getRelatedEmployers(?int $employerId): array {
        return User::when($employerId, function ($query) use ($employerId) {
            $query->whereIn('id', function ($query) use ($employerId) {
                $query->select('employee_id')
                    ->from('relations')
                    ->where('employer_id', $employerId);
            });
        })
        ->when(!$employerId, function ($query) {
            $query->where('job_title', JobTitleEnum::EMPLOYER);
        })
        ->get()
        ->map(function ($item) use ($employerId) {
            $item['parentId'] = $employerId;
            return $item;
        })
        ->toArray();
    }

    public static function createRelation(User $user, ?int $parentId): void
    {
        $parentId && Relation::create([
            'employer_id' => $parentId,
            'employee_id' => $user->id
        ]);
    }
}
