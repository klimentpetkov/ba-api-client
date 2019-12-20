<?php
declare(strict_types = 1);

namespace App;

use Requests;

class ApiConsumer
{
    private $url;
    private $user;
    private $pass;

    /**
     * ApiConsumer constructor.
     * @param $url
     * @param $user
     * @param $pass
     */
    public function __construct($url, $user, $pass)
    {

        $this->url = $url;
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * @return array
     */
    protected function getBasicHeaders()
    {
        return [
            "Accept" => "application/json",
            "Content-type" => "application/json",
            "Authorization" => 'Basic ' . base64_encode($this->user . ':' . $this->pass),
        ];
    }

    /**
     * @return \Requests_Response
     */
    public function readApi()
    {
        return Requests::get($this->url, $this->getBasicHeaders());
    }
}