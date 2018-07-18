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
            } catch (\Exception $e) {
                Db::rollback();
            }
        }
    }

    protected function parse($key)
    {
        $report = $this->reports[$key];
        $content = @json_decode(file_get_contents($this->files[$key]), true);
        // unlink($this->files[$key]);
        $report->status = 0;
        $report->save();
        if ($content) {
            $parser = new ParseBattleReportCore($content, $report->outdated);
            $parser->build();
            unset($parser);
        }
    }
}
