@extends('twill::layouts.form')


@section('contentFields')




@formField('files', [
    'name' => 'single_file',
    'label' => 'Single file',
    'note' => 'Add one file (per language)'
])
@endsection