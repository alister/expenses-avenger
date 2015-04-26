<?php
namespace AppBundle\Tests;

trait InsertingTestExpenses
{
    /**
     * Posting multiple sets of data - used internally
     *
     * the tests just make sure it works - and we've already formally tested it.
     */
    public function postNewExpenses(array $expenses)
    {
        foreach ($expenses as $expense) {
            $crawler  = $this->jsonRequest('POST', '/api/v1/expenses', $expense);
            $response = $this->client->getResponse();
            $this->assertJsonResponse($response, $statusCode = 201);    // 201: 'Created'
        }
    }

    public function filterSourceData()
    {
        return array(
            [
                'created_at'  => '2012-01-01',      // JAN 2012
                'amount'      => '9.99',
                'description' => 'description',
                'comment'     => 'comment',
            ],
            [
                'created_at'  => '2012-02-01',      // FEB 2012
                'amount'      => '9.99',
                'description' => 'description',
                'comment'     => 'comment',
            ]
        );
    }
}
