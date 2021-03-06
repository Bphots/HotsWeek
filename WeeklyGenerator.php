<?php
namespace hotsweek;

use app\hotsweek\model\PlayerBase;
use hotsweek\generator\Generator;

class WeeklyGenerator
{
    protected $weekNumber;
    protected $generator;

    protected $fileReadTimeGetPlayersFinished = 0;

    public function __construct($weekNumber)
    {
        // Last weekNumber
        $this->weekNumber = $weekNumber;
        $this->generator = new Generator($this->weekNumber);
    }

    public function __destruct()
    {
        unset($this->generator);
    }

    public function global()
    {
        $this->generator->countGlobal();
    }

    public function ranking()
    {
        $this->generator->buildRanking();
    }
    
    public function personal()
    {
        // Get players
        $playerIDs = $this->getPlayers();
        foreach ($playerIDs as $playerID) {
            $this->generator->countPersonal($playerID);
        }
    }

    public function getGeneratorReadTime()
    {
        $readTime = $this->generator->getReadTime();
        $readTime['getPlayersFinished'] = $this->fileReadTimeGetPlayersFinished;
        return $readTime;
    }

    public function getGeneratorSaveTime()
    {
        return $this->generator->getSaveTime();
    }

    protected function getPlayers()
    {
        $playersAll = $this->getPlayersAll();
        $playersFinished = $this->getPlayersFinished();
        return array_diff($playersAll, $playersFinished);
    }

    private function getPlayersAll()
    {
        return PlayerBase::where([
            'week_number' => $this->weekNumber
        ])->column('distinct player_id');
    }

    private function getPlayersFinished()
    {
        $t1 = microtime(true);
        $playerIDs = [];
        $path = ROOT_PATH . 'weeklyreport' . DS . $this->weekNumber;
        if (is_dir($path)) {
            $root = opendir($path);
            while (($groupID = readdir($root)) !== false) {
                $_path = $path . DS . $groupID;
                $_root = opendir($_path);
                while (($playerID = readdir($_root)) !== false) {
                    if (is_numeric($playerID) && is_dir($_path . DS . $playerID)) {
                        $playerIDs[] = (int)$playerID;
                    }
                }
            }
        }
        $t2 = microtime(true);
        $this->fileReadTimeGetPlayersFinished += $t2 - $t1;
        return $playerIDs;
    }
}
