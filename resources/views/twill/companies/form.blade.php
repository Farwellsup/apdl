@extends('twill::layouts.form')


@section('contentFields')

    @if (hasRole(['Owner', 'Administrator']))
      
       @formField('select',[
         'name'=> 'platform_settings_id',
         'label' => 'Parent Company',
         'placeholder' => 'Select parent company',
         'options' => collect($platformSettings ?? '')

       ])
      
        @formField('input', [
            'name' => 'company_initials',
            'label' => 'Company Initials',
            'maxlength' => 100,
        ])
       @endif

        @formField('input', [
            'name' => 'contact_first_name',
            'label' => ' Contact First Name',
            'maxlength' => 100,
        ])

        @formField('input', [
            'name' => 'contact_last_name',
            'label' => ' Contact last Name',
        
            'maxlength' => 100,
        ])

        @formField('input', [
            'name' => 'contact_email',
            'label' => ' Contact Email Address',
            'maxlength' => 100,
        ])


    @formField('input', [
        'name' => 'signature_name',
        'label' => 'Certificate Signature Name',
        'maxlength' => 100,
    ])

      @formField('medias', [
            'name' => 'logo',
            'label' => 'Company Logo',
            'note' => 'Shown on certificate',
        ])
  
    @formField('medias', [
        'name' => 'signature',
        'label' => 'Certificate Signature',
        'note' => 'Shown on certificate',
    ])



@stop
