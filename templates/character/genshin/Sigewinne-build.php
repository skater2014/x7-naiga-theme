<?php
/**
 * Template Name: Sigewinne.php
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Sigewinne/Sigewinne.png';
                $image_alt = 'Sigewinne';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Sigewinne Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_hydro.png"
                            alt="electro">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_bow.png"
                            alt="Bow">Bow</div>
                    <div class="character-role">Support</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

                    $materials = array(
                        'Equity' => 'Equity',
                        "Lightless Eye of the Maelstrom" => "Lightless Eye of the Maelstrom",
                        'Varunada Lazurite Sliver' => 'Varunada Lazurite Sliver',
                        'Romaritime Flower' => 'Romaritime Flower',
                        "Transoceanic Pearl" => "Transoceanic Pearl",
                        "Water That Failed To Transcend" => "Water That Failed To Transcend",
                    
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
                        <!-- Sigewinne Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Sigewinne Best Weapons</h2>
                            <div class="character-build-weapons">
                                <?php
                                $weapons = array(
                                    array("Silvershower Heartstrings", 1, 5),
                                    array('Elegy for the End', 2, 5),
                                    array('Recurve Bow', 3, 4),
                                    array('Favonius Warbow', 4, 3),
                                );//画像形式と同じアルファベットで配列

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

                        <!-- Sigewinne Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Sigewinne Best Artifacts</h2>

                            <?php
                            $artifacts = array(
                                array("Tenacity Of The Millelith", 1),
                                array("Vourukasha's Glow", 2),
                            );


                            $manualRanks = array(2, 2);

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




                    <!-- Sigewinne Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Sigewinne Best Stats</h2>
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
                    <h2 class="character-category">Best Sigewinne Teams</h2>
                    <!-- Sigewinne Freeze Team -->
                    <div class="character-team">
                        <div class="character-team-name">Sigewinne Vaporize Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Sigewinne_Teams1["Sigewinne"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Sigewinne_Teams1["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Sigewinne_Teams1["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                $Sigewinne_Teams1["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Sigewinne_Teams1 as $name => $info):
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

                    <!-- Sigewinne/Ganyu Mono Cryo Team -->
                    <div class="character-team">
                        <div class="character-team-name">Sigewinne/Raiden National Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Sigewinne_Teams2["Sigewinne"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Sigewinne_Teams2["Raiden"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Sigewinne_Teams2["Jean"] = array("element" => "anemo", "rarity" => "rarity-5");
                                $Sigewinne_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-5");

                                // キャラクター情報を出力
                                foreach ($Sigewinne_Teams2 as $name => $info):
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
                    <!-- Sigewinne/Ganyu Furina Hydro Team -->
                    <div class="character-team">
                        <div class="character-team-name">Sigewinne/blooming intensely Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Sigewinne_Teams3["Sigewinne"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Sigewinne_Teams3["Kuki Shinobu"] = array("element" => "electro", "rarity" => "rarity-4");
                                $Sigewinne_Teams3["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Sigewinne_Teams3["Collei"] = array("element" => "dendro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Sigewinne_Teams3 as $name => $info):
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
// Sigewinneの情報を格納する配列
$SigewinneInfo = array(
    "NormalAttack" => array(
        "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png",
        "title" => "Normal Attack",
        "name" => "Royal Reed Archery",
        "description" => "Normal Attack Performs up to 3 consecutive shots with a bow.

        Charged Attack
        Performs a more precise Aimed Shot with increased DMG.
        While aiming, the power of Electro will accumulate on the arrowhead before the arrow is fired. Has different effects based on how long the energy has been charged:
        Charge Level 1: Fires off an arrow carrying the power of lightning that deals Electro DMG.
        Charge Level 2: Fires off a Shadowpiercing Shot which can pierce enemies, dealing Electro DMG to enemies along its path. After the Shadowpiercing Shot is fully charged, Sigewinne cannot move around.

        Plunging Attack
        Fires off a shower of arrows in mid-air before falling and striking the ground, dealing AoE DMG upon impact."
    ),

    "ElementalSkill" => array(
        "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/talent_2.png",
        "title" => "Elemental Skill",
        "name" => "Ancient Rite: The Thundering Sands",
        "description" => <<<EOT
        Gathers the might of thunder, dealing AoE Electro DMG and quickly retreating. If this attack triggers Electro-Charged, Superconduct, Overloaded, Quicken, Aggravate, or Electro Swirl reactions, Sigewinne recovers a certain amount of Elemental Energy.
        EOT
    ),

    "ElementalBurst" => array(
        "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/talent_3.png",
        "title" => "Elemental Burst",
        "name" => "Secret Rite: Twilight Shadowpiercer",
        "description" => "Perform a secret rite, entering the \"Twilight Meditation\" state, during which Sigewinne's Normal Attacks will be converted into enemy-piercing Dusk Bolts: Deal Electro DMG to opponents in its path, with DMG increased based on Sigewinne's Elemental Mastery. Sigewinne cannot perform Aimed Shots while in this state. DMG dealt by Dusk Bolts is considered Charged Attack DMG. This effect will be canceled when Sigewinne leaves the field."
    )
);

// 各情報を出力
?>


                    <?php
                    // Sigewinne Passivesの情報を格納する配列
                    $passivesInfo = array(
                        "Ascension1" => array(
                        "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Sigewinne/talent_4.png",
                        "title" => "Ascension 1",
                        "name" => "Requires Appropriate Rest",
                        "description" => 'Sigewinne grants herself the "Semi-Strict Bedrest" effect for 18s after using Rebound Hydrotherapy: Sigewinne gains an 8% Hydro DMG Bonus and 10 stacks of Convalescence. When the Elemental Skills of your nearby off-field party members other than Sigewinne deal DMG, consume 1 stack of Convalescence, and increase the DMG dealt by this instance of Elemental Skill DMG. Every 1,000 Max HP Sigewinne has above 30,000 increases the DMG by 80. The maximum DMG increase for Elemental Skills that can be gained in this way is 2,800.'
                        ),

                        "Ascension4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Sigewinne/talent_5.png",
                            "title" => "Ascension 4",
                            "name" => "Detailed Diagnosis, Thorough Treatment",
                            "description" => "When Sigewinne performs healing, the amount healed for this instance is increased based on the total current value of the Bonds of Life on all party members: For each 1,000 HP worth of Bonds of Life, the outgoing healing is increased by 3%. The amount of healing provided can be increased by up to 30% in this way."
                        ),

                        "Passive" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Sigewinne/talent_6.png",
                            "title" => "Passive",
                            "name" => "Emergency Dose",
                            "description" => "While underwater, heal your active character over 2.5s when their HP falls under 50%. The amount healed is equal to 50% of their Max HP, and their All Elemental and Physical RES will be decreased by 10% for 10s. This effect can be triggered up to once every 20s."
                        )
                    );

                    // Sigewinne Constellationsの情報を格納する配列
                    $constellationsInfo = array(
                        "Constellation1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Sigewinne/constellation_1.png",
                            "title" => "Constellation 1",
                            "name" => "Can the Happiest of Spirits Understand Anxiety?.",
                            "description" => 'Rebound Hydrotherapy\'s Bolstering Bubblebalm can bounce 3 extra times, and the first 3 bounces will not cause big Bubblebalms to become smaller.
                        The Passive Talent "Requires Appropriate Rest" is enhanced: Each Bubblebalm bounce adds 1 stack to her Convalescence tally, and the values for the Convalescence stacks\' DMG bonus will be modified to: Every 1,000 max HP Sigewinne has above 30,000 increases the DMG by 100. The maximum DMG increase for Elemental Skills that can be gained in this way is 3,500. You must first unlock the Passive Talent "Requires Appropriate Rest.".'
                        ),

                        "Constellation2" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Sigewinne/constellation_2.png",
                            "title" => "Constellation 2",
                            "name" => "Can the Most Merciful of Spirits Defeat Its Foes?",
                            "description" => 'When using Rebound Hydrotherapy and Super Saturated Syringing, Sigewinne can create a Bubbly Shield worth 30% of her Max HP that absorbs Hydro DMG with 250% efficiency. The Bubbly Shield will persist until Sigewinne finishes using relevant skills.
                        Additionally, after Rebound Hydrotherapy\'s Bolstering Bubblebalm or Super Saturated Syringing hits an opponent, that opponent\'s Hydro RES will be decreased by 35% for 8s.'
                        ),

                        "Constellation3" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Sigewinne/constellation_3.png",
                            "title" => "Constellation 3",
                            "name" => "Can the Healthiest of Spirits Cure Fevers?",
                            "description" => "Increases the Level of Normal Attack: Royal Reed Archery by 3.Maximum upgrade level is 15."
                        ),
                        "Constellation4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Sigewinne/constellation_4.png",
                            "title" => "Constellation 4",
                            "name" => "Can the Loveliest of Spirits Keep Decay at Bay?",
                            "description" => "The duration of Super Saturated Syringing is extended by 3s."
                        ),
                        "Constellation5" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Sigewinne/constellation_5.png",
                            "title" => "Constellation 5",
                            "name" => "Can the Most Joyful of Spirits Alleviate Agony?",
                            "description" => "Increases the Level of Secret Rite: Twilight Shadowpiercer by 3.Maximum upgrade level is 15."
                        ),
                        "Constellation6" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Sigewinne/constellation_6.png",
                            "title" => "Constellation 6",
                            "name" => "Can the Most Radiant of Spirits Pray For Me?.",
                            "description" => "When Sigewinne performs healing, she will increase the CRIT Rate and CRIT DMG of her Super Saturated Syringing based on her Max HP. Every 1,000 Max HP she has will increase CRIT Rate by 0.4% and CRIT DMG by 2.2% for 15s. The maximum increase achievable this way is 20% CRIT Rate and 110% CRIT DMG..\""
                        ),
                    );
                    ?>


                    <?php
                    // Sigewinne Passivesの情報を出力
                    ?>
                    <div class="character-skills" id="passives">
                        <h2 class="character-category">Sigewinne Passives</h2>
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
                    // Sigewinne Constellationsの情報を出力
                    ?>
                    <div class="character-skills" id="constellations">
                        <h2 class="character-category">Sigewinne Constellations</h2>
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
                        <h2 class="character-category">Sigewinne Talents</h2>
                        <?php foreach ($SigewinneInfo as $skill): ?>
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
                        <h2 class="character-category">Sigewinne Ascension Costs</h2>
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
                                        array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Varunada Lazurite Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Romaritime Flower", "Count3" => "3", "Material4" => "Transoceanic Pearl", "Count4" => "3"),
                                        array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Water That Failed To Transcend", "Count1" => "3", "Material2" => "Varunada Lazurite Fragment", "Count2" => "2", "Material3" => "Romaritime Flower", "Count3" => "10", "Material4" => "Transoceanic Pearl", "Count4" => "15"),
                                        array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Water That Failed To Transcend", "Count1" => "6", "Material2" => "Varunada Lazurite Fragment", "Count2" => "4", "Material3" => "Romaritime Flower", "Count3" => "20", "Material4" => "Transoceanic Chunk", "Count4" => "12"),
                                        array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Water That Failed To Transcend", "Count1" => "3", "Material2" => "Varunada Lazurite Chunk", "Count2" => "8", "Material3" => "Romaritime Flower", "Count3" => "30", "Material4" => "Transoceanic Chunk", "Count4" => "18"),
                                        array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Water That Failed To Transcend", "Count1" => "6", "Material2" => "Varunada Lazurite Chunk", "Count2" => "12", "Material3" => "Romaritime Flower", "Count3" => "45", "Material4" => "Xenochromatic Crystal", "Count4" => "12"),
                                        array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Water That Failed To Transcend", "Count1" => "6", "Material2" => "Vajrada Amethyst Gemstone", "Count2" => "20", "Material3" => "Romaritime Flower", "Count3" => "60", "Material4" => "Xenochromatic Crystal", "Count4" => "24")
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
            <h2 class="character-category">Sigewinne Showcase</h2>
            <div class="character-showcase" id="showcase">
                <lite-youtube videoid="D92bOeks5P8" params="rel=0"></lite-youtube>
            </div>


            <!--character end-->
            <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>