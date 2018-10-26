<?php

namespace hotsweek\parser;

use think\Log;
use think\Hook;
use app\hotsweek\model\Period;
use app\hotsweek\model\Player;
use hotsweek\parser\builder\BaseDataBuilder;
use hotsweek\parser\builder\HeroesDataBuilder;
use hotsweek\parser\builder\EnemiesDataBuilder;
use hotsweek\parser\builder\MatesDataBuilder;

// const WEEK_MIN = 0;
const WEEK_MIN = 2533;

class ParseBattleReportCore
{
    use \hotsweek\parser\mapping\Heroes;
    use \hotsweek\parser\mapping\GameModes;

    protected $outdated;
    protected $gameModeLimit;
    protected $timestamp;
    protected $periodID;
    protected $weekNumber;
    protected $thisWeekNumber;
    protected $date;
    protected $content;
    protected $contentBase;
    protected $contentPlayers;
    protected $players;

    public function __construct($content, $outdated)
    {
        $this->outdated = $outdated;
        $this->gameModeLimit = array_keys($this->gameModesMapping);
        $this->timestamp = strtotime($content['Timestamp']);
        $this->periodID = Period::order('ReplayBuild desc')->where('ReplayBuild', '<=', $content['ReplayBuild'])->value('id');
        $this->weekNumber = floor(($this->timestamp + 345600) / 604800) + 1;
        $this->thisWeekNumber = floor((time() + 345600) / 604800) + 1;
        $this->date = date("Y-m-d", $this->timestamp);
        $this->content = $content;
        $this->contentPlayers = $content['Players'];
        unset($content['Players']);
        $this->contentBase = $content;
        $this->buildParty($this->contentPlayers);
        // Log::record('------');
        // $t1 = microtime(true);
        $this->getPlayers();
        // $t2 = microtime(true);
        // Log::record('Function getPlayers() finish: ' . ($t2 - $t1));
    }

    public function build()
    {
        if ($this->timestamp === false || !$this->checkWeekNumber()) {
            return false;
        } elseif (!in_array($this->contentBase['GameMode'], $this->gameModeLimit)) {
            return false;
        }
        if ($this->outdated !== 2) {
            foreach ($this->players as $key => $player) {
                $personal = $this->contentPlayers[$key];
                // $t1 = microtime(true);
                $this->parseBaseData($player, $personal);
                // $t2 = microtime(true);
                // Log::record('Function parseBaseData() finish: ' . ($t2 - $t1));
                $this->parseHeroesData($player, $personal);
                // $t3 = microtime(true);
                // Log::record('Function parseHeroesData() finish: ' . ($t3 - $t2));
                $this->parseEnemiesData($player, $personal);
                // $t4 = microtime(true);
                // Log::record('Function parseEnemiesData() finish: ' . ($t4 - $t3));
                $this->parseMatesData($player, $personal);
                // $t5 = microtime(true);
                // Log::record('Function parseMatesData() finish: ' . ($t5 - $t4));
            }
        }
        $hookData = [
            'date' => $this->date,
            'weekNumber' => $this->weekNumber,
            'content' => $this->content,
            'outdated' => $this->outdated,
        ];
        Hook::listen('BattleReportParsingCompleted', $hookData);
        // if (isset($t5)) {
            // $t6 = microtime(true);
            // Log::record('Hook BattleReportParsingCompleted finish: ' . ($t6 - $t5));
        // }
        return true;
    }

    protected function getPlayers()
    {
        $players = [];
        foreach ($this->contentPlayers as $key => $each) {
            $player = Player::get([
                'BattleNetId'       =>  $each['BattleNetId'],
                'BattleNetRegionId' =>  $each['BattleNetRegionId'],
            ]);
            if (!$player) {
                $player = new Player;
                $player->save([
                    'BattleNetId'       =>  $each['BattleNetId'],
                    'BattleNetRegionId' =>  $each['BattleNetRegionId'],
                    'Name'              =>  $each['Name'],
                    'BattleTag'         =>  $each['BattleTag'],
                    'rename_gametime'   =>  $this->timestamp,
                ]);
            } else {
                if ($this->timestamp > $player['rename_gametime']) {
                    $player->save([
                        'Name'              =>  $each['Name'],
                        'BattleTag'         =>  $each['BattleTag'],
                        'rename_gametime'   =>  $this->timestamp,
                    ]);
                }
            }
            $players[$key] = $player;
        }
        $this->players = $players;
    }

    protected function parseBaseData($player, $personal)
    {
        $baseData = $player->baseData()->where([
            'date'          =>  $this->date,
            'week_number'   =>  $this->weekNumber,
        ])->force('PlayerBaseParserIndex')->find();
        if (!$baseData) {
            $player->baseData()->save([
                'date'          =>  $this->date,
                'period_id'     =>  $this->periodID,
                'week_number'   =>  $this->weekNumber,
            ]);
            $baseData = $player->baseData()->where([
                'date'          =>  $this->date,
                'week_number'   =>  $this->weekNumber,
            ])->force('PlayerBaseParserIndex')->find();
        }
        $builder = new BaseDataBuilder($baseData, $this->contentBase, $personal);
        $builder->build();
        unset($builder);
    }

