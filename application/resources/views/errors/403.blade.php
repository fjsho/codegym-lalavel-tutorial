@extends('errors::illustrated-layout')

@section('title', __('Forbidden'))
@section('code', '403')
@section('heading', __($exception->getMessage()) ?: 'Forbidden')
@section('message', __('Sorry, you are forbidden from accessing this page.'))