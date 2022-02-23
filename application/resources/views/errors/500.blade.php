@extends('errors::illustrated-layout')

@section('title', __('Server Error'))
@section('code', '500')
@section('heading', 'Server Error')
@section('message', __('Whoops, something went wrong on our servers.'))
