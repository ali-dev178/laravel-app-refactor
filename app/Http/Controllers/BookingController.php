<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\DistanceFeedRequest;
use Response;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if($user_id = $request->get('user_id')) {
            $response = $this->repository->getUsersJobs($user_id);
        }
        elseif($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID'))
        {
            $response = $this->repository->getAll($request);
        }

        return response($response);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show(BookingRepository $bookingRepository)
    {   $job = $bookingRepository->with('translatorJobRel.user')->get();
        return response($job);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $response = $this->repository->store($request->__authenticatedUser, $data);

        return response($response);

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $data = $request->all();
        $cuser = $request->__authenticatedUser;
        $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $adminSenderEmail = config('app.adminemail');
        $data = $request->all();

        $response = $this->repository->storeJobEmail($data);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        if($user_id = $request->get('user_id')) {

            $response = $this->repository->getUsersJobsHistory($user_id, $request);
            return response($response);
        }

        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJob($data, $user);

        return response($response);
    }

    public function acceptJobWithId(Request $request)
    {
        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJobWithId($data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->repository->cancelJobAjax($data, $user);

        return response($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $data = $request->all();

        $response = $this->repository->endJob($data);

        return response($response);

    }

    public function customerNotCall(Request $request)
    {
        $data = $request->all();

        $response = $this->repository->customerNotCall($data);

        return response($response);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->repository->getPotentialJobs($user);

        return response($response);
    }

    public function distanceFeed(DistanceFeedRequest $request) // validation requests implented and used here
    {
        $message = ''; $result_type = 1; $status_code = 200; $data = [];

        $data = $request->all();

        /* Here if we have consistent request input formatting than we can create trait and reuse code in single inheritance.

        We created a method in traits and used in the base controller to access any of the other controller.
        So in thi smethod we pass $data as first parameter and array of attributes/features names that are need to be format as a second parameter

        We can use this method in several ways in several ways
            1. $this->formatInput($data, array('strAttrs' => ['jobid', 'session_time'])); // this will check the isset and empty condition
            2. $this->formatInput($data, array('boolAttrs' => ['flagged'])); // this is for formating and getting the result in yes/no form.
            3. $this->formatInput($data, array('jobid', 'session_time')); // in this case attributes will consider as strAttr as we discussed before */

        $data = $this->formatInput($data, array('strAttrs' => ['distance', 'time', 'jobid', 'session_time', 'admincomment'], 'boolAttrs' => ['flagged', 'manually_handled', 'by_admin']));
        $data['admin_comments'] = $data['admincomment'];

        /* if we already added these attributes in formatInput helper method than here we dont need to recheck again
            if ($admincomment || $session_time || $flagged || $manually_handled || $by_admin) */

        /* there was no exception hangling here i mean what if the job record not found with the $id. it mean record will not be updated but in responce we return the response with success message which is wrong */

        try {
            /* we can use both ways to update record with new data. In the older way we can use if we have some limited number of attributes need to be updated. But if we have not restriction wants to updated the the formatted data we can used it like this. */
            Job::findOrFail($id)->update($data);
        } catch (ModelNotFoundException $e) {
            // exception handling code will be here
        }

        $message = 'Record updated!';

        /* the api design should be consistant and standardised with responce containing all kind of basic information. So for this i have created a custom Api responce formatting method in the bas controller */
        $response = $this->makeResponse($data, $message, $result_type, $status_code);
        return Response::json($response)->header('Content-Length', strlen(json_encode($response)));
    }

    public function reopen(Request $request)
    {
        $response = $this->repository->reopen($request->all());
        return response($response);
    }

    public function resendNotifications(Request $request)
    {
        $data = $request->only('jobid');
        
        try {
            $job = $this->repository->find($data['jobid']);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationTranslator($job, $job_data, '*');
        } catch (Exception $e) {
            // exception handling code will be here
        }
        
        return response(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $data = $request->only('jobid');
        $job = $this->repository->find($data['jobid']);
        $job_data = $this->repository->jobToData($job);

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);
        }
    }

}
