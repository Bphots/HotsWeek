<?php
namespace hotsweek;

use app\hotsweek\model\PlayerBase;
use hotsweek\generator\Generator;

class WeeklyGenerator
{
    protected $weekNumber;
    protected $generator;

    public function __construct($weekNumber)
    {
        // Last weekNumber
        $this->weekNumber = $weekNumber;
        $this->generator = new Generator($this->weekNumber);
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
        $playerIDs = [];
        $path = ROOT_PATH . 'weeklyreport' . DS . $this->weekNumber;
        is_dir($path) or mkdir($path, 0755, true);
        $current = opendir($path);
        while (($file = readdir($current)) !== false) {
            $sub = $path . '/' . $file;
            if ($file == '.' || $file == '..') {
                continue;
            } elseif (is_numeric($file) && is_dir($sub)) {
                $playerIDs[] = (int)$file;
            }
        }
        return $playerIDs;
    }
}
