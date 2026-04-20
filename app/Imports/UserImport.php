<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\User;
use App\Models\Department;
use App\Models\Unit;
use App\Models\TwillPosition;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;

class UserImport implements ToCollection, WithHeadingRow
{

    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }


    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {

            dd($row, $this->companyId);

            if (empty($row['payroll_number'])) {

                $parts = explode(' ', $row['name']);
                $clean = array_filter($parts, function ($value) {
                    return $value !== null && $value !== '';
                });
                $first_name = $clean[0];
                $last_name = implode(' ', array_slice($clean, 1));

                $company = Company::where('id', $this->companyId)->first(); 

                $department = Department::where('title', $row['department'])->where('company_id', $company->id)->first();

                $unit = Unit::where('title', $row['unit'])->where('company_id', $company->id)->first();

              

                $user = User::where('payroll_number', $row['payroll_number'])->first();

                $email = $row['payroll_number'] . '@' . $this->companyId . '.com';

                $password = Str::random(18);

                $pd = new TwillPosition();
                $pd->user_id = $newUser->id;
                $pd->position = encrypt($password);
                $pd->save();

                if (empty($user)) {
                    $allow = new User();
                    $allow->name = $row['name'];
                    $allow->email = $email;
                    $allow->password = Hash::make(decrypt($pd->position));
                    $allow->first_name = $first_name;
                    $allow->last_name = $last_name;
                    $allow->company_name = $company->title;
                    $allow->company_id = $company->id;
                    $allow->department_id = ($department) ? $department->id : null;
                    $allow->department_name = ($department) ? $department->title : null;
                    $allow->unit_id = ($unit) ? $unit->id : null;
                    $allow->unit_name = ($unit) ? $unit->title : null;
                    $allow->role_id = 4;
                    $allow->published = 1;
                    $allow->save();
                }
            }
        }

        session()->put('uploaded', 'Yes');

        // return new User([
        //     //
        // ]);
    }
}
