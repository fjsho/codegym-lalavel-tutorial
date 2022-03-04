@extends('errors::illustrated-layout')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('heading', 'Too Many Requests')
@section('message', __('Sorry, you are making too many requests to our servers.'))
