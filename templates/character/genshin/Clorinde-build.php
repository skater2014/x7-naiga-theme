<?php
/**
 * Template Name: Clorinde.php
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Clorinde/Clorinde.png';
                $image_alt = 'Clorinde';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Clorinde Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_electro.png"
                            alt="electro">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_sword.png"
                            alt="Sword">Sword</div>
                    <div class="character-role">Main DPS</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

                    $materials = array(
                        'Justice' => 'Justice',
                        'Everamber' => 'Everamber',
                        'Lumitoile' => 'Lumitoile',
                        'Transoceanic Pearl' => 'Transoceanic Pearl',
                        "Fontemer Unihorn" => "Fontemer Unihorn",
                        
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
                        <!-- Clorinde Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Clorinde Best Weapons</h2>
                            <div class="character-build-weapons">
                                <?php
                                $weapons = array(
                                    array("Absolution", 1, 5),
                                    array('Haran Geppaku Futsu', 2, 5),
                                    array('Mistsplitter Reforged', 3, 5),
                                    array('Finale of the Deep', 4, 4),
                                    array('The Black Sword', 5, 4)
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

                        <!-- Clorinde Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Clorinde Best Artifacts</h2>

                            <?php
                            $artifacts = array(
                                array('Fragment of Harmonic Whimsy', 1),
                                array("Golden Troupe", 2),
                                array("Thundering Fury", 3),
                                array("Echoes of An Offering", 2),
                                array("Gladiator's Finale", 2)
                            );


                            $manualRanks = array(4, 4, 4, 2, 2);

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




                    <!-- Clorinde Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Clorinde Best Stats</h2>
                        <div class="character-stats-item"><b>Sands:</b> ATK%</div>
                        <div class="character-stats-item"><b>Goblet:</b> Pyro DMG%</div>
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
                    <h2 class="character-category">Best Clorinde Teams</h2>
                    <!-- Clorinde Freeze Team -->
                    <div class="character-team">
                        <div class="character-team-name">Clorinde Vaporize Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Clorinde_Teams1["Clorinde"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Clorinde_Teams1["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Clorinde_Teams1["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                $Clorinde_Teams1["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Clorinde_Teams1 as $name => $info):
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

                    <!-- Clorinde/Ganyu Mono Cryo Team -->
                    <div class="character-team">
                        <div class="character-team-name">Clorinde/Raiden National Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Clorinde_Teams2["Clorinde"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Clorinde_Teams2["Raiden"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Clorinde_Teams2["Jean"] = array("element" => "anemo", "rarity" => "rarity-5");
                                $Clorinde_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-5");

                                // キャラクター情報を出力
                                foreach ($Clorinde_Teams2 as $name => $info):
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
                    <!-- Clorinde/Ganyu Furina Hydro Team -->
                    <div class="character-team">
                        <div class="character-team-name">Clorinde/blooming intensely Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Clorinde_Teams3["Clorinde"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Clorinde_Teams3["Kuki Shinobu"] = array("element" => "electro", "rarity" => "rarity-4");
                                $Clorinde_Teams3["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Clorinde_Teams3["Collei"] = array("element" => "dendro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Clorinde_Teams3 as $name => $info):
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
                    // Clorindeの情報を格納する配列
                    $ClorindeInfo = array(
                        "NormalAttack" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png",
                            "title" => "Normal Attack",
                            "name" => "Oath of Hunting Shadows",
                            "description" => "Normal Attack Performs up to 5 rapid strikes. Charged Attack Consumes a certain amount of Stamina and fires Suppressing Shots in a fan pattern with her pistolet. Plunging Attack Plunges from mid-air to strike the ground below, damaging opponents along the path and dealing AoE DMG upon impact."
                        ),
                        "ElementalSkill" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/talent_2.png",
                            "title" => "Elemental Skill",
                            "name" => "Hunter's Vigil",
                            "description" => <<<EOT
                            Preparing her pistolet, she enters the "Night Vigil" state, using steel and shot together. In this state, Clorinde's Normal Attacks will be transformed into "Swift Hunt" pistolet attacks, and the DMG dealt is converted into Electro DMG that cannot be overridden by infusions, and she will be unable to use Charged Attacks. Using her Elemental Skill will transform it into "Impale the Night": Perform a lunging attack, dealing Electro DMG. The DMG done through the aforementioned method is considered Normal Attack DMG. Swift Hunt When her Bond of Life is equal to or greater than 100% of her max HP: Performs a pistolet shot. When her Bond of Life is less than 100%, firing her pistolet will grant her Bond of Life, with the amount gained based on her max HP. The shots she fires can pierce opponents, and DMG dealt to opponents in their path is increased. Impale the Night The current percentage value of Clorinde's Bond of Life determines its effect: When the Bond of Life value is 0%, perform a normal lunging strike; When the Bond of Life value is less than 100% of her max HP, Clorinde is healed based on the Bond of Life value, and the AoE of the lunging attack and the DMG dealt is increased; When the value of the Bond of Life is equal to or greater than 100% of her max HP, use Impale the Night: Pact. The healing multiplier is increased, and the AoE and DMG dealt by the lunge is increased even further. In addition, when Clorinde is in the Night Vigil state, healing effects other than Impale the Night will not take effect and will instead be converted into a Bond of Life that is a percentage of the healing that would have been received. Clorinde will exit the "Night Vigil" state when she leaves the field. Arkhe: Ousia Periodically, when Clorinde's Swift Hunt shots strike opponents, she will summon a Surging Blade at the position hit that deals Ousia-aligned Electro DMG.
                            EOT
                        ),

                        "ElementalBurst" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/talent_3.png",
                            "title" => "Elemental Burst",
                            "name" => "Last Lightfall",
                            "description" => "Grants herself a Bond of Life based upon her own max HP before swiftly evading and striking with saber and sidearm as one, dealing AoE Electro DMG."
                        ),
                    );

                    // 各情報を出力
                    ?>

                    <?php
                    // Clorinde Passivesの情報を格納する配列
                    $passivesInfo = array(
                        "Ascension1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/talent_4.png",
                            "title" => "Ascension 1",
                            "name" => "Dark-Shattering Flame",
                            "description" => "After a nearby party member triggers an Electro-related reaction against an opponent, Electro DMG dealt by Clorinde's Normal Attacks and Last Lightfall will be increased by 20% of Clorinde's ATK for 15s. Max 3 stacks. Each stack is counted independently. The Maximum DMG increase achievable this way for the above attacks is 1,800."
                        ),

                        "Ascension4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/talent_5.png",
                            "title" => "Ascension 4",
                            "name" => "Lawful Remuneration",
                            "description" => "If Clorinde's Bond of Life is equal to or greater than 100% of her Max HP, her CRIT Rate will increase by 10% for 15s whenever her Bond of Life value increases or decreases. Max 2 stacks. Each stack is counted independently. Additionally, Hunter's Vigil's Night Vigil state is buffed: While it is active, the percent of healing converted to Bond of Life increases to 100%."
                        ),
                        "Passive" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/talent_6.png",
                            "title" => "Passive",
                            "name" => "Night Vigil's Harvest",
                            "description" => "Displays the location of nearby resources unique to Fontaine on the mini-map."
                        )
                    );

                    // Clorinde Constellationsの情報を格納する配列
                    $constellationsInfo = array(
                        "Constellation1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/constellation_1.png",
                            "title" => "Constellation 1",
                            "name" => "From This Day, I Pass the Candle's Shadow-Veil",
                            "description" => "While Hunter's Vigil's Night Vigil state is active, when Electro DMG from Clorinde's Normal Attacks hit opponents, they will trigger 2 coordinated attacks from a Nightvigil Shade summoned near the hit opponent, each dealing 30% of Clorinde's ATK as Electro DMG. This effect can occur once every 1.2s. DMG dealt this way is considered Normal Attack DMG."
                        ),

                        "Constellation2" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/constellation_2.png",
                            "title" => "Constellation 2",
                            "name" => "In Five Colors Dyed",
                            'description' => 'Enhance the Passive Talent "Dark-Shattering Flame": After a nearby party member triggers an Electro-related reaction against an opponent, Electro DMG dealt by Clorinde\'s Normal Attacks and Last Lightfall will be increased by 30% of Clorinde\'s ATK for 15s. Max 3 stacks. Each stack is counted independently. When you have 3 stacks, Clorinde\'s interruption resistance will be increased. The Maximum DMG increase achievable this way for the above attacks is 2,700. You must first unlock the Passive Talent "Dark-Shattering Flame."'
                        ),
                        "Constellation3" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/constellation_3.png",
                            "title" => "Constellation 3",
                            "name" => "I Pledge to Remember the Oath of Daylight",
                            "description" => "Increases the Level of Hunter's Vigil by 3. Maximum upgrade level is 15."
                        ),
                        "Constellation4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/constellation_4.png",
                            "title" => "Constellation 4",
                            "name" => "To Enshrine Tears, Life, and Love",
                            "description" => "When Last Lightfall deals DMG to opponent(s), DMG dealt is increased based on Clorinde's Bond of Life percentage. Every 1% of her current Bond of Life will increase Last Lightfall DMG by 2%. The maximum Last Lightfall DMG increase achievable this way is 200%."
                        ),
                        "Constellation5" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/constellation_5.png",
                            "title" => "Constellation 5",
                            "name" => "Holding Dawn's Coming as My Votive",
                            "description" => "Increases the Level of Last Lightfall by 3. Maximum upgrade level is 15."
                        ),
                        "Constellation6" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Clorinde/constellation_6.png",
                            "title" => "Constellation 6",
                            "name" => "And So Shall I Never Despair.",
                            "description" => "For 12s after Hunter's Vigil is used, Clorinde's CRIT Rate will be increased by 10%, and her CRIT DMG by 70%. Additionally, while Night Vigil is active, a Glimbright Shade will appear under specific circumstances, executing an attack that deals 200% of Clorinde's ATK as Electro DMG. DMG dealt this way is considered Normal Attack DMG. The Glimbright Shade will appear under the following circumstances: When Clorinde is about to be hit by an attack. When Clorinde uses Impale the Night: Pact. 1 Glimbright Shade can be summoned in the aforementioned ways every 1s. 6 Shades can be summoned per single Night Vigil duration. In addition, while Night Vigil is active, the DMG Clorinde receives is decreased by 80% and her interruption resistance is increased. This effect will disappear after the Night Vigil state ends or 1s after she summons 6 Glimbright Shades."
                        ),
                    );
                    ?>


                    <?php
                    // Clorinde Passivesの情報を出力
                    ?>
                    <div class="character-skills" id="passives">
                        <h2 class="character-category">Clorinde Passives</h2>
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
                    // Clorinde Constellationsの情報を出力
                    ?>
                    <div class="character-skills" id="constellations">
                        <h2 class="character-category">Clorinde Constellations</h2>
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
                        <h2 class="character-category">Clorinde Talents</h2>
                        <?php foreach ($ClorindeInfo as $skill): ?>
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
                        <h2 class="character-category">Clorinde Ascension Costs</h2>
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
                                        array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Vajrada Amethyst Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Lumitoile", "Count3" => "3", "Material4" => "Transoceanic Chunk", "Count4" => "3"),
                                        array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Fontemer Unihorn", "Count1" => "3", "Material2" => "Vajrada Amethyst Fragment", "Count2" => "2", "Material3" => "Lumitoile", "Count3" => "10", "Material4" => "Transoceanic Chunk", "Count4" => "15"),
                                        array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Fontemer Unihorn", "Count1" => "6", "Material2" => "Vajrada Amethyst Fragment", "Count2" => "4", "Material3" => "Lumitoile", "Count3" => "20", "Material4" => "Transoceanic Chunk", "Count4" => "12"),
                                        array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Fontemer Unihorn", "Count1" => "3", "Material2" => "Vajrada Amethyst Chunk", "Count2" => "8", "Material3" => "Lumitoile", "Count3" => "30", "Material4" => "Transoceanic Chunk", "Count4" => "18"),
                                        array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Fontemer Unihorn", "Count1" => "6", "Material2" => "Vajrada Amethyst Chunk", "Count2" => "12", "Material3" => "Lumitoile", "Count3" => "45", "Material4" => "Xenochromatic Crystal", "Count4" => "12"),
                                        array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Fontemer Unihorn", "Count1" => "6", "Material2" => "Vajrada Amethyst Gemstone", "Count2" => "20", "Material3" => "Lumitoile", "Count3" => "60", "Material4" => "Xenochromatic Crystal", "Count4" => "24")
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
                                                    $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Farming/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
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
            <h2 class="character-category">Clorinde Showcase</h2>
            <div class="character-showcase" id="showcase">
                <lite-youtube videoid="g1gW1AFMx18" params="rel=0"></lite-youtube>
            </div>


            <!--character end-->
            <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>