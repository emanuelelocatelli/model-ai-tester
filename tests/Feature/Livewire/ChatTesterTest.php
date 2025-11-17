<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ChatTester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ChatTesterTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(ChatTester::class)
            ->assertStatus(200);
    }
}
