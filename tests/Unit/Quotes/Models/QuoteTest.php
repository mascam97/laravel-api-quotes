<?php

namespace Tests\Unit\Quotes\Models;

use Domain\Quotes\Factories\QuoteFactory;
use Domain\Quotes\Models\Quote;
use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_excerpt()
    {
        $quote = new Quote();
        $quote->content = 'Sunt quaerat eveniet hic voluptatem quod quibusdam voluptas. Cum iusto assumenda mollitia ea ut consequuntur. Labore ipsam voluptatem delectus libero ab deserunt. Recusandae ut quia rem quia qui dolorem soluta. Exercitationem saepe vel minus dolore et et maiores.';

        $this->assertEquals('Sunt quaerat eveniet hic voluptatem quod quibusdam voluptas. Cum iusto assu...', $quote->excerpt);
    }

    public function test_belongs_to_user()
    {
        $quote = (new QuoteFactory)->withUser((new UserFactory)->create())->create();

        $this->assertInstanceOf(User::class, $quote->user);
    }
}