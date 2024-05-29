<?php

use LaravelFCM\Response\GroupResponse;
use PHPUnit\Framework\Attributes\Test;

class GroupResponseTest extends FCMTestCase
{
    #[Test]
    public function it_construct_a_response_with_successes(): void
    {
        $notificationKey = 'notificationKey';

        $response = new \GuzzleHttp\Psr7\Response(200, [], '{
					"success": 2,
					"failure": 0
					}');

        $responseGroup = new GroupResponse($response, $notificationKey);

        $this->assertEquals(2, $responseGroup->numberSuccess());
        $this->assertEquals(0, $responseGroup->numberFailure());
        $this->assertCount(0, $responseGroup->tokensFailed());
    }

    #[Test]
    public function it_construct_a_response_with_failures(): void
    {
        $notificationKey = 'notificationKey';

        $response = new \GuzzleHttp\Psr7\Response(200, [], '{
					"success": 0,
					"failure": 2,
					"failed_registration_ids":[
					   "regId1",
					   "regId2"
					]}');

        $responseGroup = new GroupResponse($response, $notificationKey);

        $this->assertEquals(0, $responseGroup->numberSuccess());
        $this->assertEquals(2, $responseGroup->numberFailure());
        $this->assertCount(2, $responseGroup->tokensFailed());

        $this->assertEquals('regId1', $responseGroup->tokensFailed()[0]);
        $this->assertEquals('regId2', $responseGroup->tokensFailed()[1]);
    }

    #[Test]
    public function it_construct_a_response_with_partials_failures(): void
    {
        $notificationKey = 'notificationKey';

        $response = new \GuzzleHttp\Psr7\Response(200, [], '{
					"success": 1,
					"failure": 2,
					"failed_registration_ids":[
					   "regId1",
					   "regId2"
					]}');

        $responseGroup = new GroupResponse($response, $notificationKey);

        $this->assertEquals(1, $responseGroup->numberSuccess());
        $this->assertEquals(2, $responseGroup->numberFailure());
        $this->assertCount(2, $responseGroup->tokensFailed());
    }
}
