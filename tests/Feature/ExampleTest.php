<?php

test('the application redirects to the claim list', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('claims.index'));
});
