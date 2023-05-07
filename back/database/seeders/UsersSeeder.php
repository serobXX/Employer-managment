<?php

namespace Database\Seeders;

use App\Enums\JobTitleEnum;
use App\Models\Relation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employers = $this->createUsers(10, JobTitleEnum::EMPLOYER);
        foreach ($employers as $employer) {
            $employees = $this->createUsers(10, JobTitleEnum::EMPLOYEE);
            foreach ($employees as $employee) {
                $this->createRelations($employer->id, $employee->id);
                $employees2 = $this->createUsers(10, JobTitleEnum::EMPLOYEE);
                foreach ($employees2 as $employee2) {
                    $this->createRelations($employee->id, $employee2->id);
                    $employees3 = $this->createUsers(10, JobTitleEnum::EMPLOYEE);
                    foreach ($employees3 as $employee3) {
                        $this->createRelations($employee2->id, $employee3->id);
                    }
                }
            }
        }
    }

    public function createUsers($count, $jobTitle) {
        return User::factory()
            ->count($count)
            ->create([
                'job_title' => $jobTitle
            ]);
    }

    public function createRelations($employerId, $employeeId) {
        return Relation::factory()
            ->create([
                'employer_id' => $employerId,
                'employee_id' => $employeeId
            ]);
    }
}
