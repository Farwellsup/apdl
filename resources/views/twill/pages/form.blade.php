@extends('twill::layouts.form')

@php
    $wysiwygOptions = [
        ['header' => [2, 3, 4, 5, 6, false]],
        'bold',
        'italic',
        'underline',
        'strike',
        'blockquote',
        'ordered',
        'bullet',
        'hr',
        'link',
        'clean',
        'table',
        'align',
    ];
@endphp

@section('contentFields')
    @formColumns
    @slot('left')
        @formField('input', [
            'name' => 'key',
            'label' => 'Key',
            'maxlength' => 100,
        ])
    @endslot
    @slot('right')
        @formField('select', [
            'name' => 'menu_type',
            'label' => 'Menu Type',
            'placeholder' => 'Select Menu Type',
            'options' => $menuTypeList ?? [],
        ])
    @endslot
    @endformColumns

    @formField('input', [
        'name' => 'header_title',
        'label' => 'Header Title',
        'maxlength' => 100,
    ])

    @formField('wysiwyg', [
        'name' => 'description',
        'label' => 'Description',
        'maxlength' => 2000,
        'toolbarOptions' => $wysiwygOptions ?? [],
        'editSource' => true,
    ])



    @formColumns
    @slot('left')
        @formField('medias', [
            'name' => 'hero_image',
            'label' => 'Hero image',
        ])
    @endslot
    @slot('right')
        @formField('medias', [
            'name' => 'mobile_hero_image',
            'label' => 'Mobile Hero image',
        ])
    @endslot
    @endformColumns

    @formColumns
    @slot('left')
        @formField('input', [
            'name' => 'url',
            'label' => 'Route',
        ])
    @endslot
    @slot('right')
        @formField('input', [
            'name' => 'link_text',
            'label' => 'Link Text',
        ])
    @endslot
    @endformColumns
@endsection
