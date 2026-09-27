<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee(__('Municipality of Sebha'))
        ->assertSee(__('Fair fuel distribution for the citizens of Sebha.'));
});

it('shows registration for guests', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(route('register'), false)
        ->assertSee(__('Create citizen account'));
});
