<?php
namespace hotsweek\generator;

use think\Db;

class RankingSqlBuilder extends Presets
{
    static function run($weekNumber = null)
    {
        /* week number */
        $weekNumber or $weekNumber = floor((time() + 345600) / 604800);
        
        /* avg game length desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_game_length, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_game_length, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by (sum(t.game_length) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_game_length = x.rank_avg_game_length");
        
        /* total game length desc */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_total_game_length, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_total_game_length, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id order by (sum(t.game_length)) desc) o) x ON DUPLICATE KEY UPDATE rank_total_game_length = x.rank_total_game_length");
        
        /* total game desc */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_total_game, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_total_game, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id order by (sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_total_game = x.rank_total_game");
        
        /* total win desc */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_total_win, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_total_win, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id order by (sum(t.game_win)) desc) o) x ON DUPLICATE KEY UPDATE rank_total_win = x.rank_total_win");
        
        /* win rate desc (game >= 8) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_win_rate, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_win_rate, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 8 order by (sum(t.game_win) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_win_rate = x.rank_win_rate");
        
        /* avg solo kills desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_solo_kills, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_solo_kills, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by (sum(t.SoloKills) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_solo_kills = x.rank_avg_solo_kills");
        
        /* avg takedowns desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_takedowns, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_takedowns, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by (sum(t.Takedowns) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_takedowns = x.rank_avg_takedowns");
        
        /* avg deaths asc (win >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_deaths, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_deaths, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_win) >= 3 order by (sum(t.Deaths) / sum(t.game_total))) o) x ON DUPLICATE KEY UPDATE rank_avg_deaths = x.rank_avg_deaths");
        
        /* avg experience contribution desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_experience, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_experience, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by (sum(t.ExperienceContribution) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_experience = x.rank_avg_experience");
        
        /* avg hero damage desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_hero_damage, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_hero_damage, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by (sum(t.HeroDamage) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_hero_damage = x.rank_avg_hero_damage");
        
        /* avg siege damage desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_siege_damage, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_siege_damage, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by (sum(t.SiegeDamage) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_siege_damage = x.rank_avg_siege_damage");
        
        /* avg damage taken desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_damage_taken, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_damage_taken, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by (sum(t.DamageTaken) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_damage_taken = x.rank_avg_damage_taken");
        
        /* avg camp desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_camp, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_camp, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by (sum(t.MercCampCaptures) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_camp = x.rank_avg_camp");
        
        /* avg teamfight damage desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_teamfight_damage, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_teamfight_damage, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by (sum(t.TeamfightHeroDamage) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_teamfight_damage = x.rank_avg_teamfight_damage");
        
        /* avg teamfight damage taken desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_teamfight_damage_taken, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_teamfight_damage_taken, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by (sum(t.TeamfightDamageTaken) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_teamfight_damage_taken = x.rank_avg_teamfight_damage_taken");
        
        /* avg outnumbered deaths asc (win >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_avg_outnumbered_deaths, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_avg_outnumbered_deaths, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_win) >= 3 order by (sum(t.OutnumberedDeaths) / sum(t.game_total)) desc) o) x ON DUPLICATE KEY UPDATE rank_avg_outnumbered_deaths = x.rank_avg_outnumbered_deaths");
        
        /* quickmatch win rate desc (game >= 8) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_win_rate_quickMatch, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_win_rate_quickMatch, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total_QuickMatch) >= 8 order by (sum(t.game_win_QuickMatch) / sum(t.game_total_QuickMatch)) desc) o) x ON DUPLICATE KEY UPDATE rank_win_rate_quickMatch = x.rank_win_rate_quickMatch");
        
        /* unrankeddraft win rate desc (game >= 8) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_win_rate_unrankedDraft, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_win_rate_unrankedDraft, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total_UnrankedDraft) >= 8 order by (sum(t.game_win_UnrankedDraft) / sum(t.game_total_UnrankedDraft)) desc) o) x ON DUPLICATE KEY UPDATE rank_win_rate_unrankedDraft = x.rank_win_rate_unrankedDraft");
        
        /* teamleague win rate desc (game >= 8) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_win_rate_teamLeague, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_win_rate_teamLeague, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total_TeamLeague) >= 8 order by (sum(t.game_win_TeamLeague) / sum(t.game_total_TeamLeague)) desc) o) x ON DUPLICATE KEY UPDATE rank_win_rate_teamLeague = x.rank_win_rate_teamLeague");
        
        /* heroleague win rate desc (game >= 8) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_win_rate_heroLeague, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_win_rate_heroLeague, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total_HeroLeague) >= 8 order by (sum(t.game_win_HeroLeague) / sum(t.game_total_HeroLeague)) desc) o) x ON DUPLICATE KEY UPDATE rank_win_rate_heroLeague = x.rank_win_rate_heroLeague");
        
        /* kda desc (game >= 3) */
        Db::connect("hotsweek")->execute("INSERT INTO hw_player_rankings (player_id, rank_kda, week_number) SELECT * from ( SELECT o.player_id, (@rank := @rank + 1) as rank_kda, o.week_number from (SELECT t.player_id, t.week_number FROM `hw_player_base` t, (SELECT @rank := 0) r where t.week_number = $weekNumber group by t.player_id having sum(t.game_total) >= 3 order by ((sum(t.SoloKills) + sum(t.Assists) * 0.7) / (sum(t.Deaths) + 1 )) desc) o) x ON DUPLICATE KEY UPDATE rank_kda = x.rank_kda");
    }
}
