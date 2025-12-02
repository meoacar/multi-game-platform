<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\Api\V1\Matchmaking\JoinQueueRequest;
use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Facades\Validator;

/**
 * JoinQueueRequest Validation Test
 * 
 * Requirements: 2.1, 2.2, 2.3, 2.4, 2.5
 */
class JoinQueueRequestTest extends TestCase
{
    protected User $user;
    protected Game $game;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Her test için veritabanını temizle
        \Illuminate\Support\Facades\DB::table('games')->delete();
        \Illuminate\Support\Facades\DB::table('users')->delete();
        
        $this->user = User::factory()->create();
        $this->game = Game::factory()->create();
    }

    /**
     * Test: Geçerli veri ile validation başarılı olmalı
     * Requirement 2.1, 2.2, 2.3, 2.4, 2.5
     */
    public function test_valid_data_passes_validation(): void
    {
        $data = [
            'game_id' => $this->game->id,
            'mode' => 'squad',
            'min_rank' => 'Gold',
            'max_rank' => 'Diamond',
            'city' => 'İstanbul',
            'microphone_required' => true,
            'play_style' => 'aggressive',
        ];

        $request = new JoinQueueRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * Test: Mode zorunlu olmalı
     * Requirement 2.1
     */
    public function test_mode_is_required(): void
    {
        $data = [
            'game_id' => $this->game->id,
            // mode eksik
        ];

        $request = new JoinQueueRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('mode'));
    }

    /**
     * Test: Mode sadece squad, duo, solo olabilir
     * Requirement 2.1
     */
    public function test_mode_must_be_valid(): void
    {
        $data = [
            'game_id' => $this->game->id,
            'mode' => 'invalid_mode',
        ];

        $request = new JoinQueueRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('mode'));
    }

    /**
     * Test: Rank değerleri geçerli olmalı
     * Requirement 2.2
     */
    public function test_rank_values_must_be_valid(): void
    {
        $data = [
            'game_id' => $this->game->id,
            'mode' => 'squad',
            'min_rank' => 'InvalidRank',
        ];

        $request = new JoinQueueRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('min_rank'));
    }

    /**
     * Test: City maksimum 100 karakter olabilir
     * Requirement 2.3
     */
    public function test_city_max_length(): void
    {
        $data = [
            'game_id' => $this->game->id,
            'mode' => 'squad',
            'city' => str_repeat('a', 101), // 101 karakter
        ];

        $request = new JoinQueueRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('city'));
    }

    /**
     * Test: Microphone required boolean olmalı
     * Requirement 2.4
     */
    public function test_microphone_required_is_boolean(): void
    {
        $data = [
            'game_id' => $this->game->id,
            'mode' => 'squad',
            'microphone_required' => 'not_a_boolean',
        ];

        $request = new JoinQueueRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('microphone_required'));
    }

    /**
     * Test: Play style geçerli değerler olmalı
     * Requirement 2.5
     */
    public function test_play_style_must_be_valid(): void
    {
        $data = [
            'game_id' => $this->game->id,
            'mode' => 'squad',
            'play_style' => 'invalid_style',
        ];

        $request = new JoinQueueRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('play_style'));
    }

    /**
     * Test: Game ID zorunlu ve var olmalı
     */
    public function test_game_id_is_required_and_exists(): void
    {
        $data = [
            'mode' => 'squad',
            // game_id eksik
        ];

        $request = new JoinQueueRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('game_id'));

        // Var olmayan game_id
        $data['game_id'] = 99999;
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('game_id'));
    }

    /**
     * Test: Opsiyonel alanlar boş olabilir
     */
    public function test_optional_fields_can_be_null(): void
    {
        $data = [
            'game_id' => $this->game->id,
            'mode' => 'squad',
            // Diğer alanlar opsiyonel
        ];

        $request = new JoinQueueRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    /**
     * Test: Tüm geçerli rank değerleri kabul edilmeli
     * Requirement 2.2
     */
    public function test_all_valid_ranks_are_accepted(): void
    {
        $validRanks = ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror'];

        foreach ($validRanks as $rank) {
            $data = [
                'game_id' => $this->game->id,
                'mode' => 'squad',
                'min_rank' => $rank,
                'max_rank' => $rank,
            ];

            $request = new JoinQueueRequest();
            $validator = Validator::make($data, $request->rules());

            $this->assertTrue($validator->passes(), "Rank '{$rank}' kabul edilmedi");
        }
    }

    /**
     * Test: Tüm geçerli play style değerleri kabul edilmeli
     * Requirement 2.5
     */
    public function test_all_valid_play_styles_are_accepted(): void
    {
        $validStyles = ['aggressive', 'balanced', 'defensive'];

        foreach ($validStyles as $style) {
            $data = [
                'game_id' => $this->game->id,
                'mode' => 'squad',
                'play_style' => $style,
            ];

            $request = new JoinQueueRequest();
            $validator = Validator::make($data, $request->rules());

            $this->assertTrue($validator->passes(), "Play style '{$style}' kabul edilmedi");
        }
    }

    /**
     * Test: Tüm geçerli mode değerleri kabul edilmeli
     * Requirement 2.1
     */
    public function test_all_valid_modes_are_accepted(): void
    {
        $validModes = ['squad', 'duo', 'solo'];

        foreach ($validModes as $mode) {
            $data = [
                'game_id' => $this->game->id,
                'mode' => $mode,
            ];

            $request = new JoinQueueRequest();
            $validator = Validator::make($data, $request->rules());

            $this->assertTrue($validator->passes(), "Mode '{$mode}' kabul edilmedi");
        }
    }
}
