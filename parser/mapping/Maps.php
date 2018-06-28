<?php

namespace hotsweek\parser\mapping;

const HANAMURA = 1;
const TOWERS_OF_DOOM = 2;
const INFERNAL_SHRINES = 3;
const BATTLEFIELD_OF_ETERNITY = 4;
const TOMB_OF_THE_SPIDER_QUEEN = 5;
const SKY_TEMPLE = 6;
const DRAGON_SHIRE = 7;
const BLACKHEARTS_BAY = 8;
const HAUNTED_MINES = 9;
const CURSED_HOLLOW = 10;
const GARDEN_OF_TERROR = 11;
const BRAXIS_HOLDOUT = 12;
const WARHEAD_JUNCTION = 13;
const VOLSKAYA_FOUNDRY = 14;
const ALTERAC_PASS = 15;

trait Maps
{
    protected $mapsMapping = [
        "Battlefield of Eternity" => BATTLEFIELD_OF_ETERNITY,
        "Campo de Batalha da Eternidade" => BATTLEFIELD_OF_ETERNITY,
        "Campo de batalla de la Eternidad" => BATTLEFIELD_OF_ETERNITY,
        "永恆戰場" => BATTLEFIELD_OF_ETERNITY,
        "永恒战场" => BATTLEFIELD_OF_ETERNITY,
        "영원의 전쟁터" => BATTLEFIELD_OF_ETERNITY,
        "Campos de Batalla de la Eternidad" => BATTLEFIELD_OF_ETERNITY,
        "Schlachtfeld der Ewigkeit" => BATTLEFIELD_OF_ETERNITY,
        "Champs de l’Éternité" => BATTLEFIELD_OF_ETERNITY,
        "Champs de l'Éternité" => BATTLEFIELD_OF_ETERNITY,
        "Champs de la Éternité" => BATTLEFIELD_OF_ETERNITY,
        "Вечная битва" => BATTLEFIELD_OF_ETERNITY,
        "Campi di Battaglia Eterni" => BATTLEFIELD_OF_ETERNITY,
        "Pole Bitewne Wieczności" => BATTLEFIELD_OF_ETERNITY,
        "Les champs de l’éternité" => BATTLEFIELD_OF_ETERNITY,
        "Les champs de l'éternité" => BATTLEFIELD_OF_ETERNITY,
        "Les champs de la éternité" => BATTLEFIELD_OF_ETERNITY,
        "Blackheart's Bay" => BLACKHEARTS_BAY,
        "Blackheart’s Bay" => BLACKHEARTS_BAY,
        "Baie de Cœur-Noir" => BLACKHEARTS_BAY,
        "Schwarzherzbucht" => BLACKHEARTS_BAY,
        "Bahía de Almanegra" => BLACKHEARTS_BAY,
        "Zatoka Czarnosercego" => BLACKHEARTS_BAY,
        "Baia di Cuornero" => BLACKHEARTS_BAY,
        "Бухта Черносерда" => BLACKHEARTS_BAY,
        "Baía do Coração Negro" => BLACKHEARTS_BAY,
        "블랙하트 항만" => BLACKHEARTS_BAY,
        "黑心湾" => BLACKHEARTS_BAY,
        "黑心灣" => BLACKHEARTS_BAY,
        "La baie de Cœur-Noir" => BLACKHEARTS_BAY,
        "Braxis Holdout" => BRAXIS_HOLDOUT,
        "Endstation Braxis" => BRAXIS_HOLDOUT,
        "布萊西斯實驗所" => BRAXIS_HOLDOUT,
        "브락시스 항전" => BRAXIS_HOLDOUT,
        "Resistência de Braxis" => BRAXIS_HOLDOUT,
        "Бойня на Браксисе" => BRAXIS_HOLDOUT,
        "Laboratoire de Braxis" => BRAXIS_HOLDOUT,
        "Resistencia en Braxis" => BRAXIS_HOLDOUT,
        "布拉克西斯禁区" => BRAXIS_HOLDOUT,
        "布莱克西斯禁区" => BRAXIS_HOLDOUT,
        "Placówka na Braxis" => BRAXIS_HOLDOUT,
        "Distaccamento di Braxis" => BRAXIS_HOLDOUT,
        "Le laboratoire de Braxis" => BRAXIS_HOLDOUT,
        "Cursed Hollow" => CURSED_HOLLOW,
        "Val Maudit" => CURSED_HOLLOW,
        "Verfluchtes Tal" => CURSED_HOLLOW,
        "Cuenca Maldita" => CURSED_HOLLOW,
        "Valle Maledetta" => CURSED_HOLLOW,
        "Przeklęta Kotlina" => CURSED_HOLLOW,
        "Проклятая лощина" => CURSED_HOLLOW,
        "Creux Maudit" => CURSED_HOLLOW,
        "Hondonada maldita" => CURSED_HOLLOW,
        "Clareira Maldita" => CURSED_HOLLOW,
        "저주받은 골짜기" => CURSED_HOLLOW,
        "诅咒谷" => CURSED_HOLLOW,
        "詛咒谷地" => CURSED_HOLLOW,
        "Cursed Hollow - Scaling Test" => CURSED_HOLLOW,
        "Val Maudit - Test d’échelonnage" => CURSED_HOLLOW,
        "Val Maudit - Test d'échelonnage" => CURSED_HOLLOW,
        "Valle Maledetta (Test adattamento)" => CURSED_HOLLOW,
        "Проклятая лощина: тест параметров" => CURSED_HOLLOW,
        "詛咒谷地 - 調整測試" => CURSED_HOLLOW,
        "Przeklęta Kotlina – Test skalowania" => CURSED_HOLLOW,
        "저주받은 골짜기 - 수치 변경 테스트" => CURSED_HOLLOW,
        "Clareira Maldita - Teste de Escalonamento" => CURSED_HOLLOW,
        "Verfluchtes Tal – Skalierungstest" => CURSED_HOLLOW,
        "诅咒谷 - 数值测试" => CURSED_HOLLOW,
        "Cuenca Maldita: prueba de escala" => CURSED_HOLLOW,
        "Hondonada maldita - Mapa de prueba" => CURSED_HOLLOW,
        "Sandbox (Cursed Hollow)" => CURSED_HOLLOW,
        "Bac à sable (val Maudit)" => CURSED_HOLLOW,
        "Mapa Fechado (Clareira Maldita)" => CURSED_HOLLOW,
        "Sandbox (Valle Maledetta)" => CURSED_HOLLOW,
        "Prueba grupal (Hondonada maldita)" => CURSED_HOLLOW,
        "샌드박스 (저주받은 골짜기)" => CURSED_HOLLOW,
        "Песочница (Проклятая лощина)" => CURSED_HOLLOW,
        "沙盒（诅咒谷）" => CURSED_HOLLOW,
        "Terreno de pruebas (Cuenca Maldita)" => CURSED_HOLLOW,
        "Sandbox (Verfluchtes Tal)" => CURSED_HOLLOW,
        "Sandbox (Przeklęta Kotlina)" => CURSED_HOLLOW,
        "試驗模式（詛咒谷地）" => CURSED_HOLLOW,
        "Sandbox (Hondonada maldita)" => CURSED_HOLLOW,
        "Bójka na Śnieżki" => CURSED_HOLLOW,
        "Choc au sommet" => CURSED_HOLLOW,
        "Combate bajo cero" => CURSED_HOLLOW,
        "Contenda na Neve" => CURSED_HOLLOW,
        "Contienda nevada" => CURSED_HOLLOW,
        "Rissa nella Neve" => CURSED_HOLLOW,
        "Schneeballschlacht" => CURSED_HOLLOW,
        "Snow Brawl" => CURSED_HOLLOW,
        "Потасовка со снежками" => CURSED_HOLLOW,
        "雪球乱斗" => CURSED_HOLLOW,
        "雪球大亂鬥" => CURSED_HOLLOW,
        "눈싸움 난투" => CURSED_HOLLOW,
        "Le val maudit" => CURSED_HOLLOW,
        "Bac à sable (Le val maudit)" => CURSED_HOLLOW,
        "Dragon Shire" => DRAGON_SHIRE,
        "Comté du dragon" => DRAGON_SHIRE,
        "Drachengärten" => DRAGON_SHIRE,
        "Condado del Dragón" => DRAGON_SHIRE,
        "Smocze Włości" => DRAGON_SHIRE,
        "Contea del Drago" => DRAGON_SHIRE,
        "Драконий край" => DRAGON_SHIRE,
        "Comarca del dragón" => DRAGON_SHIRE,
        "Condado do Dragão" => DRAGON_SHIRE,
        "용의 둥지" => DRAGON_SHIRE,
        "巨龙镇" => DRAGON_SHIRE,
        "巨龍郡" => DRAGON_SHIRE,
        "Le comté du dragon" => DRAGON_SHIRE,
        "Garden of Terror" => GARDEN_OF_TERROR,
        "Jardín del Terror" => GARDEN_OF_TERROR,
        "Ogród Grozy" => GARDEN_OF_TERROR,
        "Jardins de terreur" => GARDEN_OF_TERROR,
        "Garten der Ängste" => GARDEN_OF_TERROR,
        "Сад Ужасов" => GARDEN_OF_TERROR,
        "Giardino del Terrore" => GARDEN_OF_TERROR,
        "Jardim do Terror" => GARDEN_OF_TERROR,
        "PLACEHOLDER" => GARDEN_OF_TERROR,
        "공포의 정원" => GARDEN_OF_TERROR,
        "恐魔园" => GARDEN_OF_TERROR,
        "恐怖花園" => GARDEN_OF_TERROR,
        "Les jardins de terreur" => GARDEN_OF_TERROR,
        "Hanamura" => HANAMURA,
        "花村" => HANAMURA,
        "하나무라" => HANAMURA,
        "Ханамура" => HANAMURA,
        "Hanamura #6" => HANAMURA,
        "Haunted Mines" => HAUNTED_MINES,
        "Mines hantées" => HAUNTED_MINES,
        "Geisterminen" => HAUNTED_MINES,
        "Minas Encantadas" => HAUNTED_MINES,
        "Nawiedzone Kopalnie" => HAUNTED_MINES,
        "Miniere Infestate" => HAUNTED_MINES,
        "Призрачные копи" => HAUNTED_MINES,
        "Minas embrujadas" => HAUNTED_MINES,
        "Mina Assombrada" => HAUNTED_MINES,
        "죽음의 광산" => HAUNTED_MINES,
        "鬼灵矿" => HAUNTED_MINES,
        "亡骸礦坑" => HAUNTED_MINES,
        "La mine hantée" => HAUNTED_MINES,
        "Infernal Shrines" => INFERNAL_SHRINES,
        "Santuarios infernales" => INFERNAL_SHRINES,
        "Santuários Infernais" => INFERNAL_SHRINES,
        "Sagrarios Infernales" => INFERNAL_SHRINES,
        "Sanctuaires infernaux" => INFERNAL_SHRINES,
        "Höllenschreine" => INFERNAL_SHRINES,
        "煉獄聖壇" => INFERNAL_SHRINES,
        "불지옥 신단" => INFERNAL_SHRINES,
        "Piekielne kapliczki" => INFERNAL_SHRINES,
        "Altari Infernali" => INFERNAL_SHRINES,
        "Оскверненные святилища" => INFERNAL_SHRINES,
        "炼狱圣坛" => INFERNAL_SHRINES,
        "Les sanctuaires infernaux" => INFERNAL_SHRINES,
        "Sky Temple" => SKY_TEMPLE,
        "Temple céleste" => SKY_TEMPLE,
        "Templo celeste" => SKY_TEMPLE,
        "天空殿" => SKY_TEMPLE,
        "Tempel des Himmels" => SKY_TEMPLE,
        "Podniebna Świątynia" => SKY_TEMPLE,
        "Tempio Celeste" => SKY_TEMPLE,
        "Небесный храм" => SKY_TEMPLE,
        "天空神殿" => SKY_TEMPLE,
        "하늘 사원" => SKY_TEMPLE,
        "Le temple céleste" => SKY_TEMPLE,
        "Tomb of the Spider Queen" => TOMB_OF_THE_SPIDER_QUEEN,
        "Tumba de la reina araña" => TOMB_OF_THE_SPIDER_QUEEN,
        "蛛后之墓" => TOMB_OF_THE_SPIDER_QUEEN,
        "Tumba da Aranha Rainha" => TOMB_OF_THE_SPIDER_QUEEN,
        "거미 여왕의 무덤" => TOMB_OF_THE_SPIDER_QUEEN,
        "Tombe de la Reine araignée" => TOMB_OF_THE_SPIDER_QUEEN,
        "Grabkammer der Spinnenkönigin" => TOMB_OF_THE_SPIDER_QUEEN,
        "Grobowiec Pajęczej Królowej" => TOMB_OF_THE_SPIDER_QUEEN,
        "Tomba della Regina Ragno" => TOMB_OF_THE_SPIDER_QUEEN,
        "Гробница королевы пауков" => TOMB_OF_THE_SPIDER_QUEEN,
        "蛛后墓" => TOMB_OF_THE_SPIDER_QUEEN,
        "La tombe de la reine araignée" => TOMB_OF_THE_SPIDER_QUEEN,
        "Towers of Doom" => TOWERS_OF_DOOM,
        "Torres da Perdição" => TOWERS_OF_DOOM,
        "Torres de Fatalidad" => TOWERS_OF_DOOM,
        "Torres de la perdición" => TOWERS_OF_DOOM,
        "Tours du destin" => TOWERS_OF_DOOM,
        "Türme des Unheils" => TOWERS_OF_DOOM,
        "Wieże Zagłady" => TOWERS_OF_DOOM,
        "Башни Рока" => TOWERS_OF_DOOM,
        "厄運之塔" => TOWERS_OF_DOOM,
        "末日塔" => TOWERS_OF_DOOM,
        "파멸의 탑" => TOWERS_OF_DOOM,
        "Torri della Rovina" => TOWERS_OF_DOOM,
        "Les tours du destin" => TOWERS_OF_DOOM,
        "Volskaya Foundry" => VOLSKAYA_FOUNDRY,
        "Fundición de Volskaya" => VOLSKAYA_FOUNDRY,
        "Fundição Volskaya" => VOLSKAYA_FOUNDRY,
        "Volskaya" => VOLSKAYA_FOUNDRY,
        "볼스카야 공장" => VOLSKAYA_FOUNDRY,
        "伏斯凱亞鑄造廠" => VOLSKAYA_FOUNDRY,
        "沃斯卡娅铸造厂" => VOLSKAYA_FOUNDRY,
        "Fonderia Volskaya" => VOLSKAYA_FOUNDRY,
        "Fonderie Volskaya" => VOLSKAYA_FOUNDRY,
        "Odlewnia Volskaya Industries" => VOLSKAYA_FOUNDRY,
        "Volskaya-Fertigung" => VOLSKAYA_FOUNDRY,
        "Завод Вольской" => VOLSKAYA_FOUNDRY,
        "Sandbox (Volskaya Foundry)" => VOLSKAYA_FOUNDRY,
        "Sandbox (Volskaya-Fertigung)" => VOLSKAYA_FOUNDRY,
        "Sandbox (Odlewnia Volskaya Industries)" => VOLSKAYA_FOUNDRY,
        "沙盒（沃斯卡娅铸造厂）" => VOLSKAYA_FOUNDRY,
        "試驗模式（伏斯凱亞鑄造廠）" => VOLSKAYA_FOUNDRY,
        "Sandbox (Fundición de Volskaya)" => VOLSKAYA_FOUNDRY,
        "샌드박스 (볼스카야 공장)" => VOLSKAYA_FOUNDRY,
        "Песочница (Завод Вольской)" => VOLSKAYA_FOUNDRY,
        "Odlewnia Volskaya" => VOLSKAYA_FOUNDRY,
        "Mapa Fechado (Fundição Volskaya)" => VOLSKAYA_FOUNDRY,
        "Terreno de pruebas (Fundición de Volskaya)" => VOLSKAYA_FOUNDRY,
        "La fonderie Volskaya" => VOLSKAYA_FOUNDRY,
        "Bac à sable (La fonderie Volskaya)" => VOLSKAYA_FOUNDRY,
        "Sandbox (Fonderia Volskaya)" => VOLSKAYA_FOUNDRY,
        "Warhead Junction" => WARHEAD_JUNCTION,
        "Cruce nuclear" => WARHEAD_JUNCTION,
        "Estación Nuclear" => WARHEAD_JUNCTION,
        "Junção da Ogiva" => WARHEAD_JUNCTION,
        "Menace nucléaire" => WARHEAD_JUNCTION,
        "핵탄두 격전지" => WARHEAD_JUNCTION,
        "弹头枢纽站" => WARHEAD_JUNCTION,
        "核武戰地" => WARHEAD_JUNCTION,
        "Ядерный полигон" => WARHEAD_JUNCTION,
        "Sprengkopfmanufaktur" => WARHEAD_JUNCTION,
        "Poligon Nuklearny" => WARHEAD_JUNCTION,
        "Stazione Atomica" => WARHEAD_JUNCTION,
        "Alterac Pass" => ALTERAC_PASS,
        "Alteracpass" => ALTERAC_PASS,
        "Cañón de Alterac" => ALTERAC_PASS,
        "Garganta de Alterac" => ALTERAC_PASS,
        "Paso de Alterac" => ALTERAC_PASS,
        "Passe d’Alterac" => ALTERAC_PASS,
        "Passo d'Alterac" => ALTERAC_PASS,
        "Przełęcz Alterak" => ALTERAC_PASS,
        "Альтеракский перевал" => ALTERAC_PASS,
        "奥特兰克战道" => ALTERAC_PASS,
        "奧特蘭克隘口" => ALTERAC_PASS,
        "알터랙 고개" => ALTERAC_PASS,
    ];
}