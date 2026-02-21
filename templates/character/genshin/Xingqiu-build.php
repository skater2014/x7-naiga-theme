<?php
/**
 * Template Name: Xingqiu-build.php
 * Description: Template for displaying Genshin Impact character builds Xingqiu.
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu.png';
                $image_alt = 'Xingqiu';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Xingqiu Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_hydro.png"
                            alt="Hydro">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_sword.png"
                            alt="Sword">Sword</div>
                    <div class="character-role">Sub DPS</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

                    $materials = array(
                        "Gold" => "Gold",
                        "Tail of Boreas" => "Tail of Boreas",
                        "Varunada Lazurite Sliver" => "Varunada Lazurite Sliver",
                        "Cleansing Heart" => "Cleansing Heart",
                        "Silk Flower" => "Silk Flower",
                        "Damaged Mask" => "Damaged Mask",

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


                    <div class="character-build">
                        <!-- Xingqiu Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Xingqiu Best Weapons</h2>
                            <?php
                            $weapons = array(
                                array("Sacrificial Sword", 1, 4),
                                array("Favonius Sword", 2, 4),
                                array("Primordial Jade Cutter", 3, 5),
                                array("Mistsplitter Reforged", 4, 5),
                                array("Haran Geppaku Futsu", 5, 5)
                            );

                            foreach ($weapons as $weapon):
                                $weaponRank = $weapon[1];
                                $weaponRarity = $weapon[2];
                                $weaponName = str_replace('_', ' ', $weapon[0]);
                                ?>
                                <div class="character-build-weapon">
                                    <div class="character-build-weapon-rank"><?php echo $weaponRank; ?></div>
                                    <img class="character-build-weapon-icon rarity-<?php echo $weaponRarity; ?>"
                                        src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xingqiu/<?php echo urlencode(str_replace(' ', '_', $weaponName)); ?>.png"
                                        alt="<?php echo $weaponName; ?>">
                                    <div class="character-build-weapon-name"><?php echo $weaponName; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>


                        <!-- Xingqiu Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Xingqiu Best Artifacts</h2>

                            <?php
                            // アーティファクト ランク
                            // Xingqiuの最適なアーティファクトの情報
                            $artifacts = array(
                                array("Emblem of Severed Fate", 1),
                                array("Noblesse Oblige", 2),
                                array("Emblem of Severed Fate", 3),
                                array("Noblesse Oblige", 3),
                                array("Emblem of Severed Fate", 4),
                                array("Heart of Depth", 4),
                                array("Heart of Depth", 5),
                                array("Noblesse Oblige", 5),
                            );

                            $groupedArtifacts = [];

                            foreach ($artifacts as $index => $artifact) {
                                $currentRank = $artifact[1];

                                // 最初の要素 or 前の要素とランクが異なる場合
                                if ($index === 0 || $currentRank !== $artifacts[$index - 1][1]) {
                                    $groupedArtifacts[] = array($artifact);
                                } else {
                                    // 同じランクの場合は前のグループに追加
                                    $groupedArtifacts[count($groupedArtifacts) - 1][] = $artifact;
                                }
                            }

                            foreach ($groupedArtifacts as $group) {
                                echo '<div class="character-build-weapon">';

                                foreach ($group as $artifact) {
                                    $artifactName = str_replace(' ', '_', $artifact[0]);
                                    $imageUrl = "https://rerollcdn.com/GENSHIN/Gear/" . strtolower(str_replace(' ', '_', $artifact[0])) . ".png";
                                    $artifactRank = $artifact[1];

                                    // グループ内に複数の要素がある場合はランクを表示
                                    echo '<div class="character-build-weapon-rank">' . $artifactRank . '</div>';

                                    echo '<div class="character-build-weapon-content' . (count($group) > 1 ? ' full' : '') . '">';
                                    echo '<img class="character-build-weapon-icon rarity-5" src="' . $imageUrl . '" alt="' . str_replace('_', ' ', $artifactName) . '">';
                                    echo '<div class="character-build-weapon-name">' . str_replace('_', ' ', $artifactName) . '</div>';
                                    echo '<div class="character-build-weapon-count">' . $artifactRank . '</div>';
                                    echo '</div>';
                                }

                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>




                    <!-- Xingqiu Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Xingqiu Best Stats</h2>
                        <div class="character-stats-item"><b>Sands:</b> Energy Recharge / ATK%</div>
                        <div class="character-stats-item"><b>Goblet:</b> Pyro DMG</div>
                        <div class="character-stats-item"><b>Circlet:</b> CRIT Rate / CRIT DMG</div>
                        <div class="character-stats-item full"><b>Substats:</b> Energy Recharge &gt; CRIT Rate / CRIT
                            DMG &gt; ATK%</div>
                    </div>

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
                        <h2 class="character-category">Best Xingqiu Teams</h2>
                        <!-- AyatoFreeze Team -->
                        <div class="character-team">
                            <div class="character-team-name">Nilou Bloom
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Xingqiu_Teams1["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                    $Xingqiu_Teams1["Kokomi"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Xingqiu_Teams1["Fischl"] = array("element" => "electro", "rarity" => "rarity-4");
                                    $Xingqiu_Teams1["Nahida"] = array("element" => "dendro", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Xingqiu_Teams1 as $name => $info):
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

                        <!-- Ayaka/Ganyu Mono Cryo Team -->
                        <div class="character-team">
                            <div class="character-team-name">Raiden National
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Xingqiu_Teams2["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                    $Xingqiu_Teams2["Raiden"] = array("element" => "electro", "rarity" => "rarity-5");
                                    $Xingqiu_Teams2["Jean"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Xingqiu_Teams2["Bennett"] = array("element" => "dendro", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Xingqiu_Teams2 as $name => $info):
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
                        <!-- Ayaka/Ganyu Furina Hydro Team -->
                        <div class="character-team">
                            <div class="character-team-name">Hu Tao Vaporize
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Xingqiu_Teams3["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                    $Xingqiu_Teams3["Hu Tao"] = array("element" => "pyro", "rarity" => "rarity-5");
                                    $Xingqiu_Teams3["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Xingqiu_Teams3["Zhongli"] = array("element" => "dendro", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Xingqiu_Teams3 as $name => $info):
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

                        <div class="character-skills" id="talents">
                            <h2 class="character-category">Xingqiu Talents</h2>
                            <?php
                            $XingqiuInfo = array(
                                array(
                                    "icon" => "https://rerollcdn.com/GENSHIN/Skill/UI_GachaTypeIcon_Sword.png",
                                    "title" => "Normal Attack",
                                    "name" => "Guhua Style",
                                    "description" => "Normal Attack Performs up to 5 rapid strikes. Charged Attack Consumes a certain amount of Stamina to unleash 2 rapid sword strikes. Plunging Attack Plunges from mid-air to strike the ground below, damaging enemies along the path and dealing AoE DMG upon impact."
                                ),
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/talent_2.png",
                                    "title" => "Elemental Skill",
                                    "name" => "Guhua Sword: Fatal Rainscreen",
                                    "description" => "Xingqiu performs twin strikes with his sword, dealing Hydro DMG. At the same time, this ability creates the maximum number of Rain Swords, which will orbit the character. The Rain Swords have the following properties: When a character takes DMG, the Rain Sword will shatter, reducing the amount of DMG taken. Increases the character's resistance to interruption. 20% of Xingqiu's Hydro DMG Bonus will be converted to additional DMG Reduction for the Rain Swords. The maximum amount of additional DMG Reduction that can be gained this way is 24%. The initial maximum number of Rain Swords is 3. Using this ability applies the Wet status onto the character."
                                ),
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/talent_3.png",
                                    "title" => "Elemental Burst",
                                    "name" => "Guhua Sword: Raincutter",
                                    "description" => "Initiate Rainbow Bladework and fight using an illusory sword rain, while creating the maximum number of Rain Swords. Rainbow Bladework Normal Attacks will trigger consecutive sword rain attacks, dealing Hydro DMG. Rain Swords will remain at the maximum number throughout the ability's duration. These effects carry over to other characters."
                                ),
                            );



                            // タレント情報をループで表示
                            foreach ($XingqiuInfo as $talent):
                                ?>
                                <div class="character-skill">
                                    <div class="character-skill-header">
                                        <img class="character-skill-icon" src="<?php echo esc_url($talent['icon']); ?>"
                                            alt="<?php echo esc_attr($talent['title']); ?>">
                                        <h3 class="character-skill-title"><?php echo esc_html($talent['title']); ?></h3>
                                    </div>
                                    <div class="character-skill-body">
                                        <h2 class="character-skill-name"><?php echo esc_html($talent['name']); ?></h2>
                                        <div class="character-skill-description">
                                            <?php echo esc_html($talent['description']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

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

                        <div class="character-skills" id="passives">
                            <h2 class="character-category">Xingqiu Passives</h2>
                            <?php
                            $passivesInfo = array(
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/talent_4.png",
                                    "title" => "Ascension 1",
                                    "name" => "Hydropathic",
                                    "description" => "When a Rain Sword is shattered or when its duration expires, it regenerates the current character's HP based on 6% of Xingqiu's Max HP."
                                ),
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/talent_5.png",
                                    "title" => "Ascension 4",
                                    "name" => "Blades Amidst Raindrops",
                                    "description" => "Xingqiu gains a 20% Hydro DMG Bonus."
                                ),
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/talent_6.png",
                                    "title" => "Passive",
                                    "name" => "Flash of Genius",
                                    "description" => "When Xingqiu crafts Character Talent Materials, he has a 25% chance to refund a portion of the crafting materials used."
                                ),
                            );


                            // パッシブ情報をループで表示
                            foreach ($passivesInfo as $passive):
                                ?>
                                <div class="character-skill">
                                    <div class="character-skill-header">
                                        <img class="character-skill-icon" src="<?php echo esc_url($passive['icon']); ?>"
                                            alt="<?php echo esc_attr($passive['title']); ?>">
                                        <h3 class="character-skill-title"><?php echo esc_html($passive['title']); ?></h3>
                                    </div>
                                    <div class="character-skill-body">
                                        <h2 class="character-skill-name"><?php echo esc_html($passive['name']); ?></h2>
                                        <div class="character-skill-description">
                                            <?php echo esc_html($passive['description']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

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

                        <div class="character-skills" id="constellations">
                            <h2 class="character-category">Xingqiu Constellations</h2>
                            <?php
                            $constellationsInfo = array(
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/constellation_1.png",
                                    "title" => "Constellation 1",
                                    "name" => "The Scent Remained",
                                    "description" => "Increases the maximum number of Rain Swords by 1."
                                ),
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/constellation_2.png",
                                    "title" => "Constellation 2",
                                    "name" => "Rainbow Upon the Azure Sky",
                                    "description" => "Extends the duration of Guhua Sword - Raincutter by 3s. Decreases the Hydro RES of enemies hit by sword rain attacks by 15% for 4s."
                                ),
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/constellation_3.png",
                                    "title" => "Constellation 3",
                                    "name" => "Weaver of Verses",
                                    "description" => "Increases the Level of Guhua Sword - Raincutter by 3. Maximum upgrade level is 15."
                                ),
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/constellation_4.png",
                                    "title" => "Constellation 4",
                                    "name" => "Evilsoother",
                                    "description" => "Throughout the duration of Guhua Sword: Raincutter, the DMG dealt by Guhua Sword: Fatal Rainscreen is increased by 50%."
                                ),
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/constellation_5.png",
                                    "title" => "Constellation 5",
                                    "name" => "Embrace of Rain",
                                    "description" => "Increase the Level of Guhua Sword - Fatal Rainscreen by 3. Maximum upgrade level is 15."
                                ),
                                array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/constellation_6.png",
                                    "title" => "Constellation 6",
                                    "name" => "Hence, Call Them My Own Verses",
                                    "description" => "Activating 2 of Guhua Sword - Raincutter's sword rain attacks greatly increases the DMG of the third. Xingqiu regenerates 3 Energy when sword rain attacks hit enemies."
                                ),
                            );


                            // 各情報を出力
                            foreach ($constellationsInfo as $constellation):
                                ?>
                                <div class="character-skill">
                                    <div class="character-skill-header">
                                        <img class="character-skill-icon"
                                            src="<?php echo esc_url($constellation['icon']); ?>"
                                            alt="<?php echo esc_attr($constellation['title']); ?>">
                                        <h3 class="character-skill-title"><?php echo esc_html($constellation['title']); ?>
                                        </h3>
                                    </div>
                                    <div class="character-skill-body">
                                        <h2 class="character-skill-name"><?php echo esc_html($constellation['name']); ?>
                                        </h2>
                                        <div class="character-skill-description">
                                            <?php echo esc_html($constellation['description']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>




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

                        <!--Character Ascension Section-->
                        <div class="character-ascension" style="display: contents;">
                            <h2 class="character-category">Ayato Ascension Costs</h2>
                            <!--Table Data-->
                            <div class="ReactTable table" id="ascension">
                                <div class="rt-table" role="grid">
                                    <div class="rt-thead -header" style="min-width: 1200px;">
                                        <!-- ヘッダーの定義 -->
                                        <div class="rt-tr" role="row">
                                            <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                                style="text-align: center; flex: 60 0 auto; width: 60px;">Rank</div>
                                            <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                                style="text-align: center; flex: 60 0 auto; width: 60px;">Lvl</div>
                                            <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                                style="text-align: center; flex: 80 0 auto; width: 80px;">Cost</div>
                                            <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                                style="flex: 150 0 auto; width: 150px;">Material</div>
                                            <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                                style="flex: 150 0 auto; width: 150px;">Material</div>
                                            <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                                style="flex: 150 0 auto; width: 150px;">Material</div>
                                            <div class="rt-th -cursor-pointer" role="columnheader" tabindex="-1"
                                                style="flex: 150 0 auto; width: 150px;">Material</div>
                                        </div>
                                    </div>

                                    <!--Table Data-->
                                    <div class="rt-tbody" style="min-width: 1200px;">
                                        <?php
                                        // 行秋の昇華アイテムの情報
                                        $ascensionItems = array(
                                            array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Varunada Lazurite Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Silk Flower", "Count3" => "3", "Material4" => "Damaged Mask", "Count4" => "3"),
                                            array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Varunada Lazurite Fragment", "Count1" => "3", "Material2" => "Cleansing Heart", "Count2" => "2", "Material3" => "Silk Flower", "Count3" => "10", "Material4" => "Damaged Mask", "Count4" => "15"),
                                            array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Varunada Lazurite Fragment", "Count1" => "6", "Material2" => "Cleansing Heart", "Count2" => "4", "Material3" => "Silk Flower", "Count3" => "20", "Material4" => "Stained Mask", "Count4" => "12"),
                                            array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Varunada Lazurite Chunk", "Count1" => "3", "Material2" => "Cleansing Heart", "Count2" => "8", "Material3" => "Silk Flower", "Count3" => "30", "Material4" => "Stained Mask", "Count4" => "18"),
                                            array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Varunada Lazurite Chunk", "Count1" => "6", "Material2" => "Cleansing Heart", "Count2" => "12", "Material3" => "Silk Flower", "Count3" => "45", "Material4" => "Ominous Mask", "Count4" => "12"),
                                            array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Varunada Lazurite Gemstone", "Count1" => "6", "Material2" => "Cleansing Heart", "Count2" => "20", "Material3" => "Silk Flower", "Count3" => "60", "Material4" => "Ominous Mask", "Count4" => "24")
                                            // 他の昇華ランクも同様に追加
                                        );


                                        foreach ($ascensionItems as $ascensionItem):
                                            ?>
                                            <!-- アイテムごとの表示 -->
                                            <div class="rt-tr-group" role="rowgroup">
                                                <div class="rt-tr -odd" role="row">
                                                    <?php
                                                    // Rank、Lvl、Costの情報を表示　Rank:60px Lev:60px Cost:80pxで表示。Flexboxのflex-basisプロパティは、アイテムの基本的なサイズを指定するために使用されています。
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
                                                        $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xingqiu/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
                                                        ?>
                                                        <div class="rt-td" role="gridcell"
                                                            style="flex: 150 0 auto; width: 150px;">
                                                            <?php if ($material != ""): ?>
                                                                <div class="table-image-wrapper">
                                                                    <img class="table-image" src="<?= $materialUrl; ?>"
                                                                        alt="<?= $material; ?>">
                                                                    <span
                                                                        class="table-image-count"><?= $ascensionItem[$countKey]; ?></span>
                                                                </div>
                                                                <?= ucfirst(str_replace(" ", "_", $material)); // アイテム名を大文字とアンダースコアに変更して表示 ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <!--Table Data end-->



                                </div>
                                <!--character team-->
                                <h2 class="character-category">AyatoShowcase</h2>

                                <div class="character-showcase" id="showcase">
                                    <lite-youtube videoid="bQU6hcwglOk" params="rel=0"></lite-youtube>
                                </div>


                                <!--character end-->
                                <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>