<?php

namespace hotsweek;

use think\Db;
use app\hotsweek\model\BattleReport;
use hotsweek\parser\ParseBattleReportCore;

define('BATTLEREPORT_ROOT', ROOT_PATH . 'battlereports' . DS);

class ParseBattleReport
{
    protected $reports;
    protected $files;

    public function load($number)
    {
        $reports = BattleReport::limit($number)->where('status', 1)->select();
        if (!$reports) {
            return false;
        }
        $files = [];
        foreach ($reports as $key => $report) {
            $fingerprint = $report->fingerprint;
            $short = substr($fingerprint, 0, 2);
            $files[$key] = BATTLEREPORT_ROOT . $report->date . DS . $short . DS . $report->save_name;
        }
        $this->reports = $reports;
        $this->files = $files;
        return true;
    }

    public function parseAll()
    {
        foreach ($this->reports as $key => $report) {
            Db::startTrans();
            try {
                $this->parse($key);
                Db::commit();
                // delete Json file
                unlink($this->files[$key]);
            } catch (\Exception $e) {
                Db::rollback();
            }
        }
    }

    protected function parse($key)
    {
        $t1 = microtime(true);
        $report = $this->reports[$key];
        $contentOriginal = file_get_contents($this->files[$key]);
        // \think\Log::record('ParseBattleReportBegin: ' . $report->fingerprint);
        $content = @json_decode($contentOriginal, true);
        // gzcompress Json file
        $gzFileName = $this->files[$key] . '.gz';
        file_put_contents($gzFileName, gzcompress($contentOriginal));
        $report->status = 0;
        $report->save();
        if ($content) {
            $parser = new ParseBattleReportCore($content, $report->outdated);
            $parser->build();
            unset($parser);
        }
        $t2 = microtime(true);
        $delta = $t2 - $t1;
        // \think\Log::record('ParseBattleReportFinish: ' . $report->fingerprint . ' ' . $delta);
    }
}
