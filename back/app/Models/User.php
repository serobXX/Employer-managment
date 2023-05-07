<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'job_title',
        'email',
        'phone',
        'note'
    ];

    public function employeeRelations(): HasOne
    {
        return $this->hasOne(Relation::class, 'employee_id');
    }

    public function employerRelations(): HasMany
    {
        return $this->hasMany(Relation::class, 'employer_id');
    }

    public function deleteRecursive(): void
    {
        // // Delete related employees recursively
        foreach ($this->employerRelations as $employer) {
            $employer->employee->deleteRecursive();
        }

        // Delete the user
        $this->delete();
    }
}
