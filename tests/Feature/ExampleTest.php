<?php

/**
 * A basic test example.
 */
test('successful response')
    ->get('/')
    ->assertOk();
