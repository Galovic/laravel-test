<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class GAHelper
{
    /** @var \Google_Client */
    private $client;

    /** @var string */
    private $profileId;

    /** @var \Google_Service_Analytics */
    private $service;

    /** @var Collection */
    private $errors;


    /**
     * GAHelper constructor.
     * @param array $token
     * @param string $profileId
     */
    public function __construct(array $token, $profileId)
    {
        $this->errors = collect([]);

        $this->profileId = $profileId;

        $this->client = \Google::getClient();
        $this->client->setAccessToken($token);
        $this->service = new \Google_Service_Analytics($this->client);
    }


    /**
     * Get data from GA service.
     *
     * @param string $from
     * @param string $to
     * @param string $metrics
     * @param array $options
     * @return \Google_Service_Analytics_GaData|null
     */
    public function getData( $from, $to, $metrics, array $options = []) {
        try {

            $options['samplingLevel'] = 'HIGHER_PRECISION';
            $data = $this->service->data_ga->get( 'ga:' . $this->profileId, $from, $to, $metrics, $options );

        } catch ( \Google_Service_Exception $e ) {
            $this->errors->push($e->getMessage() . ":[{$e->getCode()}]");
            return null;
        } catch ( \Exception $e ) {
            $this->errors->push($e->getMessage() . ":[{$e->getCode()}]");
            return null;
        }

        return $data;
    }


    /**
     * Has errors?
     *
     * @return bool
     */
    public function hasErrors() {
        return $this->errors->isNotEmpty();
    }


    /**
     * Get errors.
     *
     * @return Collection
     */
    public function getErrors() {
        return $this->errors;
    }
}
