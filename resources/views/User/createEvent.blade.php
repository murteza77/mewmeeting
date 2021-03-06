@extends('User.layout')
@section('content')

<?php 
 header("Content-Type: text/html;charset=UTF-8");
?>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Create Event
            </h1>
            <ol class="breadcrumb">
                <li class="active">
                    <a style="text-decoration: none" href="{!! URL::to('/') !!}"> Dashboard</a> /
                    <a style="text-decoration: none" href="{!! URL::to('user/create-event') !!}"> Create Event</a>
                </li>
            </ol>
        </div>
    </div>

    <div class="row" ng-app="event" ng-controller="eventDeleteController">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-edit fa-fw"></i> Create Event</h3>
                </div>

                <div class="panel-body">

 


                    {!! Form::open(array('id' => 'event', 'class' => 'form-horizontal',  'ng-submit'=>'create($event)')) !!}
                    <div class="form-group">
                        <label for="inxputEmail3" class="col-sm-2 control-label">Title</label>

                        <div class="col-sm-10">
                         
                            <input type="text" class="form-control" name="title" id="inputEmail3" placeholder="Enter the title" style="width: 60%">

                        </div>
                    </div>
                  

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Start Time</label>

                        <div class="col-sm-10">
                            <input type="text" readonly class="form-control from" name="start" required
                                   placeholder="Start Time" style="width: 60%">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">End Time</label>

                        <div class="col-sm-10">
                            <input type="text" readonly class="form-control to" name="end" required
                                   placeholder="End Time" style="width: 60%">
                        </div>
                    </div>

                    <div id="addReminderShow">

                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="button" id="addReminderCreate" class="btn btn-primary"><i
                                        class="fa fa-plus"></i> Add
                                Reminder
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-fw fa-plus"></i> Create
                            </button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="addReminderView" style="display: none">
        <div class="reminder_delete_div">
            <div class="form-group">
                <hr class="col-sm-8 width60 hr-clr-green"/>
                <span class="col-sm-1 pull-left reminder-cross-table delete_reminder" style="cursor: pointer">X</span>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Reminder Date</label>

                <div class="col-sm-10">
                    <input type="text" required name="reminder_date[]"
                           class="form-control width60 reminder_date readonly">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Reminder to(Email)</label>

                <div class="col-sm-10">
                    <input type="text" required name="reminder_email[]" class="form-control width60"
                           placeholder="Example : imtiazpabel@yahoo.com,ippabel@gmail.com">
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Reminder Text</label>

                <div class="col-sm-10">
                    <textarea name="reminder_text[]" class="form-control width60" required></textarea>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('asset')
    <style>
        .from {
            position: relative;
            z-index: 10000;
        }

        .to {
            position: relative;
            z-index: 10000;
        }

        .ui-pnotify {
            z-index: 1041
        }

        .reminder_date {
            position: relative;
            z-index: 10000;
        }
    </style>
    <script>
        $(document).ready(function () {
            $("#addReminderCreate").click(function (event) {
                event.preventDefault();
                $("#addReminderCreate").html('<i class="fa fa-plus"></i> Add More Reminder');
                $("#addReminderShow").append($(".addReminderView").html());
            });
            $(document).on('focus', '.reminder_date', function () {
                $(this).datepicker({
                    dateFormat: 'yy-mm-dd',
                    defaultDate: "+1w",
                    changeMonth: true,
                    changeYear: true,
                    numberOfMonths: 1
                });
            });

            $(document).on('click', '.delete_reminder', function () {
                $(this).parent().parent().remove();
            });
        });
        var event = angular.module('event', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('{kp');
            $interpolateProvider.endSymbol('kp}');
        });
        event.controller('eventDeleteController', function ($scope, $http) {
            $scope.create = function (event) {
                event.preventDefault();
                var req = {
                    method: 'POST',
                    url: "{!! URL::to('user/create-event') !!}",
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    data: $("#event").serialize()
                };
                $http(req).success(function (response) {
                    $.pnotify.defaults.styling = "bootstrap3";
                    if (response == 'true') {
                        window.location.href = "{!! URL::to('user/event') !!}";
                    } else {
                        $.pnotify({
                            title: 'ERROR',
                            text: response,
                            type: 'error',
                            delay: 3000
                        });
                    }
                });
            };
        });
    </script>
    <script>
        $(document).ready(function () {
            $(".from").datetimepicker({
                dateFormat: 'yy-mm-dd',
                defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                onClose: function (selectedDate) {
                    $(".to").datepicker("option", "minDate", selectedDate);
                }
            });
            $(".to").datetimepicker({
                dateFormat: 'yy-mm-dd',
                defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                onClose: function (selectedDate) {
                    $(".from").datepicker("option", "maxDate", selectedDate);
                }
            });
        });
    </script>
@endsection