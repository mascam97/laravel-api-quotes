<?php

namespace Tests\Unit\Models;

use App\Models\Quote;
use App\Models\User;
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
        $quote = Quote::factory()->create([
            'user_id' => User::factory()->create(),
        ]);

        $this->assertInstanceOf(User::class, $quote->user);
    }
}
