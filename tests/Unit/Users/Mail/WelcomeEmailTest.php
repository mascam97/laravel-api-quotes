<?php

use Domain\Users\Mail\WelcomeEmail;

it('shows the welcome message', function () {
    $email = new WelcomeEmail();

    $email->assertSeeInHtml('Welcome to ');
});
