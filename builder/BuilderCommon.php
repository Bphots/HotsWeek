<?php

namespace hotsweek\builder;

use hotsweek\builder\BuilderMappings;

const SEP = '.';
const SET = 1;
const COUNTER = 2;
const COUNTER_SR = 3;
const COUNTER_MSRED = 4;
const FUNC = 5;
const INC = 1;
const TIMESTAMP = 'timestamp';
const MAP = 'map';
const WIN = 'IsWinner';
const PARTY_WIN = 'partyWin';
const PARTY = 'party';
const PLAYER_HERO_ID = 'playerHeroID';
const PLAYER2_HERO_ID = 'player2HeroID';
const GAME_MODE = 'gameMode';
const GAME_LENGTH = 'gameLength';
const BUILD_FIELD_DATA = 'buildFieldData';
const BUILD_JSON_FIELD_DATA = 'buildJsonFieldData';
const SCORE_RESULT = 'ScoreResult';
const MSRED = 'MiscellaneousScoreResultEventDictionary';

class BuilderCommon extends BuilderMappings
{
    protected $model;
    protected $base;
    protected $personal;
    protected $timestamp;
    protected $playerHeroID;
    protected $map;
    protected $gameMode;
    protected $gameLength;
    protected $party;
    protected $partyWin;
    protected $presets = [
        SET => [],
        COUNTER => [],
        COUNTER_SR => [],
        COUNTER_MSRED => [],
        FUNC => [],
    ];

    public function __construct($model, $base, $personal)
    {
        $this->model = $model;
        $this->base = $base;
        $this->personal = $personal;
        $this->timestamp = strtotime($base['Timestamp']);
        if (isset($this->heroesMapping[$personal['Character']])) {
            $this->playerHeroID = $this->heroesMapping[$personal['Character']];
        }
        if (isset($this->mapsMapping[$base['Map']])) {
            $this->map = $this->mapsMapping[$base['Map']];
        }
        $this->gameMode = $this->gameModesMapping[$base['GameMode']];
        $this->gameLength = $this->timespanToSeconds($base['ReplayLength']);
        $this->party = $personal[PARTY];
        if ($personal[WIN]) {
            $this->partyWin = $this->party;
        }
    }


    public function build()
    {
        $this->taskSet();
        $this->taskCounter(COUNTER);
        $this->taskCounter(COUNTER_SR);
        $this->taskCounter(COUNTER_MSRED);
        $this->taskFunc();
        return $this->model->save() ? true : false;
    }

    protected function taskSet()
    {
        $tasks = $this->presets[SET];
        foreach ($tasks as $task) {
            $field = $task[0];
            isset($task[1]) or $task[1] = $task[0];
            $value = $this->getTargetValue($task[1]) ?: 0;
            if (isset($task[2])) {
                if ($task[2] === true) {
                    // larger
                    if ($value <= $this->model->$field) {
                        continue;
                    }
                } elseif ($task[2] === false) {
                    // smaller
                    if ($value >= $this->model->$field) {
                        continue;
                    }
                } elseif (!$this->getTargetValue($task[4])) {
                    continue;
                }
            }
            $this->model->$field = $value;
        }
    }

    protected function taskCounter($type = COUNTER)
    {
        $tasks = $this->presets[$type];
        $this->counter($tasks, $type);
    }

    protected function taskFunc()
    {
        $tasks = $this->presets[FUNC];
        foreach ($tasks as $task) {
            if (isset($task[4])) {
                if (!$this->getTargetValue($task[4])) {
                    continue;
                }
            }
            $field = $task[0];
            $function = $task[1];
            $key = $this->getTargetValue($task[2]);
            $value = $this->getTargetValue($task[3]) ?: 0;
            $this->$function($field, $key, $value);
        }
    }

    protected function timespanToSeconds($time)
    {
        return strtotime($time) - strtotime(date('Y-m-d'));
    }

    protected function counter($tasks, $type = COUNTER)
    {
        foreach ($tasks as $task) {
            if (isset($task[2])) {
                if (!$this->getTargetValue($task[2])) {
                    continue;
                }
            }
            isset($task[1]) or $task[1] = $task[0];
            if ($type === COUNTER_SR) {
                $task[1] = SCORE_RESULT . SEP . $task[1];
            } elseif ($type === COUNTER_MSRED) {
                $task[1] = MSRED . SEP . $task[1];                
            }
            $field = $task[0];
            $value = $this->getTargetValue($task[1]) ?: 0;
            $this->model->$field += $value;
        }
    }

    protected function listInc(&$list, $field, $value = 1)
    {
        if (!$field) return;
        if (!is_array($field)) {
            $field = [$field];
        }
        foreach ($field as $each) {
            if (!isset($list[$each])) {
                $list[$each] = 0;
            }
            $list[$each] += $value;
        }
    }

    protected function jsonToArray($json)
    {
        return @json_decode($json, true) ? : [];
    }

    protected function arrayToJson($array)
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    protected function getTargetValue($value)
    {
        if (is_numeric($value) || is_null($value)) {
            return $value;
        } elseif (isset($this->$value)) {
            return $this->$value;
        } elseif (strtotime($value) !== false) {
            return $this->timespanToSeconds($value);
        } else {
            $path = explode(SEP, $value);
            $target = $this->personal;
            foreach ($path as $each) {
                if (isset($target[$each])) {
                    $target = $target[$each];
                } else {
                    $target = null;
                    break;
                }
            }
            if (!is_array($target)) {
                if (!(is_numeric($target) || is_null($target)) && strtotime($target) !== false) {
                    return $this->timespanToSeconds($target);
                }
            }
            return $target;
        }
    }

    protected function buildFieldData($field, $key, $value = 1)
    {
        if (!isset($this->model->$field)) {
            return;
        }
        $field = $field . '_' . $key;
        $this->model->$field += $value;
    }

    protected function buildJsonFieldData($field, $key, $value = 1)
    {
        if (!isset($this->model->$field)) {
            return;
        }
        $data = $this->jsonToArray($this->model->$field);
        $this->listInc($data, $key, $value);
        $this->model->$field = $this->arrayToJson($data);
    }
}
