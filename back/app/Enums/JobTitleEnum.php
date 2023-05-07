<?php

namespace App\Enums;

interface JobTitleEnum
{
    public const EMPLOYER = 'employer';
    public const EMPLOYEE = 'employee';
    public const JobTitle = [
        self::EMPLOYER,
        self::EMPLOYEE
    ];
}
