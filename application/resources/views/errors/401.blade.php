@extends('errors::illustrated-layout')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('heading', 'Unauthorized')
@section('message', __('Sorry, you are not authorized to access this page.'))
