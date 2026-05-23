<?php

// The 'test' function is provided by Pest
test('the application returns a successful response', function () {
    // 1. Arrange: No specific setup needed for a simple GET route

    // 2. Act: Make a GET request to the root URL
    $response = $this->get('/');

    // 3. Assert: Check that the status code is 200
    $response->assertStatus(200);
});