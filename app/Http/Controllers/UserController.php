<?php namespace App\Http\Controllers;

use App\ScheduleReminder;
use Auth;
use Request;
use Session;
use DB;
use Mail;
use Response;
use Redirect;
use App\Schedule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Symfony\Component\Security\Core\User\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $data['monthEvent'] = Schedule::where('start_time', '>=', date('Y-m-01'))
            ->where('start_time', '<=', date('Y-m-t'))
            ->count();
        $latMonth = date('m') - 1;
        $nextMonth = date('m') - 1;
        $data['lastMonthEvent'] = Schedule::where('start_time', '>=', date("Y-$latMonth-01"))
            ->where('start_time', '<=', date("Y-$latMonth-t"))
            ->count();

        $data['nextMonthEvent'] = Schedule::where('start_time', '>=', date("Y-$nextMonth-01"))
            ->where('start_time', '<=', date("Y-$nextMonth-t"))
            ->count();

        $data['fullMonthEvent'] = Schedule::select(DB::raw("count('id') as total,start_time"))
            ->where('start_time', '>=', date('Y-m-01'))
            ->where('start_time', '<=', date('Y-m-t'))
            ->groupBy(DB::raw("DATE_FORMAT(start_time, '%Y%m%d')"))
            ->get();

        $data['fullYearMonthEvent'] = Schedule::select(DB::raw("count('id') as total,start_time"))
            ->where('start_time', '>=', date('Y-01-01'))
            ->where('start_time', '<=', date('Y-12-31'))
            ->groupBy(DB::raw("DATE_FORMAT(start_time, '%Y%m')"))
            ->get();

        $data['menu'] = 'Dashboard';
        return view('User.dashboard', $data);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getEvent()
    {
        $data['menu'] = 'Event';
        $data['events'] = Schedule::all();
        return view('User.event', $data);
    }

    /**
     * @return \Illuminate\View\View|string
     */
    public function anyPasswordChange()
    {
        if (Input::all()) {
            $rules = array(
                'new_password' => 'required|same:confirm_password|min:6',
                'current_password' => 'required|password_check',
            );
            $messages = array(
                'new_password.same' => 'New Password and Confirm password are not Matched',
            );
            /* Laravel Validator Rules Apply */
            $validator = Validator::make(Input::all(), $rules, $messages);
            if ($validator->fails()):
                return $validator->messages()->first();
            else:
                $userUpdate = \App\User::find(Auth::user()->id);
                $userUpdate->password = Hash::make(Input::get('new_password'));
                $userUpdate->save();
                return 'true';
            endif;
        } else {
            $data['menu'] = 'Setting';
            return view('User.passwordChange', $data);
        }
    }

    /**
     * @return \Illuminate\View\View|string
     */
    public function anyUpdateInfo()
    {
        if (Input::all()) {
            $id = Input::get('id');
            $rules = array(
                'first_name' => 'required|alpha_num_spaces',
                'last_name' => 'required|alpha_num_spaces',
                'email' => "required|email|unique:users,email,$id",
//                'phone' => 'required|phone_number',
            );
            /* Laravel Validator Rules Apply */
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()):
                return $validator->messages()->first();
            else:
                $user = \App\User::find($id);
                $user->first_name = Input::get('first_name');
                $user->last_name = Input::get('last_name');
                $user->email = Input::get('email');
                $user->phone = Input::get('phone');
                $user->email_status = Input::get('email_status');
                $user->save();
                return 'true';
            endif;
        } else {
            $data['menu'] = 'Setting';
            return view('User.updateInfo', $data);
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function anyCalenderReport()
    {
        $data['menu'] = 'Report';
        if (Input::all()) {
            $rules = array(
                'start' => 'required|date_format:Y-m-d H:i',
                'end' => 'required|date_format:Y-m-d H:i',
            );
            /* Laravel Validator Rules Apply */
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()):
                $validationMessage = $validator->messages()->first();
                Session::flash('error', $validationMessage);
                return Redirect::back();
            else:
                $data['events'] = Schedule::where('start_time', '>=', Input::get('start'))
                    ->where('start_time', '<=', Input::get('end'))
                    ->get();
                if ($data['events']->isEmpty()) {
                    Session::flash('error', 'No Report Found Between This Time');
                    return Redirect::back();
                }
                return view('User.event', $data);
            endif;
        } else {
            return view('User.calenderReportView', $data);
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getEventTrash()
    {
        $data['menu'] = 'Trash';
        $data['events'] = Schedule::onlyTrashed()->orderBy('id', 'desc')->get();
        return view('User.eventTrash', $data);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getTableEvent()
    {
        $data['menu'] = 'Table';
        $data['events'] = Schedule::orderBy('id', 'desc')->paginate(500);
        return view('User.tableEvent', $data);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function anyTableReport()
    {
        $data['menu'] = 'Report';
        if (Input::all()) {
            $rules = array(
                'start' => 'required|date_format:Y-m-d H:i',
                'end' => 'required|date_format:Y-m-d H:i',
            );
            /* Laravel Validator Rules Apply */
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()):
                $validationMessage = $validator->messages()->first();
                Session::flash('error', $validationMessage);
                return Redirect::back();
            else:
                $data['events'] = Schedule::where('start_time', '>=', Input::get('start'))
                    ->where('start_time', '<=', Input::get('end'))
                    ->get();
                if ($data['events']->isEmpty()) {
                    Session::flash('error', 'No Report Found Between This Time');
                    return Redirect::back();
                }
                return view('User.tableEvent', $data);
            endif;
        } else {
            return view('User.tableReportView', $data);
        }
    }

    /**
     * @return \Illuminate\View\View|string
     */
    public function anyCreateEvent()
    {
        if (Input::all()) {
            $rules = array(
                'title' => 'required',
                'start' => 'required|date_format:Y-m-d H:i',
                'end' => 'required|date_format:Y-m-d H:i',
            );
            /* Laravel Validator Rules Apply */
            $validator = Validator::make(Input::all(), $rules);

            if (Input::get('reminder_date') || Input::get('reminder_email') || Input::get('reminder_text')) {
                $validator->each('reminder_date', ['required', 'date_format:Y-m-d']);
                $validator->each('reminder_email', ['required']);
                $validator->each('reminder_text', ['required']);
            }

            if ($validator->fails())
                return $validator->messages()->first();

            else {

                if (Input::get('reminder_date') && is_array(Input::get('reminder_date')) && !empty(Input::get('reminder_date')) && count(Input::get('reminder_date')) != count(array_unique(Input::get('reminder_date')))) {
                    $response['type'] = 'error';
                    $response['data'] = "Please don't Select Same Multiple Reminder Date.";
                    return Response::json($response);
                }

                if (Input::get('start') > Input::get('end'))
                    return 'Start Time Can not be Greater Than End Time';

                if (Input::get('start') == Input::get('end'))
                    return 'Start Time And End Time Can not be Same';

                $duplicate = Schedule::where('start_time', Input::get('start'))
                    ->where('end_time', Input::get('end'))
                    ->first();

                if ($duplicate)
                    return 'A Event is Already Created in This Time';

                $event = new Schedule();
                $event->title = Input::get('title');
                $event->start_time = Input::get('start');
                $event->end_time = Input::get('end');
                $event->user_id = Auth::user()->id;
                $event->save();

                if (Input::get('reminder_date') && is_array(Input::get('reminder_date')) && !empty(Input::get('reminder_date'))) {

                    for ($i = 0; $i <= count(Input::get('reminder_date')) - 1; $i++) {
                        $reminder = new ScheduleReminder();
                        $reminder->schedule_id = $event->id;
                        $reminder->reminder_date = Input::get('reminder_date')[$i];
                        $reminder->reminder_email = Input::get('reminder_email')[$i];
                        $reminder->reminder_text = Input::get('reminder_text')[$i];
                        $reminder->save();
                    }

                }

                Session::flash('success', 'Event Created Successfully');
                return 'true';
            }

        } else {
            $data['menu'] = 'Create Event';
            return view('User.createEvent', $data);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function postSaveEvent()
    {
        $rules = array(
            'title' => 'required',
            'start_time' => 'required|date_format:Y-m-d H:i',
            'end_time' => 'required|date_format:Y-m-d H:i',
        );
        /* Laravel Validator Rules Apply */
        $validator = Validator::make(Input::all(), $rules);
        if (Input::get('reminder_date') || Input::get('reminder_email') || Input::get('reminder_text')) {
            $validator->each('reminder_date', ['required', 'date_format:Y-m-d']);
            $validator->each('reminder_email', ['required']);
            $validator->each('reminder_text', ['required']);
        }
        if ($validator->fails()) {

            $validationMessage = $validator->messages()->first();
            $response['type'] = 'error';
            $response['data'] = $validationMessage;
            return Response::json($response);

        } else {

            if (Input::get('reminder_date') && is_array(Input::get('reminder_date')) && !empty(Input::get('reminder_date')) && count(Input::get('reminder_date')) != count(array_unique(Input::get('reminder_date')))) {
                $response['type'] = 'error';
                $response['data'] = "Please don't Select Same Multiple Reminder Date.";
                return Response::json($response);
            }

            if (Input::get('start_time') > Input::get('end_time')) {
                $response['type'] = 'error';
                $response['data'] = 'Start Time Can not be Greater Than End Time';
                return Response::json($response);
            }
            if (Input::get('start_time') == Input::get('end_time')) {
                $response['type'] = 'error';
                $response['data'] = 'Start Time And End Time Can not be Same';
                return Response::json($response);
            }

            $duplicate = Schedule::where('start_time', Input::get('start_time'))
                ->where('end_time', Input::get('end_time'))
                ->first();
            if ($duplicate) {
                $response['type'] = 'error';
                $response['data'] = 'A Event is Already Created in This Time';
                return Response::json($response);
            }
            $event = new Schedule();
            $event->title = Input::get('title');
            $event->start_time = Input::get('start_time');
            $event->end_time = Input::get('end_time');
            $event->user_id = Auth::user()->id;
            $event->save();

            if (Input::get('reminder_date') && is_array(Input::get('reminder_date')) && !empty(Input::get('reminder_date'))) {

                for ($i = 0; $i <= count(Input::get('reminder_date')) - 1; $i++) {
                    $reminder = new ScheduleReminder();
                    $reminder->schedule_id = $event->id;
                    $reminder->reminder_date = Input::get('reminder_date')[$i];
                    $reminder->reminder_email = Input::get('reminder_email')[$i];
                    $reminder->reminder_text = Input::get('reminder_text')[$i];
                    $reminder->save();
                }

            }

        }
        $response['type'] = 'success';
        $response['data'] = $event->id;
        return Response::json($response);
    }

    /**
     * @return string
     */
    public function postUpdateEvent()
    {
        $rules = array(
            'title' => 'required',
            'start' => 'required|date_format:Y-m-d H:i',
            'end' => 'required|date_format:Y-m-d H:i',
        );
        /* Laravel Validator Rules Apply */
        $validator = Validator::make(Input::all(), $rules);

        if (Input::get('reminder_date') || Input::get('reminder_email') || Input::get('reminder_text')) {
            $validator->each('reminder_date', ['required', 'date_format:Y-m-d']);
            $validator->each('reminder_email', ['required']);
            $validator->each('reminder_text', ['required']);
        }

        if ($validator->fails())
            return $validator->messages()->first();

        else {

            if (Input::get('reminder_date') && is_array(Input::get('reminder_date')) && !empty(Input::get('reminder_date')) && count(Input::get('reminder_date')) != count(array_unique(Input::get('reminder_date'))))
                return "Please don't Select Same Multiple Reminder Date.";

            if (Input::get('start') > Input::get('end'))
                return 'Start Time Can not be Greater Than End Time';

            if (Input::get('start') == Input::get('end'))
                return 'Start Time And End Time Can not be Same';

            $duplicate = Schedule::where('start_time', Input::get('start'))
                ->where('end_time', Input::get('end'))
                ->where('id', '!=', Input::get('id'))
                ->first();

            if ($duplicate)
                return 'A Event is Already Created in This Time';

            $event = Schedule::find(Input::get('id'));
            $event->title = Input::get('title');
            $event->start_time = Input::get('start');
            $event->end_time = Input::get('end');
            $event->save();

            ScheduleReminder::where('schedule_id', Input::get('id'))->forceDelete();
            if (Input::get('reminder_date') && is_array(Input::get('reminder_date')) && !empty(Input::get('reminder_date'))) {

                //for duplicate check from database
                /*for ($i = 0; $i <= count(Input::get('reminder_date')) - 1; $i++) {
                    $duplicateReminder = ScheduleReminder::where('schedule_id', $event->id)
                            ->where('reminder_date', Input::get('reminder_date')[$i])
                        ->first();
                    if($duplicateReminder)
                        return 'You are already created an reminder on '.Input::get('reminder_date')[$i];
                }*/

                for ($i = 0; $i <= count(Input::get('reminder_date')) - 1; $i++) {
                    $reminder = new ScheduleReminder();
                    $reminder->schedule_id = Input::get('id');
                    $reminder->reminder_date = Input::get('reminder_date')[$i];
                    $reminder->reminder_email = Input::get('reminder_email')[$i];
                    $reminder->reminder_text = Input::get('reminder_text')[$i];
                    $reminder->save();
                }

            }
        }
        return 'true';
    }

    /**
     * @param null $id
     * @return \Illuminate\View\View|string
     */
    public function anyEventUpdate($id = null)
    {
        if (Input::all()) {
            $rules = array(
                'title' => 'required',
                'start' => 'required|date_format:Y-m-d H:i',
                'end' => 'required|date_format:Y-m-d H:i',
            );
            /* Laravel Validator Rules Apply */
            $validator = Validator::make(Input::all(), $rules);

            if (Input::get('reminder_date') || Input::get('reminder_email') || Input::get('reminder_text')) {
                $validator->each('reminder_date', ['required', 'date_format:Y-m-d']);
                $validator->each('reminder_email', ['required']);
                $validator->each('reminder_text', ['required']);
            }

            if ($validator->fails())
                return $validator->messages()->first();
            else {

                if (Input::get('reminder_date') && is_array(Input::get('reminder_date')) && !empty(Input::get('reminder_date')) && count(Input::get('reminder_date')) != count(array_unique(Input::get('reminder_date'))))
                    return "Please don't Select Same Multiple Reminder Date.";

                if (Input::get('start') > Input::get('end'))
                    return 'Start Time Can not be Greater Than End Time';

                if (Input::get('start') == Input::get('end'))
                    return 'Start Time And End Time Can not be Same';

                $duplicate = Schedule::where('start_time', Input::get('start'))
                    ->where('end_time', Input::get('end'))
                    ->where('id', '!=', Input::get('id'))
                    ->first();

                if ($duplicate)
                    return 'A Event is Already Created in This Time';

                $event = Schedule::find(Input::get('id'));
                $event->title = Input::get('title');
                $event->start_time = Input::get('start');
                $event->end_time = Input::get('end');
                $event->save();

                ScheduleReminder::where('schedule_id', Input::get('id'))->forceDelete();
                if (Input::get('reminder_date') && is_array(Input::get('reminder_date')) && !empty(Input::get('reminder_date'))) {

                    //for duplicate check from database
                    /*for ($i = 0; $i <= count(Input::get('reminder_date')) - 1; $i++) {
                        $duplicateReminder = ScheduleReminder::where('schedule_id', $event->id)
                                ->where('reminder_date', Input::get('reminder_date')[$i])
                            ->first();
                        if($duplicateReminder)
                            return 'You are already created an reminder on '.Input::get('reminder_date')[$i];
                    }*/

                    for ($i = 0; $i <= count(Input::get('reminder_date')) - 1; $i++) {
                        $reminder = new ScheduleReminder();
                        $reminder->schedule_id = Input::get('id');
                        $reminder->reminder_date = Input::get('reminder_date')[$i];
                        $reminder->reminder_email = Input::get('reminder_email')[$i];
                        $reminder->reminder_text = Input::get('reminder_text')[$i];
                        $reminder->save();
                    }

                }

                Session::flash('success', 'Event Update Successfully');
                return 'true';
            }
        } else {
            $data['event'] = Schedule::find($id);
            $data['menu'] = 'Table';
            return view('User.eventUpdate', $data);
        }
    }

    /**
     * @return string
     */
    public function getTrashEvent()
    {
        $event = Schedule::find(Input::get('id'));
        $event->delete();
        return 'true';
    }

    /**
     * @return string
     */
    public function getDeleteEvent()
    {
        Schedule::onlyTrashed()->where('id', Input::get('id'))->forceDelete();
        return 'true';
    }

    /**
     * @return string
     */
    public function getRestoreEvent()
    {
        Schedule::onlyTrashed()->where('id', Input::get('id'))->restore();
        return 'true';
    }

    /**
     * @return Redirect
     */
    public function getEventAllRestore()
    {
        Schedule::onlyTrashed()->restore();
        Session::flash('success', 'All Trash Event Restored Successfully');
        return redirect('user/table-event');
    }

    /**
     * @return Redirect
     */
    public function getEventAllDelete()
    {
        Schedule::onlyTrashed()->forceDelete();
        Session::flash('success', 'All Trash Event Deleted Successfully');
        return redirect('user/table-event');
    }

    /**
     *
     */
    public function getSendReminderEmail()
    {
        $reminderList = ScheduleReminder::where('reminder_date', date('Y-m-d'))->get();

        $sendEmailNum = 0;
        foreach ($reminderList as $reminder) {
            $mailToAddress = explode(",", $reminder->reminder_email);
            foreach ($mailToAddress as $toAddress) {
                $sendEmailNum++;
                if($sendEmailNum == 15)
                    break;
                Mail::send('email.reminder', array('email' => $reminder), function ($message) use ($toAddress, $reminder) {
//                    $message->from(Auth::user()->email, Auth::user()->first_name . ' ' . Auth::user()->last_name);
                    $message->to($toAddress)->subject($reminder->Schedule->title);
                });
            }
        }
    }


}