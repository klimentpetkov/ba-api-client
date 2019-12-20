<?php
declare(strict_types = 1);

namespace App;


use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ApiProcessor
{
    /**
     * @var ApiConsumer
     */
    private $apiReader;
    /**
     * @var \Requests_Response
     */
    private $response;
    /**
     * @var TemplateRenderer
     */
    private $renderer;
    /**
     * @var FileCache
     */
    private $cache;

    /**
     * ApiProcessor constructor.
     */
    public function __construct()
    {
        $this->apiReader = new ApiConsumer(Config::API_URL, Config::API_USERNAME,Config::API_PASSWORD);
        $this->renderer = new TemplateRenderer();
        $this->cache = new FileCache();
    }

    /**
     * Reads the data from Api and loads response for further processing
     */
    protected function getData()
    {
        $this->response = $this->apiReader->readApi();
    }

    /**
     * Shows some data to the user
     */
    public function showData()
    {
        $message = "";
        $goAhead = true;
        if (!$this->cache->hasCachedData()) {
            $goAhead = $this->prepareApiData();
        }

        if ($goAhead) {
            $this->showPaginatedData();
        }
    }

    /**
     * Fetches a massage from the returned API response
     * @return string
     */
    protected function getResponseMessage() : string {
        return json_decode($this->response->body, true)['message'];
    }

    /**
     * Read data from API and prepare it for showing
     * @return bool
     */
    protected function prepareApiData() : bool
    {
        $result = true;
        $this->getData();
        switch ($this->response->status_code) {
            case 200:
                try {
                    $this->cache->writeCachedData($this->response->body);
                } catch (\Exception $e) {
                    echo "Cache error: " . $e->getMessage();
                }
                break;
            case 401:
                $message = "Authorization failed! Please check API authorization data!";
                $this->renderer->renderData('error.html', compact(['status', 'message']));
                $result = false;
                break;
            case 500:
                $serverMessage = $this->getResponseMessage();
                $message = $serverMessage ?? "Server error occured!";
                $this->renderer->renderData('error.html', compact(['status', 'message']));
                $result = false;
                break;
            default:
                $message = "A strange Error occured, please reload the page!";
                $this->renderer->renderData('error.html', compact(['status', 'message']));
                $result = false;
        }

        return $result;
    }

    /**
     * Reads cached data and prepare a pagination for showing it
     */
    protected function showPaginatedData()
    {
        try {
            $data = $this->cache->readCachedData();
        } catch (\Exception $e) {
            echo "Cache error: " . $e->getMessage();
        }

        $limit = (int)((isset($_GET['limit'])) ? $_GET['limit'] : Config::PAGINATOR_LIMIT);
        $page = (int)((isset($_GET['page'])) ? $_GET['page'] : 1);
        $totalRecords = count($data);

        if ($limit < 0) {
            $limit = Config::PAGINATOR_LIMIT;
        }
        if ($limit > $totalRecords) {
            $limit = $totalRecords;
        }

        $totalPages = ceil($totalRecords / $limit);

        if ($page < 1) {
            $page = 1;
        }

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $paginator = new Paginator($data, $limit);
        $paginatedData = $paginator->getPagedData($page);
        $paginatedLinks = $paginator->createLinks(Config::PAGINATOR_LINKS, 'pagination pagination-sm');

        $this->renderer->renderData('index.html', compact(['paginatedData', 'paginatedLinks']));
    }
}