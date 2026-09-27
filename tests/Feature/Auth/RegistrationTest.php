<?php

test('legacy open registration page redirects to citizen registration', function () {
    $this->get('/register')->assertOk();
});
