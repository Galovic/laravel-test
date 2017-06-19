<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\GAHelper;
use App\Models\Web\Settings;
use Illuminate\Http\Request;
use App\Http\Requests;
use Flash;
use Auth;

class DashboardController extends AdminController
{

    /**
     * Dashboard
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        $this->setTitleDescription('Dashboard', 'Přehled');

        $authorizeLink = null;

        $tokenData = Settings::get('ga_token');
        $gaProfileId = null;

        if (!$tokenData) {
            $authorizeLink = route('admin.dashboard.authorization');
        } else {
            $gaProfileId = Settings::get('ga_profile_id');

            if (!$gaProfileId) {
                $authorizeLink = route('admin.dashboard.profiles');
            }
        }

        return view('admin.dashboard.index', compact('authorizeLink'));
    }


    /**
     * Get chart data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData (Request $request) {
        $profileId = Settings::get('ga_profile_id');
        $tokenData = Settings::get('ga_token');
        $token = (array)json_decode($tokenData);

        $from = '30daysAgo';
        $to = 'today';
        $metrics = 'ga:sessions,ga:users,ga:pageviews,ga:BounceRate,ga:organicSearches,ga:pageviewsPerSession,ga:newUsers';

        $ga = new GAHelper($token, $profileId);

        /** @var \Google_Service_Analytics_GaData $data */
        $data = $ga->getData($from, $to, $metrics, [
            'dimensions' => 'ga:date'
        ]);
        $totals = $data->getTotalsForAllResults();

        return response()->json([
            'errors' => $ga->getErrors(),
            'headers' => array_keys($totals),
            'rows' => $data->getRows(),
            'totals' => $totals
        ]);
    }


    /**
     * Show authorization form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAuthorizationForm() {
        $this->setTitleDescription('Dashboard', 'Autorizace');

        $googleClient = \Google::getClient();
        $authUrl = $googleClient->createAuthUrl();

        return view('admin.dashboard.authorization_form', compact('authUrl'));
    }


    /**
     * Get GA token with code from authorization form.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveGAToken(Request $request) {
        $validator = \Validator::make($request->all(), [
            'code' => 'required'
        ]);

        $client = \Google::getClient();
        $code = $request->input('code');
        $token = null;

        if (!$validator->fails()) {
            $client->authenticate($code);
            $token = $client->getAccessToken();
            if (!$token) {
                $validator->errors()->add('code', 'Kód se nepodařilo ověřit. Zkuste to prosím znovu.');
            }
        }

        if (!$validator->errors()->isEmpty()) {
            return redirect()->route('admin.dashboard.authorization')
                ->withErrors($validator);
        }

        Settings::set('ga_token', json_encode($token));
        Settings::forget('ga_profile_id');

        return redirect()->route('admin.dashboard.profiles');
    }


    /**
     * Show list of google analytics profiles
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showProfilesList() {
        $this->setTitleDescription('Dashboard', 'Profil');

        $tokenData = Settings::get('ga_token');

        if (!$tokenData) {
            return redirect()->route('admin.dashboard.authorization');
        }

        $token = (array)json_decode($tokenData);

        $client = \Google::getClient();
        $client->setAccessToken($token);
        $service = new \Google_Service_Analytics($client);

        $profilesList = [];
        $profiles = $service->management_profiles->listManagementProfiles('~all', '~all');

        foreach ($profiles->getItems() as $profile) {
            $profilesList[] = (object)[
                'id' => $profile->getId(),
                'accountId' => $profile->getAccountId(),
                'name' => $profile->getName(),
                'property' => $profile->getwebPropertyId(),
                'url' => $profile->getwebsiteUrl()
            ];
        }

        return view('admin.dashboard.choose_profile_list', compact('profilesList'));
    }


    /**
     * Save selected profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function saveSelectedProfile(Request $request) {
        $profileId = $request->input('profileId');
        $accountId = $request->input('accountId');
        $propertyId = $request->input('propertyId');
        $enableTracking = intval($request->input('enableTracking', 0));

        $tokenData = Settings::get('ga_token');

        if (!$tokenData || !$accountId || !$propertyId || !$profileId) {
            flash('Nepodařilo se vybrat profil.', 'warning');
            return $this->refresh();
        }

        $token = (array)json_decode($tokenData);

        $client = \Google::getClient();
        $client->setAccessToken($token);
        $service = new \Google_Service_Analytics($client);

        $profiles = $service->management_profiles->listManagementProfiles($accountId, $propertyId);

        $selectedProfile = null;
        foreach ($profiles->getItems() as $profile) {
            if ($profile->getId() === $profileId) {
                $selectedProfile = $profile;
                break;
            }
        }

        if (!$selectedProfile) {
            flash('Nepodařilo se vybrat profil.', 'warning');
            return $this->refresh();
        }

        Settings::set('ga_profile_id', $profileId);
        Settings::set('ga_property_id', $propertyId);
        Settings::set('ga_enable_tracking', $enableTracking);

        return response()->json([
            'redirect' => route('admin.dashboard')
        ]);
    }


    /**
     * Log off from google analytics.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function logOff() {
        Settings::forget('ga_token');
        Settings::forget('ga_profile_id');
        Settings::forget('ga_property_id');
        Settings::forget('ga_enable_tracking');

        flash('Google Analytics byl odpojen.', 'success');
        return $this->refresh();
    }
}