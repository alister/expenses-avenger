<?php
namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExpenseControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * we might not have any data there yet, but we should still get back 
     * valid JSON - just empty contents.
     */
    public function testQuery()
    {
        $crawler  = $this->jsonRequest('GET', '/api/v1/expenses.json', []);
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, $statusCode = 200);
        $this->assertJson($response->getContent());
    }

    /**
     * Explicitly test a single POST
     * 
     * @see postNewExpenses Posting multiple sets of data
     */
    public function testPost()
    {
        $data =  [
            'created_at'  => '2015-04-01',
            'amount'      => '9.99',
            'description' => 'description',
            'comment'     => 'comment',
        ];
        $crawler  = $this->jsonRequest('POST', '/api/v1/expenses', $data);
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, $statusCode = 201);    // 201: 'Created'
        $this->assertEmpty($response->getContent());

        $id = $this->getIdOfPost($response->headers->get('location'));
        return [$data, $id];
    }

    /**
     * GET the data we just POSTed
     * 
     * We are passed in the data that we posted ro Expense, and the ID it was given
     * 
     * @depends testPost
     */
    public function testGet(array $info)
    {
        list($postedData, $id) = $info;

        $crawler  = $this->jsonRequest('GET', "/api/v1/expenses/{$id}.json", []);
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, $statusCode = 200);
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        $this->assertEquals($id, $data['id']);

        // to compare them, we put the ID into the original data
        $postedData['id'] = $id;
        $this->assertArraysEqual($postedData, $data);
        // we need the id in there for the future.

        // We a playing a bit of HTTP-caterpillar here, passing data from one to another
        return $data;
    }

    /**
     * GET something that does not exist.
     * 
     * we could go 204 (No Content), or 404. We expect there to be something 
     * there though, so we check for a 404.
     * 
     * @see  http://stackoverflow.com/a/2195675/6216
     */
    public function testGetNotFound()
    {
        $id = PHP_INT_MAX;
        $crawler  = $this->jsonRequest('GET', "/api/v1/expenses/{$id}.json", []);
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
    }

    /**
     * PUT (update), based on the data we just POSTed
     * 
     * @depends testGet
     */
    public function testPut(array $data)
    {
        $newData = $data;
        $newData['description'] = "newly PUTted data";

        $id = $data['id'];
        $crawler  = $this->jsonRequest('PUT', "/api/v1/expenses/{$id}.json", $newData);
        $response = $this->client->getResponse();

        $this->assertEquals(204, $response->getStatusCode());   // 204: No Content (was updated OK)
        $this->assertEmpty($response->getContent());

        // go back and check the updated description
        $crawler  = $this->jsonRequest('GET', "/api/v1/expenses/{$id}.json", []);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, $statusCode = 200);
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals("newly PUTted data", $data['description']);
    }

    /**
     * FInally, we test deleting the item.
     * 
     * @depends testGet
     */
    public function testDelete(array $data)
    {
        $id = $data['id'];
        $crawler  = $this->jsonRequest('DELETE', "/api/v1/expenses/{$id}.json", $data);
        $response = $this->client->getResponse();

        $this->assertEquals(204, $response->getStatusCode());   // 204: No Content (was updated OK)
        $this->assertEmpty($response->getContent());

        // go back and check the updated description
        $crawler  = $this->jsonRequest('GET', "/api/v1/expenses/{$id}.json", []);
        $response = $this->client->getResponse();

        // we know it's not there now.
        $this->assertJsonResponse($response, $statusCode = 404);
        $this->assertJson($response->getContent(), "expected ");

        // and there is a message
        $data = json_decode($response->getContent(), true);
        $this->assertEquals([
             'code' => 404,
             'message' => 'Expense does not exist.',
        ], $data);
    }

    public function testDeleteNotFound()
    {
        $id = PHP_INT_MAX;
        $crawler  = $this->jsonRequest('DELETE', "/api/v1/expenses/{$id}.json", []);
        $response = $this->client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());   // 204 - no content
        // we don't care if it's a 404, or a 204 - either way, there's nothing there :)
    }

    /**
     * Convenience method to have phpunit::assertEquals canonicalize array ordering
     * 
     * NOTE: We DO NOT use the assertEquals($expected, $actual, $msg, $delta, $maxDepth, *$canonicalize* = true);
     * as it does a plain sort(), which loses the keys
     * 
     * This does a ksort of copies of the $expected and $actual arrays.
     */
    public function assertArraysEqual($expectedOrig, $actualOrig, $msg = "ordered arrays do not match")
    {
        $this->assertEquals($expectedOrig, $actualOrig, $msg, $delta = 0.0, $maxDepth = 10, $canonicalize = false);
        return;
        $expected = $expectedOrig;
        $actual   = $actualOrig;

        ksort($expected);
        ksort($actual);

        // http://stackoverflow.com/a/28189403/6216 use of "$canonicalize = true"
        $this->assertEquals($expected, $actual, $msg, $delta = 0.0, $maxDepth = 10, $canonicalize = false);
        ##dump($expected, $expectedOrig);die;
    }

    /**
     * Given a URL, get the ID from it
     * 
     * the 'location', is returned from the POST:expenses and contains the ID 
     * of the new resource.
     */ 
    private function getIdOfPost($locationUrl)
    {
        $path = explode('/', (parse_url($locationUrl, PHP_URL_PATH)));
        $id = array_pop($path);
        return $id;
    }

    /**
     * Put a few items into the DB, and then query for what would be a subset
     */
    public function testQueryFilter()
    {
        $queryFilterSource = $this->filterSourceData();
        $this->postNewExpenses($queryFilterSource);

        $data = $this->dataFromApi('GET', '/api/v1/expenses.json');
        $this->assertGreaterThan(2, $data);

        // this does depend on only ONE record being POSTed (here, from 
        // filterSourceData) that is within the date range
        $data = $this->dataFromApi('GET', '/api/v1/expenses.json?startDate=2012-01-01&endDate=2012-01-07');
        $this->assertCount(1, $data);
    }

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

    public function dataFromApi($method, $url)
    {
        $crawler  = $this->jsonRequest($method, $url, []);
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, $statusCode = 200);
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);
        return $data;
    }

    // {{{ copied from Bazinga\Bundle\RestExtraBundle\Test\WebTestCase
    // We don't need the bundle, but these couple of functions are useful

    protected function assertJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }

    protected function jsonRequest($verb, $endpoint, array $data = array())
    {
        $data = empty($data) ? null : json_encode($data);

        return $this->client->request($verb, $endpoint,
            array(),
            array(),
            array(
                'HTTP_ACCEPT'  => 'application/json',
                'CONTENT_TYPE' => 'application/json'
            ),
            $data
        );
    }

    // }}}
}