    protected function parseHeroesData($player, $personal)
    {
        $heroID = $this->heroesMapping[$personal['Character']] ?? null;
        if (null === $heroID) {
            return;
        }
        $heroesData = $player->heroesData()->where([
            'hero_id'           =>  $heroID,
            'week_number'       =>  $this->weekNumber,
        ])->force('PlayerHeroesParserIndex')->find();
        if (!$heroesData) {
            $player->heroesData()->save([
                'hero_id'       =>  $heroID,
                'period_id'     =>  $this->periodID,
                'week_number'   =>  $this->weekNumber,
            ]);
            $heroesData = $player->heroesData()->where([
                'hero_id'       =>  $heroID,
                'week_number'   =>  $this->weekNumber,
            ])->force('PlayerHeroesParserIndex')->find();
        }
        $builder = new HeroesDataBuilder($heroesData, $this->contentBase, $personal);
        $builder->build();
        unset($builder);
    }

    protected function parseEnemiesData($player, $personal)
    {
        foreach ($this->contentPlayers as $key => $enemy) {
            if ($player->id <= $this->players[$key]->id) {
                continue;
            }
            if ($personal['Team'] == $enemy['Team']) {
                continue;
            }
            // $t1 = microtime(true);
            $enemiesData = $player->enemiesData()->where([
                'player2_id'        =>  $this->players[$key]->id,
                'week_number'       =>  $this->weekNumber,
            ])->force('PlayerEnemiesParserIndex')->find();
            // $t2 = microtime(true);
            // Log::record('parseEnemiesData query #1: ' . ($t2 - $t1));
            if (!$enemiesData) {
                $player->enemiesData()->save([
                    'player2_id'    =>  $this->players[$key]->id,
                    'period_id'     =>  $this->periodID,
                    'week_number'   =>  $this->weekNumber,
                ]);
                // $t3 = microtime(true);
                // Log::record('parseEnemiesData save: ' . ($t3 - $t2));
                $enemiesData = $player->enemiesData()->where([
                    'player2_id'    =>  $this->players[$key]->id,
                    'week_number'   =>  $this->weekNumber,
                ])->force('PlayerEnemiesParserIndex')->find();
                // $t4 = microtime(true);
                // Log::record('parseEnemiesData query #2: ' . ($t4 - $t3));
            }
            $builder = new EnemiesDataBuilder($enemiesData, $this->contentBase, $personal, $enemy);
            $builder->build();
            unset($builder);
            // $t5 = microtime(true);
            // Log::record('parseEnemiesData build finish: ' . ($t5 - $t4));
        }
    }

    protected function parseMatesData($player, $personal)
    {
        foreach ($this->contentPlayers as $key => $mate) {
            if ($player->id <= $this->players[$key]->id) {
                continue;
            }
            if ($personal['Team'] != $mate['Team']) {
                continue;
            }
            // $t1 = microtime(true);
            $matesData = $player->matesData()->where([
                'player2_id'        =>  $this->players[$key]->id,
                'week_number'       =>  $this->weekNumber,
            ])->force('PlayerMatesParserIndex')->find();
            // $t2 = microtime(true);
            // Log::record('parseMatesData query #1: ' . ($t2 - $t1));
            if (!$matesData) {
                $player->matesData()->save([
                    'player2_id'    =>  $this->players[$key]->id,
                    'period_id'     =>  $this->periodID,
                    'week_number'   =>  $this->weekNumber,
                ]);
                // $t3 = microtime(true);
                // Log::record('parseMatesData save: ' . ($t3 - $t2));
                $matesData = $player->matesData()->where([
                    'player2_id'    =>  $this->players[$key]->id,
                    'week_number'   =>  $this->weekNumber,
                ])->force('PlayerMatesParserIndex')->find();
                // $t4 = microtime(true);
                // Log::record('parseMatesData query #2: ' . ($t4 - $t3));
            }
            $builder = new MatesDataBuilder($matesData, $this->contentBase, $personal, $mate);
            $builder->build();
            unset($builder);
            // $t5 = microtime(true);
            // Log::record('parseMatesData build finish: ' . ($t5 - $t4));
        }
    }

    protected function buildParty($players)
    {
        $partys = [];
        foreach ($players as $key => $player) {
            $this->contentPlayers[$key]['party'] = 0;
            $value = $player['PartyValue'];
            if ($value) {
                $partys[$value][] = $key;
            }
        }
        foreach ($partys as $party) {
            $count = count($party);
            foreach ($party as $key) {
                $this->contentPlayers[$key]['party'] = $count;
            }
        }
    }

    protected function checkWeekNumber()
    {
        $delta = 3600;
        $_weekNumber = floor((time() - $delta + 345600) / 604800) + 1;
        return $this->weekNumber >= WEEK_MIN && ($this->thisWeekNumber === $this->weekNumber || $_weekNumber === $this->thisWeekNumber - 1);
    }
}
