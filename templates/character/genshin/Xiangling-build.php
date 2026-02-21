<?php
/**
 * Template Name: Xianling-build.php
 * Description: Template for displaying Genshin Impact character builds Xianling.
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xiangling.png';
                $image_alt = 'Xiangling';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Xiangling Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_pyro.png"
                            alt="Pyro">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_polearm.png"
                            alt="Polearm">Polearm</div>
                    <div class="character-role">Support</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

                    $materials = array(
                        'Diligence' => 'Diligence',
                        "Dvalin's_Claw" => "Dvalin's Claw",
                        'Agnidus_Agate_Sliver' => 'Agnidus Agate Sliver',
                        'Everflame_Seed' => 'Everflame Seed',
                        'Jueyun_Chili' => 'Jueyun Chili',
                        'Slime_Condensate' => 'Slime Condensate',
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
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Xiangling Best Weapons</h2>
                            <?php
                            $xiangling_weapons = array(
                                array("Staff of the Scarlet Sands", 1, 5),
                                array("Staff of Homa", 2, 5),
                                array("The Catch", 3, 4),
                                array("Engulfing Lightning", 4, 5),
                                array("Deathmatch", 5, 4),
                            );

                            foreach ($xiangling_weapons as $weapon):
                                $weaponRank = $weapon[1];
                                $weaponRarity = $weapon[2];
                                $weaponName = str_replace('_', ' ', $weapon[0]);
                                ?>
                                <div class="character-build-weapon">
                                    <div class="character-build-weapon-rank"><?php echo $weaponRank; ?></div>
                                    <img class="character-build-weapon-icon rarity-<?php echo $weaponRarity; ?>"
                                        src="https://rerollcdn.com/GENSHIN/Weapons/<?php echo urlencode(str_replace(' ', '_', $weaponName)); ?>.png"
                                        alt="<?php echo $weaponName; ?>">
                                    <div class="character-build-weapon-name"><?php echo $weaponName; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Xiangling Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Xiangling Best Artifacts</h2>

                            <?php
                            // アーティファクト名 ランク
                            $artifacts = array(
                                array('Emblem of Severed Fate', 1),
                                array('Crimson Witch of Flames', 2),
                                array('Crimson Witch of Flames', 2),
                                array("Crimson Witch of Flames", 3),
                                array('Emblem of Severed Fate', 3),
                                array("Emblem of Severed Fate", 4),
                                array("Noblesse Oblige", 4),
                                array("Crimson Witch of Flames", 5),
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
                                    $artifactName = str_replace('_', ' ', $artifact[0]);
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



                    <!-- Xiangling Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Xiangling Best Stats</h2>
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
                        <h2 class="character-category">Best Xiangling Teams</h2>
                        <!-- AyatoFreeze Team -->
                        <div class="character-team">
                            <div class="character-team-name">AyatoFreeze
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Xiangling_Teams1["Xiangling"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Xiangling_Teams1["Faruzan"] = array("element" => "anemo", "rarity" => "rarity-4");
                                    $Xiangling_Teams1["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Xiangling_Teams1["Xiao"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Xiangling_Teams1 as $name => $info):
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
                            <div class="character-team-name">Ayaka/Ganyu Mono Cryo
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Xiangling_Teams2["Xiangling"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Xiangling_Teams2["Diluc"] = array("element" => "pyro", "rarity" => "rarity-5");
                                    $Xiangling_Teams2["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Xiangling_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                    // キャラクター情報を出力
                                    foreach ($Xiangling_Teams2 as $name => $info):
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
                            <div class="character-team-name">Ayaka/Furina Jean Cyro
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Xiangling_Teams3["Xiangling"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Xiangling_Teams3["Diluc"] = array("element" => "pyro", "rarity" => "rarity-5");
                                    $Xiangling_Teams3["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Xiangling_Teams3["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                    // キャラクター情報を出力
                                    foreach ($Xiangling_Teams3 as $name => $info):
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
                            <h2 class="character-category">Xiangling Talents</h2>
                            <?php
                            // Xianglingの情報を格納する配列
                            $XianglingInfo = array(
                                "NormalAttack" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/UI_GachaTypeIcon_Polearm.png",
                                    "title" => "Normal Attack",
                                    "name" => "Dough-Fu",
                                    "description" => "Normal Attack Performs up to 5 consecutive spear strikes. Charged Attack Consumes a certain amount of Stamina to lunge forward, dealing damage to enemies along the way. Plunging Attack Plunges from mid-air to strike the ground below, damaging enemies along the path and dealing AoE DMG upon impact."
                                ),
                                "ElementalSkill" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/talent_2.png",
                                    "title" => "Elemental Skill",
                                    "name" => "Guoba Attack",
                                    "description" => "Summons Guoba the Panda. Guoba continuously breathes fire, dealing AoE Pyro DMG."
                                ),
                                "ElementalBurst" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/talent_3.png",
                                    "title" => "Elemental Burst",
                                    "name" => "Pyronado",
                                    "description" => "Displaying her mastery over both fire and polearms, Xiangling sends a Pyronado whirling around her. The Pyronado will move with your character for so long as the ability persists, dealing Pyro DMG to all enemies in its path."
                                ),
                            );

                            // タレント情報をループで表示
                            foreach ($XianglingInfo as $talent):
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
                                            <?php echo esc_html($talent['description']); ?></div>
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
                            <h2 class="character-category">Xiangling Passives</h2>
                            <?php
                            // Xianglingのパッシブおよびコンステレーション情報を格納する配列
                            $passivesInfo = array(
                                "Ascension1" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/talent_4.png",
                                    "title" => "Ascension 1",
                                    "name" => "Crossfire",
                                    "description" => "Increases the flame range of Guoba by 20%."
                                ),
                                "Ascension4" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/talent_5.png",
                                    "title" => "Ascension 4",
                                    "name" => "Beware, It's Super Hot!",
                                    "description" => "When Guoba Attack's effect ends, Guoba leaves a chili pepper on the spot where it disappeared. Picking up a chili pepper increases ATK by 10% for 10s."
                                ),
                                "Passive" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/talent_6.png",
                                    "title" => "Passive",
                                    "name" => "Chef de Cuisine",
                                    "description" => "When Xiangling cooks an ATK-boosting dish perfectly, she has a 12% chance to receive double the product."
                                )
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
                                            <?php echo esc_html($passive['description']); ?></div>
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
                            <h2 class="character-category">Xiangling Constellations</h2>
                            <?php
                            $constellationsInfo = array(
                                "Constellation1" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/constellation_1.png",
                                    "title" => "Constellation 1",
                                    "name" => "Crispy Outside, Tender Inside",
                                    "description" => "Enemies hit by Guoba's attacks have their Pyro RES reduced by 15% for 6s."
                                ),
                                "Constellation2" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/constellation_2.png",
                                    "title" => "Constellation 2",
                                    "name" => "Oil Meets Fire",
                                    "description" => "The last attack in a Normal Attack sequence applies the Implode status onto the enemy for 2s. An explosion will occur once this duration ends, dealing 75% of Xiangling's ATK as AoE Pyro DMG."
                                ),
                                "Constellation3" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/constellation_3.png",
                                    "title" => "Constellation 3",
                                    "name" => "Deepfry",
                                    "description" => "Increases the Level of Pyronado by 3. Maximum upgrade level is 15."
                                ),
                                "Constellation4" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/constellation_4.png",
                                    "title" => "Constellation 4",
                                    "name" => "Slowbake",
                                    "description" => "Pyronado's duration is increased by 40%."
                                ),
                                "Constellation5" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/constellation_5.png",
                                    "title" => "Constellation 5",
                                    "name" => "Guoba Mad",
                                    "description" => "Increase the Level of Guoba Attack by 3. Maximum upgrade level is 15."
                                ),
                                "Constellation6" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/xiangling/constellation_6.png",
                                    "title" => "Constellation 6",
                                    "name" => "Condensed Pyronado",
                                    "description" => "For the duration of Pyronado, all party members receive a 15% Pyro DMG Bonus."
                                )
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
                                            <?php echo esc_html($constellation['description']); ?></div>
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
                                        // シャンリンの昇華アイテムの情報
                                        $ascensionItems = array(
                                            array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Agnidus Agate Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Jueyun Chili", "Count3" => "3", "Material4" => "Slime Condensate", "Count4" => "3"),
                                            array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Agnidus Agate Fragment", "Count1" => "3", "Material2" => "Everflame Seed", "Count2" => "2", "Material3" => "Jueyun Chili", "Count3" => "10", "Material4" => "Slime Condensate", "Count4" => "15"),
                                            array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Agnidus Agate Fragment", "Count1" => "6", "Material2" => "Everflame Seed", "Count2" => "4", "Material3" => "Jueyun Chili", "Count3" => "20", "Material4" => "Slime Secretions", "Count4" => "12"),
                                            array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Agnidus Agate Chunk", "Count1" => "3", "Material2" => "Everflame Seed", "Count2" => "8", "Material3" => "Jueyun Chili", "Count3" => "30", "Material4" => "Slime Secretions", "Count4" => "18"),
                                            array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Agnidus Agate Chunk", "Count1" => "6", "Material2" => "Everflame Seed", "Count2" => "12", "Material3" => "Jueyun Chili", "Count3" => "45", "Material4" => "Slime Concentrate", "Count4" => "12"),
                                            array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Agnidus Agate Gemstone", "Count1" => "6", "Material2" => "Everflame Seed", "Count2" => "20", "Material3" => "Jueyun Chili", "Count3" => "60", "Material4" => "Slime Concentrate", "Count4" => "24")
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
                                                        $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/xiangling/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
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
                                    <lite-youtube videoid="jBPMh_6ZY_8" params="rel=0"></lite-youtube>
                                </div>


                                <!--character end-->
                                <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>