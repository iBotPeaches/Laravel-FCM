<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use LaravelFCM\Response\Exceptions\InvalidRequestException;
use LaravelFCM\Sender\FCMSender;
use PHPUnit\Framework\Attributes\Test;

class DownstreamTest extends FCMTestCase
{
    #[Test]
    public function it_send_a_notification_to_a_device(): void
    {
        $response = new Response(200, [], '{ 
						  "multicast_id": 216,
						  "success": 3,
						  "failure": 3,
						  "canonical_ids": 1,
						  "results": [
							    { "message_id": "1:0408" }
	                      ]
					}');

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')->once()->andReturn($response);

        $tokens = 'uniqueToken';

        $fcm = new FCMSender($client, 'http://test.test');
        $fcm->sendTo($tokens);
    }

    #[Test]
    public function it_send_a_notification_to_more_than_1000_devices(): void
    {
        $response = new Response(200, [], '{ 
						  "multicast_id": 216,
						  "success": 3,
						  "failure": 3,
						  "canonical_ids": 1,
						  "results": [
							    { "message_id": "1:0408" },
							    { "error": "Unavailable" },
							    { "error": "InvalidRegistration" },
							    { "message_id": "1:1516" },
							    { "message_id": "1:2342", "registration_id": "32" },
							    { "error": "NotRegistered"}
	                      ]
					}');

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')->times(10)->andReturn($response);

        $tokens = [];
        for ($i = 0; $i < 10000; $i++) {
            $tokens[$i] = 'token_'.$i;
        }

        $fcm = new FCMSender($client, 'http://test.test');
        $fcm->sendTo($tokens);
    }

    #[Test]
    public function an_empty_array_of_tokens_thrown_an_exception(): void
    {
        $response = new Response(400, [], '{ 
						  "multicast_id": 216,
						  "success": 3,
						  "failure": 3,
						  "canonical_ids": 1,
						  "results": [
							    { "message_id": "1:0408" },
							    { "error": "Unavailable" },
							    { "error": "InvalidRegistration" },
							    { "message_id": "1:1516" },
							    { "message_id": "1:2342", "registration_id": "32" },
							    { "error": "NotRegistered"}
	                      ]
					}');

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')->once()->andReturn($response);

        $fcm = new FCMSender($client, 'http://test.test');
        $this->expectException(InvalidRequestException::class);
        $fcm->sendTo([]);
    }
}
