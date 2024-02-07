<?php

require_once 'CSV.php';

class Book
{
    private array $baseAPI = [
        'ok' => false,
        'status' => 400,
        'result' => ""
    ];

    public function __construct()
    {
        if (! isset($_REQUEST['action'])) {
            http_response_code(400);
            unset($this->baseAPI['result']);
            $this->baseAPI['error'] = "parameters not send";
            $this->baseAPI['info'] = "با متد get یا post و با پارامتر action مقادیر مورد نظر را ارسال کنید.\n1. all - دریافت تمامی کتاب ها.\n2. random - دریافت یک کتاب به صورت رندوم.";
        } elseif ($_REQUEST['action'] <> 'all' and $_REQUEST['action'] <> 'random') {
            http_response_code(400);
            unset($this->baseAPI['result']);
            $this->baseAPI['error'] = "invalid parameter value";
            $this->baseAPI['info'] = "مقدار معتبر وارد نمائید.\n1. all, 2. random";
        } else {
            $csv = new \Jordan\CSV("book.csv");
            $action = $_REQUEST['action'];
            http_response_code(200);
            $this->baseAPI['ok'] = true;
            $this->baseAPI['status'] = 200;
            $this->baseAPI['result'] = match ($action) {
                'all' => $csv->content(),
                'random' => $csv->getRandomLine()
            };
        }
    }

    public function __toString(): string
    {
        header("Content-Type: application/json");
        return json_encode($this->baseAPI);
    }
}
echo new Book;
