<?php
/**
 * Template Name: xianyun-build.php
 * Description: Template for displaying Genshin Impact character builds Xianyun.
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
<div>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9458790149381361"
        crossorigin="anonymous"></script>
    <!-- ディスプレイ広告 縦長 レスポンシブル -->
    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-9458790149381361" data-ad-slot="1273600322"
        data-ad-format="auto" data-full-width-responsive="true"></ins>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>
<div class="row">
    <main class="content" style="background: #1c1f46;">
        <div class="character">
            <!-- Character Intro Section -->
            <div class="character-intro">
                <!-- Character Image -->
                <?php
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Xianyun.png';
                $image_alt = 'Xianyun';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Xianyun Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_anemo.png"
                            alt="Hydro">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_sword.png"
                            alt="Catalyst">Catalyst </div>
                    <div class="character-role">Support</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

                    $materials = array(
                        'Gold' => 'Golg',
                        'Vayuda_Turquoise_Sliver' => 'Vayuda_Turquoise_Sliver',
                        'Vayuda Turquoise Sliver' => 'Vayuda Turquoise Sliver',
                        'Cloudseam_Scale' => 'Cloudseam Scale',
                        'Divining Scroll' => 'Divining Scroll',
                        'Lightless Eye of the Maelstrom' => 'Lightless Eye of the Maelstrom',
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
                        <!-- Ayato Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Xianyun Best Weapons</h2>
                            <div class="character-build-weapons">
                                <?php
                                $weapons = array(
                                    array("Crane's Echoing Call", 1),
                                    array('Oathsworn Eye', 2),
                                    array('Thrilling Tales of Dragon Slayers', 3),
                                    array('Favonius Codex', 4),
                                    array('Skyward Atlas', 5)
                                );

                                foreach ($weapons as $weapon):
                                    $weaponName = $weapon[0];
                                    $weaponRank = $weapon[1];
                                    $encodedWeaponName = urlencode(str_replace([' ', '_'], '_', $weaponName));
                                    $imageUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/{$encodedWeaponName}.png";
                                    ?>
                                    <div class="character-build-weapon">
                                        <div class="character-build-weapon-rank"><?php echo esc_html($weaponRank); ?></div>
                                        <img class="character-build-weapon-icon rarity-<?php echo esc_html($weaponRank); ?>"
                                            src="<?php echo esc_url($imageUrl); ?>"
                                            alt="<?php echo esc_attr($weaponName); ?>">
                                        <div class="character-build-weapon-name">
                                            <?php echo esc_html($weaponName); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Xianyun Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Xianyun Best Artifacts</h2>
                            <?php
                            $artifacts = array(
                                array('Viridescent Venerer', 1),
                                array('Noblesse Oblige', 2),
                                array('Song of Days Past', 3),
                                array('Ocean-Hued Clam', 4),
                                array("emblem of severed fate", 5),
                                array("shimenawas reminiscence", 5),
                            );

                            $manualRanks = array(4, 4, 4, 4, 2, 2);
                            $isRepeating = false;

                            foreach ($artifacts as $index => $artifact):
                                $artifactName = $artifact[0];
                                $artifactRank = $artifact[1];

                                // アーティファクトの画像URLを動的に生成（大文字小文字を区別せず）
                                $imageUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/" . str_replace(' ', '_', strtolower($artifactName)) . ".png";

                                // $manualRanks 配列の要素が2で繰り返される場合の処理
                                if ($manualRanks[$index] == 2) {
                                    if (!$isRepeating) {
                                        echo '<div class="character-build-weapon">';
                                        $isRepeating = true;
                                    }

                                    echo '<div class="character-build-weapon-rank">' . esc_html($artifactRank) . '</div>';
                                    echo '<div class="character-build-weapon-content ' . (($artifactRank === 4) ? 'full' : '') . '">';
                                    echo '<img class="character-build-weapon-icon rarity-5" src="' . esc_url($imageUrl) . '" alt="' . esc_attr($artifactName) . '">';
                                    echo '<div class="character-build-weapon-name">' . esc_html(ucwords($artifactName)) . '</div>';
                                    echo '<div class="character-build-weapon-count">' . esc_html($manualRanks[$index]) . '</div>';
                                    echo '</div>';

                                    // もし $index が $artifacts 配列の最後の要素のインデックスであるか、または次の要素のランクが2でない場合
                                    if ($index === count($artifacts) - 1 || $manualRanks[$index + 1] != 2) {
                                        echo '</div>';
                                        $isRepeating = false;
                                    }
                                } else {
                                    echo '<div class="character-build-weapon">';
                                    echo '<div class="character-build-weapon-rank">' . esc_html($artifactRank) . '</div>';
                                    echo '<div class="character-build-weapon-content ' . (($artifactRank === 4) ? 'full' : '') . '">';
                                    echo '<img class="character-build-weapon-icon rarity-5" src="' . esc_url($imageUrl) . '" alt="' . esc_attr($artifactName) . '">';
                                    echo '<div class="character-build-weapon-name">' . esc_html(ucwords($artifactName)) . '</div>';
                                    echo '<div class="character-build-weapon-count">' . esc_html($manualRanks[$index]) . '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            endforeach;
                            ?>
                        </div>
                    </div>



                    <!-- Xianyun Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Xianyun Best Stats</h2>
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
                            <!-- ディスプレイ広告　横長レスポンシブル -->
                            <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-9458790149381361"
                                data-ad-slot="3245157546" data-ad-format="auto" data-full-width-responsive="true"></ins>
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
                        <h2 class="character-category">Best Xianyun Teams</h2>
                        <!-- AyatoFreeze Team -->
                        <div class="character-team">
                            <div class="character-team-name">AyatoFreeze
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Xianyun_Teams1["Xianyun"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Xianyun_Teams1["Faruzan"] = array("element" => "anemo", "rarity" => "rarity-4");
                                    $Xianyun_Teams1["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Xianyun_Teams1["Xiao"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Xianyun_Teams1 as $name => $info):
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
                                    $Xianyun_Teams2["Xianyun"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Xianyun_Teams2["Diluc"] = array("element" => "pyro", "rarity" => "rarity-5");
                                    $Xianyun_Teams2["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Xianyun_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                    // キャラクター情報を出力
                                    foreach ($Xianyun_Teams2 as $name => $info):
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
                                    $Xianyun_Teams3["Xianyun"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Xianyun_Teams3["Diluc"] = array("element" => "pyro", "rarity" => "rarity-5");
                                    $Xianyun_Teams3["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Xianyun_Teams3["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                    // キャラクター情報を出力
                                    foreach ($Xianyun_Teams3 as $name => $info):
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
                            <h2 class="character-category">Xianyun Talents</h2>
                            <?php
                            // 配列でタレント情報を定義
                            $talents = array(
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/UI_GachaTypeIcon_Catalyst.png',
                                    'title' => 'Normal Attack',
                                    'name' => 'Word of Wind and Flower',
                                    'description' => 'Normal Attack Summons swirling winds to perform up to 4 attacks, dealing Anemo DMG. Charged Attack Consumes a certain amount of Stamina and launches a Breeze Bolt in a straight line that deals Anemo DMG to opponents along its path. Plunging Attack Gathers the power of Anemo and plunges towards the ground from mid-air, damaging all opponents in her path. Deals AoE Anemo DMG upon impact with the ground.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/talent_2.png',
                                    'title' => 'Elemental Skill',
                                    'name' => 'White Clouds at Dawn',
                                    'description' => 'Xianyun enters the Cloud Transmogrification state, in which she will not take Fall DMG, and uses Skyladder once. In this state, her Plunging Attack will be converted into Driftcloud Wave instead, which deals AoE Anemo DMG and ends the Cloud Transmogrification state. This DMG is considered Plunging Attack DMG. Each use of Skyladder while in this state increases the DMG and AoE of the next Driftcloud Wave used. Skyladder Can be used while in mid-air. Xianyun leaps forward, dealing Anemo DMG to targets along her path. During each Cloud Transmogrification state Xianyun enters, Skyladder may be used up to 3 times and only 1 instance of Skyladder DMG can be dealt to any one opponent. If Skyladder is not used again in a short period, the Cloud Transmogrification state will be canceled. If Xianyun does not use Driftcloud Wave while in this state, the next CD of White Clouds at Dawn will be decreased by 3s.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/talent_3.png',
                                    'title' => 'Elemental Burst',
                                    'name' => 'Stars Gather at Dusk',
                                    'description' => 'Brings forth a sacred breeze that deals AoE Anemo DMG and heals all nearby characters based on Xianyun\'s ATK. It will also summon the "Starwicker" mechanism. Starwicker Continuously follows the active character and periodically heals all nearby party members based on Xianyun\'s ATK. Starts with 8 stacks of Adeptal Assistance. While Adeptal Assistance is active, nearby active characters in the party will have their jump height increased. When the active character completes a Plunging Attack, Starwicker will consume 1 stack of Adeptal Assistance and deal AoE Anemo DMG. Only 1 Starwicker can exist simultaneously.'
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
                            <h2 class="character-category">Xianyun Passives</h2>
                            <?php
                            // 配列でパッシブ情報を定義
                            $passives = array(
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/talent_4.png',
                                    'title' => 'Ascension 1',
                                    'name' => 'Galefeather Pursuit',
                                    'description' => 'Each opponent hit by Driftcloud Waves from White Clouds at Dawn will grant all nearby party members 1 stack of Storm Pinion for 20s. Max 4 stacks. These will cause the characters\' Plunging Attack CRIT Rate to increase by 4%/6%/8%/10% respectively. Each Storm Pinion created by hitting an opponent has an independent duration.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/talent_5.png',
                                    'title' => 'Ascension 4',
                                    'name' => 'Consider, the Adeptus in Her Realm',
                                    'description' => 'When the Starwicker created by Stars Gather at Dusk has Adeptal Assistance stacks, nearby active characters\' Plunging Attack shockwave DMG will be increased by 200% of Xianyun\'s ATK. The maximum DMG increase that can be achieved this way is 9,000. Each Plunging Attack shockwave DMG instance can only apply this increased DMG effect to a single opponent. Each character can trigger this effect once every 0.4s.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/talent_6.png',
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
                            <h2 class="character-category">Xianyun Constellations</h2>
                            <?php
                            // 配列でコンステレーション情報を定義
                            $constellations = array(
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/constellation_1.png',
                                    'title' => 'Constellation 1',
                                    'name' => 'Purifying Wind',
                                    'description' => 'White Clouds at Dawn gains 1 additional charge.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/constellation_2.png',
                                    'title' => 'Constellation 2',
                                    'name' => 'Aloof From the World',
                                    'description' => 'After using a Skyladder from White Clouds at Dawn, Xianyun\'s ATK will be increased by 20% for 15s. Additionally, the effects of the Passive Talent "Consider, the Adeptus in Her Realm" will be enhanced: When the Starwicker created by Stars Gather at Dusk has Adeptal Assistance stacks, nearby active characters\' Plunging Attack shockwave DMG will be increased by 400% of Xianyun\'s ATK. The maximum DMG increase that can be achieved this way is 18,000. Each Plunging Attack shockwave DMG instance can only apply this increased DMG effect to a single opponent. Each character can trigger this effect once every 0.4s. You must first unlock the Passive Talent "Consider, the Adeptus in Her Realm."'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/constellation_3.png',
                                    'title' => 'Constellation 3',
                                    'name' => 'Creations of Star and Moon',
                                    'description' => 'Increases the Level of Stars Gather at Dusk by 3. Maximum upgrade level is 15.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/constellation_4.png',
                                    'title' => 'Constellation 4',
                                    'name' => 'Mystery Millet Gourmet',
                                    'description' => 'After using Skyladder 1/2/3 times during one White Clouds at Dawn Cloud Transmogrification state, when a Driftcloud Wave unleashed during that instance hits an opponent, it will heal all nearby party members for 50%/80%/150% of Xianyun\'s ATK. This effect can be triggered once every 5s.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/constellation_5.png',
                                    'title' => 'Constellation 5',
                                    'name' => 'Astride Rose-Colored Clouds',
                                    'description' => 'Increases the Level of White Clouds at Dawn by 3. Maximum upgrade level is 15.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Xianyun/constellation_6.png',
                                    'title' => 'Constellation 6',
                                    'name' => 'They Call Her Cloud Retainer',
                                    'description' => 'After Xianyun uses 1/2/3 Skyladders within one Cloud Transmogrification caused by White Clouds at Dawn, the CRIT DMG of a Driftcloud Wave created in this instance of Cloud Transmogrification will be increased by 15%/35%/70%. Within 16s after Xianyun has used Stars Gather at Dusk, White Clouds at Dawn will not enter CD. This effect will be canceled once she has used White Clouds at Dawn 8 times.'
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
                                    <lite-youtube videoid="Hl_jEpTpVdM" params="rel=0"></lite-youtube>
                                </div>


                                <!--character end-->
                                <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>