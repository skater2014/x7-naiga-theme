<?php
/**
 * Template Name: Xiao-build.php
 * Description: Template for displaying Genshin Impact character builds Xiao.
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao.png';
                $image_alt = 'Nahida';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Xiao Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/element_anemo.png"
                            alt="Anemo">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_polearm.png"
                            alt="Polearm">Polearm</div>
                    <div class="character-role">Main DPS</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/farming/';

                    $materials = array(
                        "Prosperity" => "Prosperity",
                        "Shadow_of_the_Warrior" => "Shadow of the Warrior",
                        "Vayuda_Turquoise_Sliver" => "Vayuda Turquoise Sliver",
                        "Juvenile_Jade" => "Juvenile Jade",
                        "Qingxin" => "Qingxin",
                        "Slime_Condensate" => "Slime Condensate",
                    );


                    // ucwords 最初の文字を大文字にかえる
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
                        <!--  Xiao Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title"> Xiao Best Weapons</h2>
                            <?php
                            $weapons = array(
                                array("Primordial Jade Winged-Spear", 1, 5),
                                array("Staff of Homa", 2, 5),
                                array("Vortex Vanquisher", 3, 5),
                                array("Calamity Queller", 4, 5),
                                array("Deathmatch", 5, 4)
                            );

                            foreach ($weapons as $weapon):
                                $weaponRank = $weapon[1];
                                $weaponRarity = $weapon[2];
                                $weaponName = str_replace('_', ' ', $weapon[0]);
                                ?>
                                <div class="character-build-weapon">
                                    <div class="character-build-weapon-rank"><?php echo $weaponRank; ?></div>
                                    <img class="character-build-weapon-icon rarity-<?php echo $weaponRarity; ?>"
                                        src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/<?php echo urlencode(str_replace(' ', '_', $weaponName)); ?>.png"
                                        alt="<?php echo $weaponName; ?>">
                                    <div class="character-build-weapon-name"><?php echo $weaponName; ?></div>
                                </div>
                            <?php endforeach; ?>

                        </div>


                        <!--  Xiao Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Xiao Best Artifacts</h2>

                            <?php
                            // アーティファクト ランクXiaoの最適なアーティファクトの情報
                            $artifacts = array(
                                array("Vermillion Hereafter", 1, 4),
                                array("Desert Pavilion Chronicle", 2, 4),
                                array("Desert Pavilion Chronicle", 3, 2),
                                array("Vermillion Hereafter", 3, 2),
                                array("Shimenawa's Reminiscence", 4, 2),
                                array("Vermillion Hereafter", 4, 2),
                                array("Desert Pavilion Chronicle", 5, 2),
                                array("Viridescent Venerer", 5, 2),
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
                                    $imageUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/" . str_replace(' ', '_', strtolower($artifactName)) . ".png";
                                    $artifactRank = $artifact[1];

                                    // グループ内に複数の要素がある場合はランクを表示
                                    echo '<div class="character-build-weapon-rank">' . $artifactRank . '</div>';

                                    echo '<div class="character-build-weapon-content' . (count($group) > 1 ? ' full' : '') . '">';
                                    echo '<img class="character-build-weapon-icon rarity-5" src="' . $imageUrl . '" alt="' . $artifactName . '">';
                                    echo '<div class="character-build-weapon-name">' . str_replace('_', ' ', $artifactName) . '</div>';
                                    echo '<div class="character-build-weapon-count">' . $artifactRank . '</div>';
                                    echo '</div>';
                                }

                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>


                    <!-- Xiao Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Xiao Best Stats</h2>
                        <div class="character-stats-item"><b>Sands:</b> ATK%</div>
                        <div class="character-stats-item"><b>Goblet:</b> Anemo DMG</div>
                        <div class="character-stats-item"><b>Circlet:</b> CRIT Rate / CRIT DMG</div>
                        <div class="character-stats-item full"><b>Substats:</b> CRIT Rate / CRIT DMG &gt; ATK% &gt;
                            Energy Recharge</div>
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
                        <h2 class="character-category">Best Xiao Teams</h2>
                        <!-- AyatoFreeze Team -->
                        <div class="character-team">
                            <div class="character-team-name">Xiao Standard
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Xiao_Teams1["Xiao"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Xiao_Teams1["Faruzan"] = array("element" => "anemo", "rarity" => "rarity-4");
                                    $Xiao_Teams1["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Xiao_Teams1["Xianyun"] = array("element" => "anemo", "rarity" => "rarity-5");

                                    // キャラクター情報を出力
                                    foreach ($Xiao_Teams1 as $name => $info):
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
                            <div class="character-team-name">Xiao Standard
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Xiao_Teams2["Xiao"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Xiao_Teams2["Faruzan"] = array("element" => "anemo", "rarity" => "rarity-4");
                                    $Xiao_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                    $Xiao_Teams2["Zhongli"] = array("element" => "geo", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Xiao_Teams2 as $name => $info):
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
                            <div class="character-team-name">Xiao Geo
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Xiao_Teams3["Xiao"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Xiao_Teams3["Faruzan"] = array("element" => "anemo", "rarity" => "rarity-4");
                                    $Xiao_Teams3["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Xiao_Teams3["Zhongli"] = array("element" => "geo", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Xiao_Teams3 as $name => $info):
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
                            <h2 class="character-category"> Xiao Talents</h2>
                            <?php
                            $talents = array(
                                array(
                                    "name" => "Whirlwind Thrust",
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/UI_GachaTypeIcon_Polearm.png",
                                    "type" => "Normal Attack",
                                    "description" => "Performs up to 6 consecutive spear strikes. Charged Attack consumes a certain amount of Stamina to perform an upward thrust. Plunging Attack plunges from mid-air to strike the ground from below, damaging opponents along the path and dealing AoE DMG upon impact. Xiao does not take DMG from performing Plunging Attacks."
                                ),
                                array(
                                    "name" => "Lemniscatic Wind Cycling",
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/talent_2.png",
                                    "type" => "Elemental Skill",
                                    "description" => "Xiao lunges forward, dealing Anemo DMG to opponents in his path. Can be used in mid-air. Starts with 2 charges. Generates 3 elemental particles when hits at least 1 target."
                                ),
                                array(
                                    "name" => "Bane of All Evil",
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/talent_3.png",
                                    "type" => "Elemental Burst",
                                    "description" => "Xiao dons the Yaksha Mask that set gods and demons trembling millennia ago. Yaksha's Mask: Greatly increases Xiao's jumping ability. Increases his attack AoE and attack DMG. Converts attack DMG into Anemo DMG, which cannot be overridden by any other elemental infusion. In this state, Xiao will continuously lose HP. The descriptions of this skill end when Xiao leaves the field."
                                )
                            );

                            // タレント情報をループで表示
                            foreach ($talents as $talent):
                                ?>
                                <div class="character-skill">
                                    <div class="character-skill-header">
                                        <img class="character-skill-icon" src="<?php echo esc_url($talent['icon']); ?>"
                                            alt="<?php echo esc_attr($talent['name']); ?>">
                                        <h3 class="character-skill-title"><?php echo esc_html($talent['type']); ?></h3>
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
                            <h2 class="character-category"> Xiao Passives</h2>
                            <?php
                            $passives = array(
                                array(
                                    "type" => "Ascension 1",
                                    "name" => "Conqueror of Evil: Tamer of Demons",
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/talent_4.png",
                                    "description" => "While under the descriptions of Bane of All Evil, all DMG dealt by Xiao increases by 5%. DMG increases by a further 5% for every 3s the ability persists. The maximum DMG Bonus is 25%."
                                ),
                                array(
                                    "type" => "Ascension 4",
                                    "name" => "Dissolution Eon: Heaven Fall",
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/talent_5.png",
                                    "description" => "Using Lemniscatic Wind Cycling increases the DMG of subsequent uses of Lemniscatic Wind Cycling by 15%. This description lasts for 7s, and has a maximum of 3 stacks. Gaining a new stack refreshes the description's duration."
                                ),
                                array(
                                    "type" => "Passive",
                                    "name" => "Transcension: Gravity Defier",
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/talent_6.png",
                                    "description" => "Decreases climbing Stamina consumption for your own party members by 20%. Not stackable with Passive Talents that provide the exact same descriptions."
                                )
                            );

                            // パッシブ情報をループで表示
                            foreach ($passives as $passive):
                                ?>
                                <div class="character-skill">
                                    <div class="character-skill-header">
                                        <img class="character-skill-icon" src="<?php echo esc_url($passive['icon']); ?>"
                                            alt="<?php echo esc_attr($passive['name']); ?>">
                                        <h3 class="character-skill-title"><?php echo esc_html($passive['type']); ?></h3>
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
                            <h2 class="character-category"> Xiao Constellations</h2>
                            <?php
                            $constellations = array(
                                array(
                                    "name" => "Conqueror of Evil: Tamer of Demons",
                                    'title' => 'Constellation 1',
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/constellation_1.png",
                                    "description" => "Increases Lemniscatic Wind Cycling's charges by 1."
                                ),
                                array(
                                    "name" => "Annihilation Eon - Blossom of Kaleidos",
                                    'title' => 'Constellation 2',
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/constellation_2.png",
                                    "description" => "When in the party but not on the field, Xiao's Energy Recharge is increased by 25%."
                                ),
                                array(
                                    "name" => "Conqueror of Evil: Wrath Deity",
                                    'title' => 'Constellation 3',
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/constellation_3.png",
                                    "description" => "Increases the Level of Lemniscatic Wind Cycling by 3. Maximum upgrade level is 15."
                                ),
                                array(
                                    "name" => "Transcension - Extinction of Suffering",
                                    'title' => 'Constellation 4',
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/constellation_4.png",
                                    "description" => "When Xiao's HP falls below 50%, he gains a 100% DEF Bonus."
                                ),
                                array(
                                    "name" => "Evolution Eon - Origin of Ignorance",
                                    'title' => 'Constellation 5',
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/constellation_5.png",
                                    "description" => "Increase the Level of Bane of All Evil by 3. Maximum upgrade level is 15."
                                ),
                                array(
                                    "name" => "Conqueror of Evil: Guardian Yaksha",
                                    'title' => 'Constellation 6',
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/constellation_6.png",
                                    "description" => "While under the descriptions of Bane of All Evil, hitting at least 2 opponents with Xiao's Plunging Attack will immediately grant him 1 charge of Lemniscatic Wind Cycling and for the next 1s, he may use Lemniscatic Wind Cycling while ignoring its CD."
                                )
                            );

                            // 各情報を出力
                            foreach ($constellations as $constellation):
                                ?>
                                <div class="character-skill">
                                    <div class="character-skill-header">
                                        <img class="character-skill-icon"
                                            src="<?php echo esc_url($constellation['icon']); ?>"
                                            alt="<?php echo esc_attr($constellation['name']); ?>">
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
                            <h2 class="character-category"> Xiao Ascension Costs</h2>
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
                                        // Xiaoの昇華アイテムの情報
                                        $xiao_ascension_items = array(
                                            array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Vayuda Turquoise Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Qingxin", "Count3" => "3", "Material4" => "Slime Condensate", "Count4" => "3"),
                                            array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Vayuda Turquoise Fragment", "Count1" => "3", "Material2" => "Juvenile Jade", "Count2" => "2", "Material3" => "Qingxin", "Count3" => "10", "Material4" => "Slime Condensate", "Count4" => "15"),
                                            array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Vayuda Turquoise Fragment", "Count1" => "6", "Material2" => "Juvenile Jade", "Count2" => "4", "Material3" => "Qingxin", "Count3" => "20", "Material4" => "Slime Secretions", "Count4" => "12"),
                                            array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Vayuda Turquoise Chunk", "Count1" => "3", "Material2" => "Juvenile Jade", "Count2" => "8", "Material3" => "Qingxin", "Count3" => "30", "Material4" => "Slime Secretions", "Count4" => "18"),
                                            array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Vayuda Turquoise Chunk", "Count1" => "6", "Material2" => "Juvenile Jade", "Count2" => "12", "Material3" => "Qingxin", "Count3" => "45", "Material4" => "Slime Concentrate", "Count4" => "12"),
                                            array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Vayuda Turquoise Gemstone", "Count1" => "6", "Material2" => "Juvenile Jade", "Count2" => "20", "Material3" => "Qingxin", "Count3" => "60", "Material4" => "Slime Concentrate", "Count4" => "24")
                                            // 他の昇華ランクも同様に追加
                                        );

                                        foreach ($xiao_ascension_items as $ascensionItem):
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
                                                        $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Xiao/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
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
                                <h2 class="character-category">Showcase</h2>

                                <div class="character-showcase" id="showcase">
                                    <lite-youtube videoid="UV5m1tmMU_0" params="rel=0"></lite-youtube>
                                </div>


                                <!--character end-->
                                <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer(); ?>