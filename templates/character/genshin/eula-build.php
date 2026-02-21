<?php
/**
 * Template Name: eula-build.php
 * Description: Template for displaying Genshin Impact character builds Eula.
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Eula.png';
                $image_alt = 'Eula';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Eula Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_cryo.png"
                            alt="Cryo">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_claymore.png"
                            alt="Catalyst">Claymore</div>
                    <div class="character-role">Support</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

                    $materials = array(
                        'Resistance' => 'Resistance',
                        "Dragon Lord's Crown" => "Dragon Lord's Crown",
                        'Shivada Jade Sliver' => 'Shivada Jade Sliver',
                        'Crystalline Bloom' => 'Crystalline Bloom',
                        'Dandelion Seed' => 'Dandelion Seed',
                        'Damaged Mask' => 'Damaged Mask',
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
                        <!-- Eula Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Eula Best Weapons</h2>
                            <?php
                            $weapons = array(
                                array("Song of Broken Pines", 1, 5),
                                array("Wolf's Gravestone", 2, 5),
                                array("Beacon of the Reed Sea", 3, 5),
                                array("Redhorn Stonethresher", 4, 5),
                                array("Serpent Spine", 5, 4)
                            );

                            foreach ($weapons as $weapon):
                                $weaponRank = $weapon[1];
                                $weaponRarity = $weapon[2];
                                $weaponName = str_replace('_', ' ', $weapon[0]);
                                ?>
                                <div class="character-build-weapon">
                                    <div class="character-build-weapon-rank"><?php echo $weaponRank; ?></div>
                                    <img class="character-build-weapon-icon rarity-<?php echo $weaponRarity; ?>"
                                        src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Eula/<?php echo urlencode(str_replace(' ', '_', $weaponName)); ?>.png"
                                        alt="<?php echo $weaponName; ?>">
                                    <div class="character-build-weapon-name"><?php echo $weaponName; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Eula Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Eula Best Artifacts</h2>

                            <?php
                            // アーティファクト名 ランク
                            $artifacts = array(
                                array('Pale Flame', 1),
                                array('Bloodstained Chivalry', 2),
                                array('Pale Flame', 2),
                                array("Gladiator's Finale", 3),
                                array('Pale Flame', 3),
                                array("Bloodstained Chivalry", 4),
                                array("Gladiator's Finale", 4),
                                array("Gladiator's Finale", 5),
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



                    <!-- Eula Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Eula Best Stats</h2>
                        <div class="character-stats-item"><b>Sands:</b> Energy Recharge / ATK%</div>
                        <div class="character-stats-item"><b>Goblet:</b> ATK%</div>
                        <div class="character-stats-item"><b>Circlet:</b> Healing Bonus / ATK%</div>
                        <div class="character-stats-item full"><b>Substats:</b> Energy Recharge &gt; ATK% &gt; CRIT Rate
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
                        <h2 class="character-category">Best Eula Teams</h2>
                        <!-- AyatoFreeze Team -->
                        <div class="character-team">
                            <div class="character-team-name">AyatoFreeze
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Eula_Teams1["Eula"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Eula_Teams1["Faruzan"] = array("element" => "anemo", "rarity" => "rarity-4");
                                    $Eula_Teams1["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Eula_Teams1["Xiao"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Eula_Teams1 as $name => $info):
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
                                    $Eula_Teams2["Eula"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Eula_Teams2["Diluc"] = array("element" => "pyro", "rarity" => "rarity-5");
                                    $Eula_Teams2["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Eula_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                    // キャラクター情報を出力
                                    foreach ($Eula_Teams2 as $name => $info):
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
                                    $Eula_Teams3["Eula"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Eula_Teams3["Diluc"] = array("element" => "pyro", "rarity" => "rarity-5");
                                    $Eula_Teams3["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Eula_Teams3["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                    // キャラクター情報を出力
                                    foreach ($Eula_Teams3 as $name => $info):
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

                        <?php
                        // Ayakaの情報を格納する配列
                        $AyakaInfo = array(
                            "NormalAttack" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png",
                                "title" => "Normal Attack",
                                "name" => "Kamisato Art: Kabuki",
                                "description" => "Normal Attack Performs up to 5 rapid strikes. Charged Attack Consumes a certain amount of Stamina to unleash a continuous stream of sword ki. Plunging Attack lunges from mid-air to strike the ground below, damaging enemies along the path and dealing AoE DMG upon impact."
                            ),
                            "ElementalSkill" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/talent_2.png",
                                "title" => "Elemental Skill",
                                "name" => "Kamisato Art: Hyouka",
                                "description" => "Summons blooming ice to launch nearby opponents, dealing AoE Cryo DMG."
                            ),
                            "ElementalBurst" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/talent_3.png",
                                "title" => "Elemental Burst",
                                "name" => "Kamisato Art: Soumetsu",
                                "description" => "Summons forth a snowstorm with flawless poise, unleashing a Frostflake Seki no To that moves forward continuously. Frostflake Seki no To A storm of whirling icy winds that slashes repeatedly at every enemy it touches, dealing Cryo DMG The snowstorm explodes after its duration ends, dealing AoE Cryo DMG."
                            ),

                        );

                        // 各情報を出力
                        ?>



                        <?php
                        // AyatoPassivesの情報を格納する配列
                        $passivesInfo = array(
                            "Ascension1" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/talent_4.png",
                                "title" => "Ascension 1",
                                "name" => "Amatsumi Kunitsumi Sanctification",
                                "description" => "After using Kamisato Art: Hyouka, Kamisato Ayaka's Normal and Charged attacks deal 30% increased DMG for 6s."
                            ),
                            "Ascension4" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/talent_5.png",
                                "title" => "Ascension 4",
                                "name" => "Kanten Senmyou Blessing",
                                "description" => "When the Cryo application at the end of Kamisato Art: Senho hits an opponent, Kamisato Ayatogains the following effects: Restores 10 Stamina Gains 18% Cryo DMG Bonus for 10s."
                            ),
                            "Passive" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/talent_6.png",
                                "title" => "Passive",
                                "name" => "Fruits of Shinsa",
                                "description" => "When Ayatocrafts Weapon Ascension Materials, she has a 10% chance to receive double the product."
                            )
                        );

                        // AyatoConstellationsの情報を格納する配列
                        $constellationsInfo = array(
                            "Constellation1" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/constellation_1.png",
                                "title" => "Constellation 1",
                                "name" => "Snowswept Sakura",
                                "description" => "When Kamisato Ayaka's Normal or Charged Attacks deal Cryo DMG to opponents, it has a 50% chance of decreasing the CD of Kamisato Art: Hyouka by 0.3s. This effect can occur once every 0.1s."
                            ),
                            "Constellation2" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/constellation_2.png",
                                "title" => "Constellation 2",
                                "name" => "Blizzard Blade Seki no To",
                                "description" => "When casting Kamisato Art: Soumetsu, unleashes 2 smaller additional Frostflake Seki no To, each dealing 20% of the original storm's DMG."
                            ),
                            "Constellation3" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/constellation_3.png",
                                "title" => "Constellation 3",
                                "name" => "Frostbloom Kamifubuki",
                                "description" => "Increases the Level of Kamisato Art: Soumetsu by 3. Maximum upgrade level is 15."
                            ),
                            "Constellation4" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/constellation_4.png",
                                "title" => "Constellation 4",
                                "name" => "Ebb and Flow",
                                "description" => "Opponents damaged by Kamisato Art: Soumetsu's Frostflake Seki no To will have their DEF decreased by 30% for 6s."
                            ),
                            "Constellation5" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/constellation_5.png",
                                "title" => "Constellation 5",
                                "name" => "Blossom Cloud Irutsuki",
                                "description" => "Increase the Level of Kamisato Art: Hyouka by 3. Maximum upgrade level is 15."
                            ),
                            "Constellation6" => array(
                                "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/constellation_6.png",
                                "title" => "Constellation 6",
                                "name" => "Dance of Suigetsu",
                                "description" => "Kamisato Ayatogains Usurahi Butou every 10s, increasing her Charged Attack DMG by 298%. This buff will be cleared 0.5s after Ayaka's Charged ATK hits an opponent, after which the timer for this ability will restart."
                            )
                        );
                        ?>

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
                            <h2 class="character-category">Eula Talents</h2>
                            <?php                        // 配列でタレント情報を定義
                            $talents = array(
                                array(
                                    'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Eula/UI_GachaTypeIcon_Claymore.png',
                                    'title' => 'Normal Attack',
                                    'name' => 'Favonius Bladework - Edel',
                                    'description' => 'Normal Attack Performs up to five consecutive strikes. Charged Attack Drains Stamina over time to perform continuous slashes. At the end of the sequence, perform a more powerful slash. Plunging Attack Plunges from mid-air to strike the ground, damaging opponents along the path and dealing AoE DMG upon impact.'
                                ),
                                array(
                                    'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Eula/talent_2.png',
                                    'title' => 'Elemental Skill',
                                    'name' => 'White Clouds at Dawn',
                                    'description' => 'Eula enters the Cloud Transmogrification state, in which she will not take Fall DMG, and uses Skyladder once. In this state, her Plunging Attack will be converted into Driftcloud Wave instead, which deals AoE Anemo DMG and ends the Cloud Transmogrification state. This DMG is considered Plunging Attack DMG. Each use of Skyladder while in this state increases the DMG and AoE of the next Driftcloud Wave used. Skyladder Can be used while in mid-air. Eula leaps forward, dealing Anemo DMG to targets along her path. During each Cloud Transmogrification state Eula enters, Skyladder may be used up to 3 times and only 1 instance of Skyladder DMG can be dealt to any one opponent. If Skyladder is not used again in a short period, the Cloud Transmogrification state will be canceled. If Eula does not use Driftcloud Wave while in this state, the next CD of White Clouds at Dawn will be decreased by 3s.'
                                ),
                                array(
                                    'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Eula/talent_3.png',
                                    'title' => 'Elemental Burst',
                                    'name' => 'Glacial Illumination',
                                    'description' => 'Brandishes her greatsword, dealing<span class="cryo"> Cryo </span>DMG to nearby opponents and creating a Lightfall Sword that follows her around for a duration of up to 7s. While present, the Lightfall Sword increases Eula\'s resistance to interruption. When Eula\'s own Normal Attack, Elemental Skill, and Elemental Burst deal DMG to opponents, they will charge the Lightfall Sword, which can gain an energy stack once every 0.1s. Once its duration ends, the Lightfall Sword will descend and explode violently, dealing Physical DMG to nearby opponents. This DMG scales on the number of energy stacks the Lightfall Sword has accumulated. If Eula leaves the field, the Lightfall Sword will immediately explode.'
                                )

                            );

                            // タレント情報をループで表示
                            foreach ($talents as $talent):
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
                            <h2 class="character-category">Eula Passives</h2>
                            <?php
                            // 配列でパッシブ情報を定義
                            $passives = array(
                                array(
                                    'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Eula/talent_4.png',
                                    'title' => 'Ascension 1',
                                    'name' => 'Galefeather Pursuit',
                                    'description' => 'Each opponent hit by Driftcloud Waves from White Clouds at Dawn will grant all nearby party members 1 stack of Storm Pinion for 20s. Max 4 stacks. These will cause the characters\' Plunging Attack CRIT Rate to increase by 4%/6%/8%/10% respectively. Each Storm Pinion created by hitting an opponent has an independent duration.'
                                ),
                                array(
                                    'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Eula/talent_5.png',
                                    'title' => 'Ascension 4',
                                    'name' => 'Consider, the Adeptus in Her Realm',
                                    'description' => 'When the Starwicker created by Stars Gather at Dusk has Adeptal Assistance stacks, nearby active characters\' Plunging Attack shockwave DMG will be increased by 200% of Eula\'s ATK. The maximum DMG increase that can be achieved this way is 9,000. Each Plunging Attack shockwave DMG instance can only apply this increased DMG effect to a single opponent. Each character can trigger this effect once every 0.4s.'
                                ),
                                array(
                                    'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Eula/talent_6.png',
                                    'title' => 'Passive',
                                    'name' => 'Crane Form',
                                    'description' => 'Increases gliding SPD for your own party members by 15%. Not stackable with Passive Talents that provide the exact same effects.'
                                )
                            );

                            // パッシブ情報をループで表示
                            foreach ($passives as $passive):
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
                            <h2 class="character-category">Eula Constellations</h2>
                            <?php
                            $constellations = array(
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Eula/constellation_1.png',
                                    'title' => 'Constellation 1',
                                    'name' => 'Tidal Illusion',
                                    'description' => 'Every time Icetide Vortex\'s Grimheart stacks are consumed, Eula\'s Physical DMG is increased by 30% for 6s. Each stack consumed will increase the duration of this effect by 6s up to a maximum of 18s.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Eula/constellation_2.png',
                                    'title' => 'Constellation 2',
                                    'name' => 'Lady of Seafoam',
                                    'description' => 'Decreases the CD of Icetide Vortex\'s Holding Mode, rendering it identical to Tapping CD.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Eula/constellation_3.png',
                                    'title' => 'Constellation 3',
                                    'name' => 'Lawrence Pedigree',
                                    'description' => 'Increases the Level of Glacial Illumination by 3. Maximum upgrade level is 15.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Eula/constellation_4.png',
                                    'title' => 'Constellation 4',
                                    'name' => 'The Obstinacy of One\'s Inferiors',
                                    'description' => 'Lightfall Swords deal 25% increased DMG against opponents with less than 50% HP.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Eula/constellation_5.png',
                                    'title' => 'Constellation 5',
                                    'name' => 'Chivalric Quality',
                                    'description' => 'Increases the Level of Icetide Vortex by 3. Maximum upgrade level is 15.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Eula/constellation_6.png',
                                    'title' => 'Constellation 6',
                                    'name' => 'Noble Obligation',
                                    'description' => 'Lightfall Swords created by Glacial Illumination start with 5 stacks of energy. Normal Attacks, Elemental Skills, and Elemental Bursts have a 50% chance to grant the Lightning Sword an additional stack of energy.'
                                )
                            );

                            // コンステレーション情報をループで表示
                            foreach ($constellations as $constellation):
                                ?>
                                <div class="character-skill">
                                    <div class="character-skill-header">
                                        <img class="character-skill-icon"
                                            src="<?php echo esc_url($constellation['icon']); ?>"
                                            alt="<?php echo esc_attr($constellation['title']); ?>">
                                        <h2 class="character-skill-title"><?php echo esc_html($constellation['title']); ?>
                                        </h2>
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
                                            // アヤトの昇華アイテムの情報
                                            $ascensionItems = array(
                                                array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Vayuda Turquoise Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Clearwater Jade", "Count3" => "3", "Material4" => "Divining Scroll", "Count4" => "3"),
                                                array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Cloudseam Scale", "Count1" => "2", "Material2" => "Vayuda Turquoise Fragment", "Count2" => "3", "Material3" => "Clearwater Jade", "Count3" => "10", "Material4" => "Divining Scroll", "Count4" => "15"),
                                                array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Cloudseam Scale", "Count1" => "4", "Material2" => "Vayuda Turquoise Fragment", "Count2" => "6", "Material3" => "Clearwater Jade", "Count3" => "20", "Material4" => "Sealed Scroll", "Count4" => "12"),
                                                array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Cloudseam Scale", "Count1" => "8", "Material2" => "Vayuda Turquoise Chunk", "Count2" => "3", "Material3" => "Clearwater Jade", "Count3" => "30", "Material4" => "Sealed Scroll", "Count4" => "18"),
                                                array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Cloudseam Scale", "Count1" => "12", "Material2" => "Vayuda Turquoise Chunk", "Count2" => "6", "Material3" => "Clearwater Jade", "Count3" => "45", "Material4" => "Forbidden Curse Scroll", "Count4" => "12"),
                                                array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Cloudseam Scale", "Count1" => "20", "Material2" => "Vayuda Turquoise Gemstone", "Count2" => "6", "Material3" => "Clearwater Jade", "Count3" => "60", "Material4" => "Forbidden Curse Scroll", "Count4" => "24")
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
                                                            $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/xianyun/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
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
                                        <lite-youtube videoid="6L41Yfo0450" params="rel=0"></lite-youtube>
                                    </div>


                                    <!--character end-->
                                    <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>