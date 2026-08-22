<?php

test('the api health endpoint returns a successful response', function () {
    $response = $this->getJson('/api/v1/hello');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Hello API V1']);
});
