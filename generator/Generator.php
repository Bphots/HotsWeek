<?php
namespace hotsweek\generator;

use think\Db;
use hotsweek\generator\Counter;
use app\hotsweek\model\Period;

ini_set('memory_limit', '-1');

class Generator
{
    protected $date;
    protected $weekNumber;
    protected $periodID;
    protected $rootPath;
    protected $savePath;

    protected $readTime = [
        'buildRanking' => 0,
    ];
    protected $saveTime = [
        'countGlobal' => 0,
        'buildRanking' => 0,
        'countPersonal' => 0,
    ];
    protected $active = false;

    public function __construct($weekNumber)
    {
        $this->weekNumber = $weekNumber;
        $this->rootPath = ROOT_PATH . 'weeklyreport' . DS . $weekNumber . DS;
        $this->savePath = $this->rootPath . '%u' . DS . '%u' . DS;
    }

    public function __destruct()
    {
        $this->active and $this->destoryWeekData();
    }

    protected function collectWeekData()
    {
        // Build weekly temp database table
        $weekNumber = $this->weekNumber;
        Db::connect("hotsweek")->execute("CREATE TABLE hw_temp_player_base (INDEX(player_id)) AS (SELECT * FROM `hw_player_base` WHERE week_number = {$weekNumber})");
        Db::connect("hotsweek")->execute("CREATE TABLE hw_temp_player_heroes (INDEX(player_id)) AS (SELECT * FROM `hw_player_heroes` WHERE week_number = {$weekNumber})");
        Db::connect("hotsweek")->execute("CREATE TABLE hw_temp_player_enemies (INDEX(player_id), INDEX(player2_id)) AS (SELECT * FROM `hw_player_enemies` WHERE week_number = {$weekNumber})");
        Db::connect("hotsweek")->execute("CREATE TABLE hw_temp_player_mates (INDEX(player_id), INDEX(player2_id)) AS (SELECT * FROM `hw_player_mates` WHERE week_number = {$weekNumber})");
        Db::connect("hotsweek")->execute("CREATE TABLE hw_temp_player_rankings (INDEX(player_id)) AS (SELECT * FROM `hw_player_rankings` WHERE week_number = {$weekNumber})");

        Db::connect("hotsweek")->execute("ALTER TABLE hw_temp_player_base ADD PRIMARY KEY(id)");
        Db::connect("hotsweek")->execute("ALTER TABLE hw_temp_player_heroes ADD PRIMARY KEY(id)");
        Db::connect("hotsweek")->execute("ALTER TABLE hw_temp_player_enemies ADD PRIMARY KEY(id)");
        Db::connect("hotsweek")->execute("ALTER TABLE hw_temp_player_mates ADD PRIMARY KEY(id)");
        Db::connect("hotsweek")->execute("ALTER TABLE hw_temp_player_rankings ADD PRIMARY KEY(id)");
    }

    protected function destoryWeekData()
    {
        // Destory weekly temp database table
        Db::connect("hotsweek")->execute("DROP TABLE hw_temp_player_base");
        Db::connect("hotsweek")->execute("DROP TABLE hw_temp_player_heroes");
        Db::connect("hotsweek")->execute("DROP TABLE hw_temp_player_enemies");
        Db::connect("hotsweek")->execute("DROP TABLE hw_temp_player_mates");
        Db::connect("hotsweek")->execute("DROP TABLE hw_temp_player_rankings");
    }
    
    public function countGlobal()
    {
        $this->active or $this->collectWeekData();
        $this->active = true;
        $path = sprintf($this->savePath, 0, 0);
        $counter = new Counter;
        $counter->countBaseData();
        $counter->countHeroesData();
        $counter->countGlobalRankingsPlayerNumbers();
        $saveTime = $counter->save($path);
        $saveTime and $this->saveTime['countGlobal'] += $saveTime;
    }

    public function buildRanking()
    {
        $this->active or $this->collectWeekData();
        $this->active = true;
        $except = [0];
        $path = sprintf($this->savePath, 0, 0);
        $ranking = new RankingBuilder;
        $ranking->setPath($this->rootPath, $except);
        $ranking->rank();
        $saveTime = $ranking->save($path, 'ranking');
        $saveTime and $this->saveTime['buildRanking'] += $saveTime;
        $readTime = $ranking->getFileTime();
        $readTime and $this->readTime['buildRanking'] += $readTime;
    }

    public function countPersonal($playerID)
    {
        $this->active or $this->collectWeekData();
        $this->active = true;
        // For EXT3 file system
        $groupID = floor($playerID / 1000);
        $path = sprintf($this->savePath, $groupID, $playerID);
        $counter = new Counter;
        $counter->setPlayer($playerID);
        $counter->pushPlayerInfo();
        $counter->countBaseData();
        $counter->countHeroesData();
        $counter->countEnemiesData();
        $counter->countMatesData();
        $counter->countRankingsData();
        $saveTime = $counter->save($path);
        $saveTime and $this->saveTime['countPersonal'] += $saveTime;
    }

    public function getReadTime()
    {
        return $this->readTime;
    }

    public function getSaveTime()
    {
        return $this->saveTime;
    }
}
