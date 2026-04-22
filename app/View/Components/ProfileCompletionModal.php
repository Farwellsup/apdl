<?php

namespace App\View\Components;

use Closure;
use Auth;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Country;
use App\Models\Unit;
use App\Models\Department;
use App\Models\User;


class ProfileCompletionModal extends Component
{
    public bool $needsCompletion;
    public array $missingFields;

    // Dropdown options (only loaded when needed)
    public $resetpd;
    public $units;
    public $departments;
    public $genders;
    public $countries;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
          $this->missingFields    = [];
        $this->needsCompletion  = false;

         if (! Auth::check()) {
            return;
        }

        $user = Auth::user();

        $checks = [
            'company_id'    => $user->company_id,
            'unit_id'     => $user->unit_id,
            'department_id' => $user->department_id,
            'country_id'   => $user->country_id,
            'gender_id'     => $user->gender_id,
            'reset_pd'  => $user->reset_pd,
        ];

        foreach ($checks as $field => $value) {
            if (empty($value) || $value === "0") {
                $this->missingFields[] = $field;
            }
        }


        $this->needsCompletion = ! empty($this->missingFields);

        if ($this->needsCompletion) {           
            $this->units   = in_array('unit_id', $this->missingFields)
                ? Unit::where('company_id', Auth::user()->company_id)->orderBy('title')->get(['id', 'title'])
                : collect();

            $this->departments = in_array('department_id', $this->missingFields)
                ? Department::where('company_id', Auth::user()->company_id)->orderBy('title')->get(['id', 'title'])
                : collect();

            $this->countries    = in_array('country_id', $this->missingFields)
                ? Country::orderBy('title')->get(['id', 'title'])
                : collect();

            $this->genders     = in_array('gender_id', $this->missingFields)
                ? collect(User::getGender())
                : collect();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.profile-completion-modal');
    }
}
