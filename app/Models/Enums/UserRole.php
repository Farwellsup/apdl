<?php

    namespace A17\Twill\Models\Enums;

    use MyCLabs\Enum\Enum;

    class UserRole extends Enum
    {
        // const VIEWONLY = 'View only';
        // const PUBLISHER = 'Publisher';
        const ADMIN = 'Admin';
        const Learner = 'Learner';
        const GROUPHR = 'Group HR';
        const COMPANYHR = 'Company HR';
        const BRANCHMANAGER = 'Branch Manager';
        const HOD = 'HOD';
    }
