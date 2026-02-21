<?php
/**
 * Template Name: Navia.php
 * Description: Template for displaying Genshin Impact character builds AYAKA.
 */

get_header(); ?>
<style>
    @media only screen and (min-width: 991px) {
        body {
            padding: 0px 15px;
            margin: 0px auto;
        }
    }
</style>

<div class="wrapper-lb1">
    <div id="nn_lb1" data-google-query-id="pub-9458790149381361">
        <!--google ads-->
        <script async
            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9458790149381361"
            crossorigin="anonymous"></script>
        <!-- ディスプレイ広告 1,105*90 -->
        <ins class="adsbygoogle" style="display:inline-block;width:1105px;height:90px"
            data-ad-client="ca-pub-9458790149381361" data-ad-slot="8136475858"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        <!--google ads end-->
    </div>
</div>

<div class="row">
    <main class="content" style="background: #1c1f46;">
        <div class="character">
            <!-- Character Intro Section -->
            <div class="character-intro">
                <!-- Character Image -->
                <?php
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Navia.png';
                $image_alt = 'Navia';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Navia Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_geo.png"
                            alt="Geo">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_sword.png"
                            alt="Claymore">Claymore </div>
                    <div class="character-role">Main DPS</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

                    $materials = array(
                        'Equity' => 'Equity',
                        'Prithiva Topaz Sliver' => 'Prithiva Topaz Sliver',
                        'Spring of the First Dewdrop' => 'Spring of the First Dewdrop',
                        'Transoceanic Pearl' => 'Transoceanic Pearl',
                        "Artificed Spare Clockwork Component - Coppelius" => "Artificed Spare Clockwork Component - Coppelius",
                        'Lightless Silk String' => 'Lightless Silk String',
                    );

                    //ucwords 最初の文字を大文字にかえる
                    foreach ($materials as $image_filename => $material_name):
                        $formatted_material_name = ucwords(str_replace('_', ' ', $material_name));
                        $image_file_path = $image_base_path . str_replace(' ', '_', $image_filename) . '.png';
                        ?>
                        <div class="character-materials-item">
                            <img class="character-materials-icon" src="<?php echo esc_url($image_file_path); ?>"
                                alt="<?php echo esc_attr($formatted_material_name); ?>">
                            <div class="character-materials-name">
                                <?php echo esc_html($formatted_material_name); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!-- Character Build Section -->

                    <div class="character-build">
                        <!-- Navia Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Navia Best Weapons</h2>
                            <div class="character-build-weapons">
                                <?php
                                $weapons = array(
                                    array("Verdict", 1, 5),
                                    array('Serpent Spine', 2, 3),
                                    array("Wolf's Gravestone", 3, 5),
                                    array('Beacon of the Reed Sea', 4, 5),
                                    array('Redhorn Stonethresher', 5, 5)
                                );

                                foreach ($weapons as $weapon):
                                    $weaponRank = $weapon[1];
                                    $weaponRarity = $weapon[2];
                                    $weaponName = str_replace('_', ' ', $weapon[0]);
                                    ?>
                                    <div class="character-build-weapon">
                                        <div class="character-build-weapon-rank"><?php echo $weaponRank; ?></div>
                                        <img class="character-build-weapon-icon rarity-<?php echo $weaponRarity; ?>"
                                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapons/<?php echo urlencode(str_replace(' ', '_', $weaponName)); ?>.png"
                                            alt="<?php echo $weaponName; ?>">
                                        <div class="character-build-weapon-name"><?php echo $weaponName; ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Navia Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Navia Best Artifacts</h2>

                            <?php
                            $artifacts = array(
                                array('Nighttime Whispers in the Echoing Woods', 1),
                                array("Marechaussee Hunter", 2),
                                array("Golden Troupe", 3),
                                array("Archaic Petra", 4),
                                array("Nighttime Whispers in the Echoing Woods", 4),
                                array("Archaic Petra", 5),
                                array("Golden Troupe", 5),
                            );


                            $manualRanks = array(4, 4, 4, 2, 2, 2, 2);

                            $previousRank = null; // 前のアートファクトのランクを格納する変数
                            
                            foreach ($artifacts as $index => $artifact):
                                $artifactName = $artifact[0];
                                $artifactRank = $artifact[1];

                                // アーティファクトの画像URLを動的に生成（大文字小文字を区別せず）
                                $imageUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/" . str_replace(' ', '_', strtolower($artifactName)) . ".png";

                                // ランクが変わったら新しいコラムを開始
                                if ($previousRank !== $artifactRank) {
                                    // 前のアートファクトがあれば閉じる
                                    if ($previousRank !== null) {
                                        echo '</div>';
                                    }
                                    echo '<div class="character-build-weapon">';
                                    $previousRank = $artifactRank;
                                }

                                echo '<div class="character-build-weapon-rank">' . esc_html($artifactRank) . '</div>';
                                echo '<div class="character-build-weapon-content">';
                                echo '<img class="character-build-weapon-icon rarity-5" src="' . esc_url($imageUrl) . '" alt="' . esc_attr($artifactName) . '">';
                                echo '<div class="character-build-weapon-name">' . esc_html(ucwords($artifactName)) . '</div>';
                                echo '<div class="character-build-weapon-count">' . esc_html($manualRanks[$index]) . '</div>';
                                echo '</div>';

                                // 最後のアートファクトなら閉じる
                                if ($index === count($artifacts) - 1) {
                                    echo '</div>';
                                }
                            endforeach;
                            ?>
                        </div>
                    </div>




                    <!-- Navia Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Navia Best Stats</h2>
                        <div class="character-stats-item"><b>Sands:</b> ATK%</div>
                        <div class="character-stats-item"><b>Goblet:</b> Geo DMG%</div>
                        <div class="character-stats-item"><b>Circlet:</b> CRIT Rate/CRIT DMG%</div>
                        <div class="character-stats-item full"><b>Substats:</b> Energy Recharge > CRIT Rate/ CRIT DMG > ATK</div>
                    </div>
                    <!-- Character Credit Link --><a
                        href="https://docs.google.com/spreadsheets/d/e/2PACX-1vRq-sQxkvdbvaJtQAGG6iVz2q2UN9FCKZ8Mkyis87QHFptcOU3ViLh0_PJyMxFSgwJZrd10kbYpQFl1/pubhtml#"
                        target="_blank" class="character-credit">Character Builds by Genshin Impact Helper →</a>
                </div>

                <div class="wrapper-lb1">
                    <div id="nn_lb1" data-google-query-id="pub-9458790149381361">
                        <script async
                            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9458790149381361"
                            crossorigin="anonymous"></script>
                        <!-- ディスプレイ広告 -->
                        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-9458790149381361"
                            data-ad-slot="3081506014" data-ad-format="auto" data-full-width-responsive="true"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                </div>

                <div class="wrapper-mpu1" style="margin-bottom: 0px;">
                    <div id="nn_mobile_mpu2"></div>
                </div>

                <!-- ナビゲーション -->
                <div class="character-navigation">
                    <a class="character-navigation-link" href="#teams">Teams</a>
                    <a class="character-navigation-link" href="#passives">Passives</a>
                    <a class="character-navigation-link" href="#talents">Talents</a>
                    <a class="character-navigation-link" href="#constellations">Constellations</a>
                    <a class="character-navigation-link" href="#ascension">Ascension</a>
                    <a class="character-navigation-link" href="#showcase">Showcase</a>
                </div>

                <!-- JavaScript -->
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        document.querySelectorAll('.character-navigation-link').forEach(function (link) {
                            link.addEventListener('click', function (event) {
                                event.preventDefault();

                                // クリックされたリンクの href 属性の値（対応するセクションの id）を取得
                                var targetId = this.getAttribute('href').substring(1);

                                // 対応するセクションの要素を取得
                                var targetElement = document.getElementById(targetId);

                                if (targetElement) {
                                    targetElement.scrollIntoView({ behavior: 'smooth' });
                                }
                            });
                        });
                    });
                </script>

                <!-- Character Teams Section -->
                <div class="character-teams" id="teams">
                    <h2 class="character-category">Best Navia Teams</h2>
                    <!-- Navia Freeze Team -->
                    <div class="character-team">
                        <div class="character-team-name">Navia Vaporize Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Navia_Teams1["Navia"] = array("element" => "geo", "rarity" => "rarity-5");
                                $Navia_Teams1["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Navia_Teams1["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                $Navia_Teams1["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Navia_Teams1 as $name => $info):
                                    ?>
                                    <div class="character-portrait character-teams"> <a
                                            href="https://kaztokyo.sakura.ne.jp/genshin-impact-blog-<?php echo strtolower($name); ?>-best-build">
                                            <img class="character-icon <?php echo $info['rarity']; ?>"
                                                src="<?php echo get_template_directory_uri(); ?>/images/genshin/<?php echo $name; ?>.png"
                                                width="70px" height="70px" alt="<?php echo $name; ?>">
                                            <img class="character-type"
                                                src="<?php echo get_template_directory_uri(); ?>/images/genshin/element_<?php echo $info['element']; ?>.png"
                                                width="24px" height="24px" alt="<?php echo $info['element']; ?>">
                                            <div class="character-name"><?php echo $name; ?></div>
                                        </a> </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Navia/Ganyu Mono Cryo Team -->
                    <div class="character-team">
                        <div class="character-team-name">Navia/Raiden National Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Navia_Teams2["Navia"] = array("element" => "geo", "rarity" => "rarity-5");
                                $Navia_Teams2["Raiden"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Navia_Teams2["Jean"] = array("element" => "anemo", "rarity" => "rarity-5");
                                $Navia_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-5");

                                // キャラクター情報を出力
                                foreach ($Navia_Teams2 as $name => $info):
                                    ?>
                                    <div class="character-portrait"> <a
                                            href="https://kaztokyo.sakura.ne.jp/genshin-impact-blog-<?php echo strtolower($name); ?>-best-build">
                                            <img class="character-icon <?php echo $info['rarity']; ?>"
                                                src="<?php echo get_template_directory_uri(); ?>/images/genshin/<?php echo $name; ?>.png"
                                                width="70px" height="70px" alt="<?php echo $name; ?>">
                                            <img class="character-type"
                                                src="<?php echo get_template_directory_uri(); ?>/images/genshin/element_<?php echo $info['element']; ?>.png"
                                                width="24px" height="24px" alt="<?php echo $info['element']; ?>">
                                            <div class="character-name"><?php echo $name; ?></div>
                                        </a> </div>
                                <?php endforeach; ?>
                                <!-- Add character information as needed -->
                            </div>
                        </div>
                    </div>
                    <!-- Navia/Ganyu Furina Hydro Team -->
                    <div class="character-team">
                        <div class="character-team-name">Navia/blooming intensely Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Navia_Teams3["Navia"] = array("element" => "geo", "rarity" => "rarity-5");
                                $Navia_Teams3["Kuki Shinobu"] = array("element" => "electro", "rarity" => "rarity-4");
                                $Navia_Teams3["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Navia_Teams3["Collei"] = array("element" => "dendro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Navia_Teams3 as $name => $info):
                                    ?>
                                    <div class="character-portrait"> <a
                                            href="https://kaztokyo.sakura.ne.jp/genshin-impact-blog-<?php echo strtolower($name); ?>-best-build">
                                            <img class="character-icon <?php echo $info['rarity']; ?>"
                                                src="<?php echo get_template_directory_uri(); ?>/images/genshin/<?php echo $name; ?>.png"
                                                width="70px" height="70px" alt="<?php echo $name; ?>">
                                            <img class="character-type"
                                                src="<?php echo get_template_directory_uri(); ?>/images/genshin/element_<?php echo $info['element']; ?>.png"
                                                width="24px" height="24px" alt="<?php echo $info['element']; ?>">
                                            <div class="character-name"><?php echo $name; ?></div>
                                        </a> </div>
                                <?php endforeach; ?>
                                <!-- Add character information as needed -->
                            </div>
                        </div>
                    </div>
                    <?php
                    // Naviaの情報を格納する配列
                    $NaviaInfo = array(
                        "NormalAttack" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png",
                            "title" => "Normal Attack",
                            "name" => "Blunt Refusal",
                            "description" => "Normal Attack
                            Performs up to 6 consecutive spear strikes.
                            
                            Charged Attack
                            Drains Stamina over time to perform continuous spinning attacks against all nearby opponents.
                            At the end of the sequence, performs a more powerful slash.
                            
                            Plunging Attack
                            Plunges from mid-air to strike the ground below, damaging opponents along the path and dealing AoE DMG upon impact.
                            
                            Masque of the Red Death\"Masque of the Red Death\" state, where her Normal, Charged, and Plunging Attacks will be converted to deal Pyro DMG. This cannot be overridden.
                            When in the \"Masque of the Red Death\" state, Navia's Normal Attacks will deal extra DMG to opponents on hit that scales off her ATK multiplied by a certain ratio of her current Bond of Life percentage. This will consume 7.5% of said current Bond of Life. Her Bond of Life can be consumed this way every 0.03s. When her Bond of Life is consumed in this manner, All Is Ash's CD will decrease by 0.8s."
                        ),
                        "ElementalSkill" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Navia/talent_2.png",
                            "title" => "Elemental Skill",
                            "name" => "Ceremonial Crystalshot",
                            "description" => <<<EOT
                            When a character in the party obtains an Elemental Shard created from the Crystallize reaction, Navia will gain 1 Crystal Shrapnel stack. Navia can have up to 6 stacks of Crystal Shrapnel at once. Each time Crystal Shrapnel gain is triggered, the duration of the Crystal Shrapnel stacks you already have will be reset.When she fires, Navia will consume all Crystal Shrapnel stacks and open her elegant yet lethal Gunbrella, firing multiple Rosula Shardshots that can penetrate opponents, dealing Geo DMG to opponents hit.When 0/1/2/3 or more stacks of Crystal Shrapnel are consumed, 5/7/9/11 Rosula Shardshots will be fired respectively. The more Rosula Shardshots that strike a single opponent, the greater the DMG dealt to them. When all 11 Rosula Shardshots strike, 200% of the original amount of DMG is dealt.In addition, when more than 3 stacks of Crystal Shrapnel are consumed, every stack consumed beyond those 3 will increase the DMG dealt by this Gunbrella attack by an additional 15%.
                                Hold Enter Aiming Mode, continually collecting nearby Elemental Shards created by Crystallize reactions. When released, fire Rosula Shardshots with the same effect as when the skill is Tapped.Two initial charges.Arkhe: Ousia Periodically, when Navia fires her Gunbrella, a Surging Blade will be summoned, dealing Ousia-aligned Geo DMG
                            EOT
                        ),

                        "ElementalBurst" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Navia/talent_3.png",
                            "title" => "Elemental Burst",
                            "name" => "As the Sunlit Sky's Singing Salute",
                            "description" => "On the orders of the President of the Spina di Rosula, call for a magnificent Rosula Dorata Salute. Unleashes a massive cannon bombardment on opponents in front of her, dealing AoE Geo DMG and providing Cannon Fire Support for a duration afterward, periodically dealing Geo DMG to nearby opponents.When cannon attacks hit opponents, Navia will gain 1 stack of Crystal Shrapnel. This effect can be triggered up to once every 2.4s."
                        ),
                    );

                    // 各情報を出力
                    ?>

                    <?php
                    // Navia Passivesの情報を格納する配列
                    $passivesInfo = array(
                        "Ascension1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_4.png",
                            "title" => "Ascension 1",
                            "name" => "Undisclosed Distribution Channels",
                            "description" => "For 4s after using Ceremonial Crystalshot, the DMG dealt by Navia's Normal Attacks, Charged Attacks, and Plunging Attacks will be converted into Geo DMG which cannot be overridden by other Elemental infusions, and the DMG dealt by Navia's Normal Attacks, Charged Attacks, and Plunging Attacks will be increased by 40%.."
                        ),

                        "Ascension4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_5.png",
                            "title" => "Ascension 4",
                            "name" => "Mutual Assistance Network",
                            "description" => "For each Pyro/Electro/Cryo/Hydro party member, Navia gains 20% increased ATK. This effect can stack up to 2 times."
                        ),
                        "Passive" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_6.png",
                            "title" => "Passive",
                            "name" => "Painstaking Transaction",
                            "description" => "Gains 25% more rewards when dispatched on a Fontaine Expedition for 20 hours."
                        )
                    );

                    // Navia Constellationsの情報を格納する配列
                        $constellationsInfo = array(
                            "Constellation1" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Navia/constellation_1.png",

                                "title" => "Constellation 1",
                                "name" => "All Reprisals and Arrears, Mine to Bear...",
                                "description" => "Each stack of Crystal Shrapnel consumed when Navia uses Ceremonial Crystalshot will restore 3 Energy to her and decrease the CD of As the Sunlit Sky's Singing Salute by 1s. Up to 9 Energy can be gained this way, and the CD of 'As the Sunlit Sky's Singing Salute' can be decreased by up to 3s."
                            ),

                        "Constellation2" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Navia/constellation_2.png",
                            "title" => "Constellation 2",
                            "name" => "The President's Pursuit of Victory",
                            "description" => "Each stack of Crystal Shrapnel consumed will increase the CRIT Rate of this Ceremonial Crystalshot instance by 12%. CRIT Rate can be increased by up to 36% in this way.
                                In addition, when Ceremonial Crystalshot hits an opponent, one Cannon Fire Support shot from As the Sunlit Sky's Singing Salute will strike near the location of the hit. Up to one instance of Cannon Fire Support can be triggered each time Ceremonial Crystalshot is used, and DMG dealt by said Cannon Fire Support this way is considered Elemental Burst DMG."
                             ),

                        "Constellation3" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Navia/constellation_3.png",
                            "title" => "Constellation 3",
                            "name" => "Businesswoman's Broad Vision",
                            "description" => "Increases the Level of Ceremonial Crystalshot by 3.
                            Maximum upgrade level is 15."
                        ),
                        "Constellation4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Navia/constellation_4.png",
                            "title" => "Constellation 4",
                            "name" => "The Oathsworn Never Capitulate",
                            "description" => "When As the Sunlit Sky's Singing Salute hits an opponent, that opponent's Geo RES will be decreased by 20% for 8s."
                        ),
                        "Constellation5" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Navia/constellation_5.png",
                            "title" => "Constellation 5",
                            "name" => "Negotiator's Resolute Negotiations",
                            "description" => "Increases the Level of As the Sunlit Sky's Singing Salute by 3.Maximum upgrade level is 15."
                        ),
                        "Constellation6" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Navia/constellation_6.png",
                            "title" => "Constellation 6",
                            "name" => "The Flexible Finesse of the Spina's President.",
                            "description" => "If more than 3 stacks of Crystal Shrapnel are consumed when using Ceremonial Crystalshot, each stack consumed beyond the first 3 increases the CRIT DMG of that Ceremonial Crystalshot by 45%, and any stacks consumed beyond the first 3 are returned to Navia."
                        ),
                    );
                    ?>


                    <?php
                    // Navia Passivesの情報を出力
                    ?>
                    <div class="character-skills" id="passives">
                        <h2 class="character-category">Navia Passives</h2>
                        <?php foreach ($passivesInfo as $passive): ?>
                            <div class="character-skill">
                                <div class="character-skill-header"><img class="character-skill-icon"
                                        src="<?php echo $passive['icon']; ?>" alt="<?php echo $passive['name']; ?>">
                                    <h2 class="character-skill-title">
                                        <?php echo $passive['title']; ?>
                                    </h2>
                                </div>
                                <div class="character-skill-body">
                                    <h2 class="character-skill-name">
                                        <?php echo $passive['name']; ?>
                                    </h2>
                                    <div class="character-skill-description">
                                        <?php echo $passive['description']; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php
                    // Navia Constellationsの情報を出力
                    ?>
                    <div class="character-skills" id="constellations">
                        <h2 class="character-category">Navia Constellations</h2>
                        <?php foreach ($constellationsInfo as $constellation): ?>
                            <div class="character-skill">
                                <div class="character-skill-header"><img class="character-skill-icon"
                                        src="<?php echo $constellation['icon']; ?>"
                                        alt="<?php echo $constellation['name']; ?>">
                                    <h2 class="character-skill-title">
                                        <?php echo $constellation['title']; ?>
                                    </h2>
                                </div>
                                <div class="character-skill-body">
                                    <h2 class="character-skill-name">
                                        <?php echo $constellation['name']; ?>
                                    </h2>
                                    <div class="character-skill-description">
                                        <?php echo $constellation['description']; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="character-skills" id="talents">
                        <h2 class="character-category">Navia Talents</h2>
                        <?php foreach ($NaviaInfo as $skill): ?>
                            <div class="character-skill">
                                <div class="character-skill-header"><img class="character-skill-icon"
                                        src="<?php echo $skill['icon']; ?>" alt="<?php echo $skill['name']; ?>">
                                    <h2 class="character-skill-title">
                                        <?php echo $skill['title']; ?>
                                    </h2>
                                </div>
                                <div class="character-skill-body">
                                    <h2 class="character-skill-name">
                                        <?php echo $skill['name']; ?>
                                    </h2>
                                    <div class="character-skill-description">
                                        <?php echo $skill['description']; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!--Character Ascension Section-->
                    <div class="character-ascension" style="display: contents;">
                        <h2 class="character-category">Navia Ascension Costs</h2>
                        <!--Table Data-->
                        <div class="ReactTable table" id="ascension">
                            <div class="rt-table" role="grid">
                                <div class="rt-thead -header" style="min-width: 1200px;">
                                    <div class="rt-tr" role="row">
                                        <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                            style="text-align: center; flex: 60 0 auto; width: 60px;">
                                            <div class="">Rank</div>
                                        </div>
                                        <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                            style="text-align: center; flex: 60 0 auto; width: 60px;">
                                            <div class="">Lvl</div>
                                        </div>
                                        <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                            style="text-align: center; flex: 80 0 auto; width: 80px;">
                                            <div class="">Cost</div>
                                        </div>
                                        <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                            style="flex: 150 0 auto; width: 150px;">
                                            <div class="">Material</div>
                                        </div>
                                        <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                            style="flex: 150 0 auto; width: 150px;">
                                            <div class="">Material</div>
                                        </div>
                                        <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                            style="flex: 150 0 auto; width: 150px;">
                                            <div class="">Material</div>
                                        </div>
                                        <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                            style="flex: 150 0 auto; width: 150px;">
                                            <div class="">Material</div>
                                        </div>
                                    </div>
                                </div>
                                <!--Table Data-->
                                <div class="rt-tbody" style="min-width: 1200px;">
                                    <?php
                                    // 千織の昇華アイテムの情報
                                    $ascensionItems = array(
                                        array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Prithiva Topaz Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Spring of the First Dewdrop", "Count3" => "3", "Material4" => "Transoceanic Pearl", "Count4" => "3"),

                                        array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Prithiva Topaz Fragment", "Count1" => "3", "Material2" => "Artificed Spare Clockwork Component - Coppelius", "Count2" => "2", "Material3" => "Spring of the First Dewdrop", "Count3" => "10", "Material4" => "Transoceanic Pearl", "Count4" => "15"),

                                        array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Prithiva Topaz Fragment", "Count1" => "6", "Material2" => "Artificed Spare Clockwork Component - Coppelius", "Count2" => "4", "Material3" => "Spring of the First Dewdrop", "Count3" => "20", "Material4" => "Transoceanic Chunk", "Count4" => "12"),

                                        array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Prithiva Topaz Chunk", "Count1" => "3", "Material2" => "Artificed Spare Clockwork Component - Coppelius", "Count2" => "8", "Material3" => "Spring of the First Dewdrop", "Count3" => "30", "Material4" => "Transoceanic Chunk", "Count4" => "18"),

                                        array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Prithiva Topaz Chunk", "Count1" => "6", "Material2" => "Artificed Spare Clockwork Component - Coppelius", "Count2" => "12", "Material3" => "Spring of the First Dewdrop", "Count3" => "45", "Material4" => "Xenochromatic Crystal", "Count4" => "12"),

                                        array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Prithiva Topaz Gemstone", "Count1" => "6", "Material2" => "Artificed Spare Clockwork Component - Coppelius", "Count2" => "20", "Material3" => "Spring of the First Dewdrop", "Count3" => "60", "Material4" => "Xenochromatic Crystal", "Count4" => "24")
                                        // 追加の昇華ランク情報も同様に追加
                                    );

                                    foreach ($ascensionItems as $ascensionItem):
                                        ?>
                                        <!-- アイテムごとの表示 -->
                                        <div class="rt-tr-group" role="rowgroup">
                                            <div class="rt-tr -odd" role="row">
                                                <?php
                                                // Rank、Lvl、Costの情報を表示
                                                foreach (["Rank", "Lvl", "Cost"] as $infoKey):
                                                    ?>
                                                    <div class="rt-td" role="gridcell"
                                                        style="justify-content: center; text-align: center; flex: <?= ($infoKey === "Cost") ? "80" : "60"; ?> 0 auto; width: <?= ($infoKey === "Cost") ? "80" : "60"; ?>px;">
                                                        <?php echo $ascensionItem[$infoKey]; ?>
                                                    </div>
                                                <?php endforeach; ?>

                                                <?php
                                                // Material1からMaterial4までの情報をまとめて表示
                                                for ($i = 1; $i <= 4; $i++):
                                                    $materialKey = "Material{$i}";
                                                    $countKey = "Count{$i}";
                                                    $material = $ascensionItem[$materialKey];
                                                    $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/farming/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
                                                    ?>
                                                    <div class="rt-td" role="gridcell" style="flex: 150 0 auto; width: 150px;">
                                                        <?php if ($material != ""): ?>
                                                            <div class="table-image-wrapper">
                                                                <img class="table-image" src="<?= $materialUrl; ?>"
                                                                    alt="<?= $material; ?>">
                                                                <span
                                                                    class="table-image-count"><?= $ascensionItem[$countKey]; ?></span>
                                                            </div>
                                                            <?= $material; // アイテム名を表示 ?>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <!--Table Data end-->

                            </div>
                        </div>
                    </div>
                </div>




            </div>
            <!--character team-->
            <h2 class="character-category">Navia Showcase</h2>
            <div class="character-showcase" id="showcase">
                <lite-youtube videoid="jSfHaiC6SIk" params="rel=0"></lite-youtube>
            </div>


            <!--character end-->
            <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>