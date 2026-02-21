<?php
/**
 * Template Name: Nahida-build.php
 * Description: Template for displaying Genshin Impact character builds Nahida.
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida.png';
                $image_alt = 'Nahida';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Nahida Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_dendro.png"
                            alt="Dendro">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_sword.png"
                            alt="Catalyst">Catalyst</div>
                    <div class="character-role">Support</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

                    $materials = array(
                        "Ingenuity" => "Ingenuity",
                        "Puppet_Strings" => "Puppet Strings",
                        "Nagadus_Emerald_Sliver" => "Nagadus Emerald Sliver",
                        "Quelled_Creeper" => "Quelled Creeper",
                        "Kalpalata_Lotus" => "Kalpalata Lotus",
                        "Fungal_Spores" => "Fungal Spores",
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
                        <!-- Eula Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Eula Best Weapons</h2>
                            <?php
                            $weapons = array(
                                array("A Thousand Floating Dreams", 1),
                                array("Kagura's Verity", 2),
                                array("Sacrificial Fragments", 3),
                                array("Wandering Evenstar", 4),
                                array("The Widsith", 5)
                            );

                            foreach ($weapons as $weapon):
                                $weaponRank = $weapon[1];
                                $weaponName = str_replace('_', ' ', $weapon[0]);
                                ?>
                                <div class="character-build-weapon">
                                    <div class="character-build-weapon-rank"><?php echo $weaponRank; ?></div>
                                    <img class="character-build-weapon-icon rarity-5"
                                        src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Nahida/<?php echo urlencode(str_replace(' ', '_', $weaponName)); ?>.png"
                                        alt="<?php echo $weaponName; ?>">
                                    <div class="character-build-weapon-name"><?php echo $weaponName; ?></div>
                                </div>
                            <?php endforeach; ?>

                        </div>


                        <!-- Eula Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Eula Best Artifacts</h2>

                            <?php
                            // アーティファクト ランク
                            // Eulaの最適なアーティファクトの情報
                            $artifacts = array(
                                array("Deepwood Memories", 1, 4),
                                array("Gilded Dreams", 2, 4),
                                array("Flower of Paradise Lost", 3, 2),
                                array("Gilded Dreams", 3, 2),
                                array("Gilded Dreams", 4, 2),
                                array("Wanderer's Troupe", 4, 2),
                                array("Emblem of Severed Fate", 5, 2),
                                array("Gilded Dreams", 5, 2)
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




                    <!-- Nahida Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Nahida Best Stats</h2>
                        <div class="character-stats-item"><b>Sands:</b> Elemental Mastery</div>
                        <div class="character-stats-item"><b>Goblet:</b> Elemental Mastery</div>
                        <div class="character-stats-item"><b>Circlet:</b> Elemental Mastery</div>
                        <div class="character-stats-item full"><b>Substats:</b> Energy Recharge &gt; Elemental Mastery
                            &gt; CRIT Rate / CRIT DMG</div>
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
                        <h2 class="character-category">Best Nahida Teams</h2>
                        <!-- AyatoFreeze Team -->
                        <div class="character-team">
                            <div class="character-team-name">Nilou Bloom
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Nahida_Teams1["Nahida"] = array("element" => "dendro", "rarity" => "rarity-5");
                                    $Nahida_Teams1["Kokomi"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Nahida_Teams1["Fischl"] = array("element" => "electro", "rarity" => "rarity-4");
                                    $Nahida_Teams1["Kazuha"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Nahida_Teams1 as $name => $info):
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
                            <div class="character-team-name">Alhaitham Quicken
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Nahida_Teams2["Nahida"] = array("element" => "dendro", "rarity" => "rarity-5");
                                    $Nahida_Teams2["Yae Miko"] = array("element" => "electro", "rarity" => "rarity-5");
                                    $Nahida_Teams2["Kuki Shinobu"] = array("element" => "electro", "rarity" => "rarity-4");
                                    $Nahida_Teams2["Alhaitham"] = array("element" => "dendro", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Nahida_Teams2 as $name => $info):
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
                            <div class="character-team-name">Cyno Aggravate
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Nahida_Teams3["Nahida"] = array("element" => "dendro", "rarity" => "rarity-5");
                                    $Nahida_Teams3["Raiden"] = array("element" => "electro", "rarity" => "rarity-5");
                                    $Nahida_Teams3["Cyno"] = array("element" => "electro", "rarity" => "rarity-5");
                                    $Nahida_Teams3["Zhongli"] = array("element" => "dendro", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Nahida_Teams3 as $name => $info):
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
                            <h2 class="character-category">Nahida Talents</h2>
                            <?php
                            $nahida_talents = array(
                                "normal_attack" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/UI_GachaTypeIcon_Catalyst.png",
                                    "title" => "Normal Attack",
                                    "name" => "Akara",
                                    "description" => "Normal Attack
                                  Performs up to 4 attacks that deal<span class='dendro'> Dendro </span>DMG to opponents in front of her.

                                  Charged Attack
                                  Consumes a certain amount of Stamina to deal AoE<span class='dendro'> Dendro </span>DMG to opponents in front of her after a short casting time.

                                  Plunging Attack
                                  Calling upon the might of Dendro, Nahida plunges towards the ground from mid-air, damaging all opponents in her path. Deals AoE<span class='dendro'> Dendro </span>DMG upon impact with the ground."
                                ),
                                "elemental_skill" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/talent_2.png",
                                    "title" => "Elemental Skill",
                                    "name" => "All Schemes to Know",
                                    "description" => "Sends forth karmic bonds of wood and tree from her side, dealing AoE<span class='dendro'> Dendro </span>DMG and marking up to 8 opponents hit with the Seed of Skandha.
                                  When held, this skill will trigger differently.

                                  Hold
                                  Enters Aiming Mode, which will allow you to select a limited number of opponents within a limited area. During this time, Nahida's resistance to interruption will be increased.
                                  When released, this skill deals<span class='dendro'> Dendro </span>DMG to these opponents and marks them with the Seed of Skandha.
                                  Aiming Mode will last up to 5s and can select a maximum of 8 opponents.

                                  Seed of Skandha
                                  Opponents who have been marked by the Seed of Skandha will be linked to one another up till a certain distance.
                                  After you trigger Elemental Reactions on opponents who are affected by the Seeds of Skandha or when they take DMG from<span class='dendro'> Dendro </span>Cores (including Burgeon and Hyperbloom DMG), Nahida will unleash Tri-Karma Purification on the opponents and all connected opponents, dealing<span class='dendro'> Dendro </span>DMG based on her ATK and Elemental Mastery.
                                  You can trigger at most 1 Tri-Karma Purification within a short period of time."
                                ),
                                "elemental_burst" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/talent_3.png",
                                    "title" => "Elemental Burst",
                                    "name" => "Illusory Heart",
                                    "description" => "Manifests the Court of Dreams and expands the Shrine of Maya.
                                  When the Shrine of Maya field is unleashed, the following effects will be separately unleashed based on the Elemental Types present within the party.

                                  Pyro: While Nahida remains within the Shrine of Maya, the DMG dealt by Tri-Karma Purification from \"All Schemes to Know\" is increased.
                                  Electro: While Nahida remains within the Shrine of Maya, the interval between each Tri-Karma Purification from \"All Schemes to Know\" is decreased.
                                  Hydro: The Shrine of Maya's duration is increased.

                                  If there are at least 2 party members of the aforementioned Elemental Types present when the field is deployed, the aforementioned effects will be increased further.
                                  Even if Nahida is not on the field, these bonuses will still take effect so long as party members are within the Shrine of Maya."
                                ),
                            );



                            // タレント情報をループで表示
                            foreach ($nahida_talents as $talent):
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
                            <h2 class="character-category">Nahida Passives</h2>
                            <?php
                            $nahida_passives = array(
                                "ascension_1" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/talent_4.png",
                                    "title" => "Ascension 1",
                                    "name" => "Compassion Illuminated",
                                    "description" => "When unleashing Illusory Heart, the Shrine of Maya will gain the following effects:
                                      The Elemental Mastery of the active character within the field will be increased by 25% of the Elemental Mastery of the party member with the highest Elemental Mastery.
                                      You can gain a maximum of 250 Elemental Mastery in this manner."
                                ),
                                "ascension_4" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/talent_5.png",
                                    "title" => "Ascension 4",
                                    "name" => "Awakening Elucidated",
                                    "description" => "Each point of Nahida's Elemental Mastery beyond 200 will grant 0.1% Bonus DMG and 0.03% CRIT Rate to Tri-Karma Purification from All Schemes to Know.
                                      A maximum of 80% Bonus DMG and 24% CRIT Rate can be granted to Tri-Karma Purification in this manner."
                                ),
                                "passive" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/talent_6.png",
                                    "title" => "Passive",
                                    "name" => "On All Things Meditated",
                                    "description" => "Nahida can use All Schemes to Know to interact with some harvestable items within a fixed AoE. This skill may even have some other effects..."
                                ),
                            );


                            // パッシブ情報をループで表示
                            foreach ($nahida_passives as $passive):
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
                            <h2 class="character-category">Nahida Constellations</h2>
                            <?php
                            $nahida_constellations = array(
                                "constellation_1" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/constellation_1.png",
                                    "title" => "Constellation 1",
                                    "name" => "The Seed of Stored Knowledge",
                                    "description" => "When the Shrine of Maya is unleashed and the Elemental Types of the party members are being tabulated, the count will add 1 to the number of Pyro, Electro, and Hydro characters respectively."
                                ),
                                "constellation_2" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/constellation_2.png",
                                    "title" => "Constellation 2",
                                    "name" => "The Root of All Fullness",
                                    "description" => "Opponents that are marked by Seeds of Skandha applied by Nahida herself will be affected by the following effects:
                                        - Burning, Bloom, Hyperbloom, and Burgeon Reaction DMG can score CRIT Hits. CRIT Rate and CRIT DMG are fixed at 20% and 100% respectively.
                                        - Within 8s of being affected by Quicken, Aggravate, Spread, DEF is decreased by 30%."
                                ),
                                "constellation_3" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/constellation_3.png",
                                    "title" => "Constellation 3",
                                    "name" => "The Shoot of Conscious Attainment",
                                    "description" => "Increases the Level of All Schemes to Know by 3.
                                        Maximum upgrade level is 15."
                                ),
                                "constellation_4" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/constellation_4.png",
                                    "title" => "Constellation 4",
                                    "name" => "The Stem of Manifest Inference",
                                    "description" => "When 1/2/3/(4 or more) nearby opponents are affected by All Schemes to Know's Seeds of Skandha, Nahida's Elemental Mastery will be increased by 100/120/140/160."
                                ),
                                "constellation_5" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/constellation_5.png",
                                    "title" => "Constellation 5",
                                    "name" => "The Leaves of Enlightening Speech",
                                    "description" => "Increase the Level of Illusory Heart by 3.
                                        Maximum upgrade level is 15."
                                ),
                                "constellation_6" => array(
                                    "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/constellation_6.png",
                                    "title" => "Constellation 6",
                                    "name" => "The Fruit of Reason's Culmination",
                                    "description" => "When Nahida hits an opponent affected by All Schemes to Know's Seeds of Skandha with Normal or Charged Attacks after unleashing Illusory Heart, she will use Tri-Karma Purification: Karmic Oblivion on this opponent and all connected opponents, dealing Dendro DMG based on 200% of Nahida's ATK and 400% of her Elemental Mastery.
                                        DMG dealt by Tri-Karma Purification: Karmic Oblivion is considered Elemental Skill DMG and can be triggered once every 0.2s.
                                        This effect can last up to 10s and will be removed after Nahida has unleashed 6 instances of Tri-Karma Purification: Karmic Oblivion."
                                ),
                            );


                            // 各情報を出力
                            foreach ($nahida_constellations as $constellation):
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
                                        // Nahidaの昇華アイテムの情報
                                        $nahida_ascension_items = array(
                                            array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Nagadus Emerald Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Kalpalata Lotus", "Count3" => "3", "Material4" => "Fungal Spores", "Count4" => "3"),
                                            array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Nagadus Emerald Fragment", "Count1" => "3", "Material2" => "Quelled Creeper", "Count2" => "2", "Material3" => "Kalpalata Lotus", "Count3" => "10", "Material4" => "Fungal Spores", "Count4" => "15"),
                                            array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Nagadus Emerald Fragment", "Count1" => "6", "Material2" => "Quelled Creeper", "Count2" => "4", "Material3" => "Kalpalata Lotus", "Count3" => "20", "Material4" => "Luminescent Pollen", "Count4" => "12"),
                                            array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Nagadus Emerald Chunk", "Count1" => "3", "Material2" => "Quelled Creeper", "Count2" => "8", "Material3" => "Kalpalata Lotus", "Count3" => "30", "Material4" => "Luminescent Pollen", "Count4" => "18"),
                                            array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Nagadus Emerald Chunk", "Count1" => "6", "Material2" => "Quelled Creeper", "Count2" => "12", "Material3" => "Kalpalata Lotus", "Count3" => "45", "Material4" => "Crystalline Cyst Dust", "Count4" => "12"),
                                            array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Nagadus Emerald Gemstone", "Count1" => "6", "Material2" => "Quelled Creeper", "Count2" => "20", "Material3" => "Kalpalata Lotus", "Count3" => "60", "Material4" => "Crystalline Cyst Dust", "Count4" => "24")
                                            // 他の昇華ランクも同様に追加
                                        );


                                        foreach ($nahida_ascension_items as $ascensionItem):
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
                                                        $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Nahida/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
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
                                    <lite-youtube videoid="F3Ld3hLesBo" params="rel=0"></lite-youtube>
                                </div>


                                <!--character end-->
                                <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>