@extends('twill::layouts.form')

@section('contentFields')

@if(hasRole(['Owner', 'Administrator']))


@formField('medias', [
'name' => 'logo',
'label' => 'Company Logo',
'note' => 'Shown on certificate'
])


@formField('input', [
        'name' => 'primary_color',
        'label' => ' Primary Color',
        'maxlength' => 100
])
@formField('input', [
        'name' => 'secondary_color',
        'label' => 'Secondary Color',
        'maxlength' => 100
])
@formField('input', [
        'name' => 'third_color',
        'label' => ' Tertiary Color',
        'maxlength' => 100
])

@formField('input', [
        'name' => 'menu_color',
        'label' => ' Menu Color',
        'maxlength' => 100
])

@formField('input', [
        'name' => 'menu_active_color',
        'label' => ' Menu Color Active',
        'maxlength' => 100
])

@formField('input', [
        'name' => 'footer_color',
        'label' => ' Footer Color',
        'maxlength' => 100
])


@formField('input', [
        'name' => 'primary_heading_color',
        'label' => ' Primary Heading Color',
        'maxlength' => 100
])


@formField('input', [
        'name' => 'secondary_heading_color',
        'label' => ' Secondary Heading Color',
        'maxlength' => 100
])

@formField('input', [
        'name' => 'primary_text_color',
        'label' => ' Primary Text Color',
        'maxlength' => 100
])

@formField('input', [
        'name' => 'secondary_text_color',
        'label' => ' Secondary Text Color',
        'maxlength' => 100
])

@formField('input', [
        'name' => 'third_text_color',
        'label' => ' Tertiary Text Color',
        'maxlength' => 100
])

@formField('input', [
        'name' => 'button_primary_color',
        'label' => ' Button Color Primary',
        'maxlength' => 100
])

@formField('input', [
        'name' => 'button_secondary_color',
        'label' => ' Button Color Secondary',
        'maxlength' => 100
])

@formField('input', [
        'name' => 'button_third_color',
        'label' => ' Button Color Tertiary',
        'maxlength' => 100
])





@formField('medias', [
'name' => 'courses_banner',
'label' => 'Courses Banner',
'note' => 'Shown on courses page'
])

@formField('medias', [
'name' => 'live_sessions_banner',
'label' => 'Live Sessions Banner',
'note' => 'Shown on Live sessions'
])

@formField('medias', [
'name' => 'our_progress_banner',
'label' => 'Our Progress Banner',
'note' => 'Shown on Our Progress'
])

@formField('medias', [
'name' => 'enrolled_banner',
'label' => 'My Enrolled Module Banner',
'note' => 'Shown on My Enrolled Module'
])

@formField('medias', [
'name' => 'community_banner',
'label' => 'My Community Banner',
'note' => 'Shown on My Community'
])


@formField('medias', [
'name' => 'training_feed',
'label' => 'Training Feed Banner',
'note' => 'Shown on Training Feed'
])

@formField('medias', [
'name' => 'myprogress_banner',
'label' => 'My Progress Banner',
'note' => 'Shown on My Progress'
])

@formField('medias', [
'name' => 'mandatory_svg',
'label' => 'Mandatory svg',

])

@formField('medias', [
'name' => 'hr_recommended_svg',
'label' => 'HR recommened svg',

])

@formField('medias', [
'name' => 'clock_svg',
'label' => 'Clock svg',

])
@formField('medias', [
'name' => 'calendar_svg',
'label' => 'Calendar svg',

])

@formField('medias', [
'name' => 'tick_svg',
'label' => 'Tick svg',

])
@formField('medias', [
'name' => 'user_svg',
'label' => 'User svg',

])
@formField('medias', [
'name' => 'notification_bell_svg',
'label' => 'Notification Bell svg',

 ])
@formField('medias', [
'name' => 'rocket_svg',
'label' => 'Rocket svg',

])
@formField('medias', [
'name' => 'not_finished_trophy_svg',
'label' => 'Not finished Trophy svg',

])
@formField('medias', [
'name' => 'finished_trophy_svg',
'label' => 'Finished Trophy svg',

])
@formField('medias', [
'name' => 'like_svg',
'label' => 'Like svg',

 ])

@formField('medias', [
'name' => 'comment_svg',
'label' => 'comment svg',

])
@endif


@stop


