<?php
/**
 * Template Name: fuirna-build.php
 * Description: Template for displaying Genshin Impact character builds Furina.
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Furina.png';
                $image_alt = 'Furina';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Furina Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_hydro.png"
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
                        'Justice' => 'Justice',
                        'Varunada Lazurite Sliver' => 'Varunada Lazurite Sliver',
                        'Vayuda Turquoise Sliver' => 'Vayuda Turquoise Sliver',
                        'Whopperflower Nectar' => 'Whopperflower Nectar',
                        'Water That Failed To Transcend' => 'Water That Failed To Transcend',
                        'Lightless Mass' => 'Lightless Mass',
                    );

                    foreach ($materials as $image_filename => $material_name):
                        // 各素材の名前の最初の文字を大文字に変換し、アンダースコアをスペースに置換
                        $formatted_material_name = ucwords(str_replace('_', ' ', $material_name));

                        // ファイルパスを生成
                        $image_file_path = $image_base_path . str_replace(' ', '_', $image_filename) . '.png';
                        ?>
                        <!-- 各素材のアイコンと名前を表示 -->
                        <div class="character-materials-item">
                            <img class="character-materials-icon" src="<?php echo esc_url($image_file_path); ?>"
                                alt="<?php echo esc_attr($formatted_material_name); ?>">
                            <p><?php echo esc_html($formatted_material_name); ?></p>
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


                    <!-- Character Build Section -->

                    <div class="character-build">
                        <!-- Ayato Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Furina Best Weapons</h2>
                            <div class="character-build-weapons">
                                <?php
                                $weapons = array(
                                    // 武器名、ランク、レアリティ
                                    array("Splendor of Tranquil Waters", 1, 5),
                                    array('Primordial Jade Cutter', 2, 5),
                                    array('Festering Desire', 3, 4),
                                    array('Key of Khaj-Nisut', 4, 5),
                                    array('Favonius Sword', 4, 5),
                                );

                                foreach ($weapons as $weapon):
                                    // 配列から武器情報を取得
                                    $weaponName = $weapon[0];
                                    $weaponRank = $weapon[1];
                                    $weaponRarity = $weapon[2];

                                    // 手動で設定したクラスを追加
                                    $additionalClasses = ' rarity-' . esc_html($weaponRarity);

                                    // 武器名からURLエンコードして画像URLを生成
                                    $encodedWeaponName = urlencode(str_replace([' ', '_'], '_', $weaponName));
                                    $imageUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/furina/{$encodedWeaponName}.png";
                                    ?>
                                    <!-- 武器情報を表示するブロック -->
                                    <div class="character-build-weapon">
                                        <!-- 武器ランクを表示 -->
                                        <div class="character-build-weapon-rank"><?php echo esc_html($weaponRank); ?></div>
                                        <!-- 武器アイコンを表示（クラスに手動で追加したレアリティ情報も含む）-->
                                        <img class="character-build-weapon-icon<?php echo $additionalClasses; ?>"
                                            src="<?php echo esc_url($imageUrl); ?>"
                                            alt="<?php echo esc_attr($weaponName); ?>">
                                        <!-- 武器名を表示 -->
                                        <div class="character-build-weapon-name">
                                            <?php echo esc_html($weaponName); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Furina Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Furina Best Artifacts</h2>
                            <?php
                            $artifacts = array(
                                array('Golden Troupe', 1),
                                array('Tenacity of the Millelith', 2),
                                array('Golden Troupe', 3),
                                array("Tenacity of the Millelith", 3),
                                array("Golden Troupe", 4),
                                array("Heart of Depth", 4),
                                array("Heart of Depth", 5),
                                array("Tenacity of the Millelith", 5),
                            );

                            $manualRanks = array(4, 4, 2, 2, 2, 2, 2, 2);

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



                    <!-- Furina Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Furina Best Stats</h2>
                        <div class="character-stats-item"><b>Sands:</b> Energy Recharge</div>
                        <div class="character-stats-item"><b>Goblet:</b> Hydro DMG</div>
                        <div class="character-stats-item"><b>Circlet:</b> CRIT Rate / CRIT DMG</div>
                        <div class="character-stats-item full"><b>Substats:</b> Energy Recharge &gt; CRIT Rate / CRIT
                            DMG &gt; HP%</div>
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
                        <h2 class="character-category">Best Furina Teams</h2>
                        <!-- AyatoFreeze Team -->
                        <div class="character-team">
                            <div class="character-team-name">AyatoFreeze
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $Furina_Teams1["Furina"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Furina_Teams1["Faruzan"] = array("element" => "anemo", "rarity" => "rarity-4");
                                    $Furina_Teams1["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Furina_Teams1["Xiao"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($Furina_Teams1 as $name => $info):
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
                                    $Furina_Teams2["Furina"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Furina_Teams2["Diluc"] = array("element" => "pyro", "rarity" => "rarity-5");
                                    $Furina_Teams2["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Furina_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                    // キャラクター情報を出力
                                    foreach ($Furina_Teams2 as $name => $info):
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
                                    $Furina_Teams3["Furina"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    $Furina_Teams3["Diluc"] = array("element" => "pyro", "rarity" => "rarity-5");
                                    $Furina_Teams3["Furina"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $Furina_Teams3["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                    // キャラクター情報を出力
                                    foreach ($Furina_Teams3 as $name => $info):
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
                            <h2 class="character-category">Furina Talents</h2>
                            <?php
                            // Furinaのタレント情報を定義
                            $talents = array(
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/UI_GachaTypeIcon_Sword.png',
                                    'title' => 'Normal Attack',
                                    'name' => "Soloist's Solicitation",
                                    'description' => 'Normal Attack Performs up to 4 consecutive strikes. Charged Attack Consumes a certain amount of Stamina to unleash a solo dance, dealing Physical DMG to nearby opponents and changing her Arkhe alignment. If Salon Members or Singer of Many Waters summoned by her Elemental Skill "Salon Solitaire" are present, their lineup will switch in response. Arkhe: Seats Sacred and Secular At intervals, when Furina\'s Normal Attacks hit, a Spiritbreath Thorn or a Surging Blade will descend based on her current alignment, dealing Hydro DMG based on her current alignment. When Furina takes the field, her starting Arkhe will be Ousia. Plunging Attack Plunges from mid-air to strike the ground below, damaging opponents along the path and dealing AoE DMG upon impact.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/talent_2.png',
                                    'title' => 'Elemental Skill',
                                    'name' => 'Salon Solitaire',
                                    'description' => 'Invites the guests of the Salon Solitaire to come forth and abet in Furina\'s performance. Will summon either the Salon Members or the Singer of Many Waters based on Furina\'s current Arkhe alignment. Ousia Foaming bubbles like celebrants shall dance, dealing AoE Hydro DMG based on Furina\'s Max HP and summoning 3 Salon Members: the Ball Octopus-shaped Gentilhomme Usher, the Bubbly Seahorse-shaped Surintendante Chevalmarin, and the Armored Crab-shaped Mademoiselle Crabaletta. They will attack nearby opponents at intervals, prioritizing the target of the active character, dealing Hydro DMG based on Max HP. When they attack, if character(s) with more than 50% HP are nearby, the Members will increase their current attack\'s power based on the number of such characters, and consume said characters\' HP. If the characters who meet these requirements are 1/2/3/4 (or more), the Members\' attacks will deal 110%/120%/130%/140% of their original DMG. Pneuma Summons the Singer of Many Waters, who will heal nearby active character(s) based on Max HP at intervals. The Salon Members and Singer of Many Waters share a duration, and when Furina uses her Charged Attack to change the guest type, the new guests will inherit the initial duration. While the Salon Members and the Singer of Many Waters are on the field, Furina can move on the water\'s surface.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/talent_3.png',
                                    'title' => 'Elemental Burst',
                                    'name' => 'Let the People Rejoice',
                                    'description' => 'Rouses the impulse to revel, creating a stage of foam that will deal AoE Hydro DMG based on Furina\'s Max HP and cause nearby party members to enter the Universal Revelry state: During this time, when nearby party members\' HP increases or decreases, 1 Fanfare point will be granted to Furina for each percentage point of their Max HP by which their HP changes. At the same time, Furina will increase the DMG dealt by and Incoming Healing Bonus of all nearby party members based on the amount of Fanfare she has. When the duration ends, Furina\'s Fanfare points will be cleared.'
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
                            <h2 class="character-category">Furina Passives</h2>
                            <?php
                            // 配列でパッシブ情報を定義
                            $passives = array(
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/talent_4.png',
                                    'title' => 'Ascension 1',
                                    'name' => 'Endless Waltz',
                                    'description' => 'When the active character in your party receives healing, if the source of the healing is not Furina herself and the healing overflows, then Furina will heal a nearby party member for 2% of their Max HP once every 2s within the next 4s.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/talent_5.png',
                                    'title' => 'Ascension 4',
                                    'name' => 'Unheard Confession',
                                    'description' => 'Every 1,000 points of Furina\'s Max HP can buff the different Arkhe-aligned Salon Solitaire in the following ways: Will increase Salon Member DMG dealt by 0.7%, up to a maximum of 28%. Will decrease active character healing interval of the Singer of Many Waters by 0.4%, up to a maximum of 16%.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/talent_6.png',
                                    'title' => 'Passive',
                                    'name' => 'The Sea Is My Stage',
                                    'description' => 'Xenochromatic Fontemer Aberrant ability CD decreased by 30%.'
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
                            <h2 class="character-category">Furina Constellations</h2>
                            <?php
                            // 配列でコンステレーション情報を定義
                            $constellations = array(
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/constellation_1.png',
                                    'title' => 'Constellation 1',
                                    'name' => '"Love Is a Rebellious Bird That None Can Tame"',
                                    'description' => 'When using Let the People Rejoice, Furina will gain 150 Fanfare. Additionally, Furina\'s Fanfare limit is increased by 100.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/constellation_2.png',
                                    'title' => 'Constellation 2',
                                    'name' => '"A Woman Adapts Like Duckweed in Water"',
                                    'description' => 'While Let the People Rejoice lasts, Furina\'s Fanfare gain from increases or decreases in nearby characters\' HP is increased by 250%. Each point of Fanfare above the limit will increase Furina\'s Max HP by 0.35%. Her maximum Max HP increase is 140%.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/constellation_3.png',
                                    'title' => 'Constellation 3',
                                    'name' => '"My Secret Is Hidden Within Me, No One Will Know My Name"',
                                    'description' => 'Increases the Level of Let the People Rejoice by 3. Maximum upgrade level is 15.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/constellation_4.png',
                                    'title' => 'Constellation 4',
                                    'name' => '"They Know Not Life, Who Dwelt in the Netherworld Not!"',
                                    'description' => 'When the Salon Members from Salon Solitaire hit an opponent, or the Singer of Many Waters restores HP to the active character, Furina will restore 4 Energy. This effect triggers once every 5s.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/constellation_5.png',
                                    'title' => 'Constellation 5',
                                    'name' => '"His Name I Now Know, It Is...!"',
                                    'description' => 'Increases the Level of Salon Solitaire by 3. Maximum upgrade level is 15.'
                                ),
                                array(
                                    'icon' => 'https://rerollcdn.com/GENSHIN/Skill/1/Furina/constellation_6.png',
                                    'title' => 'Constellation 6',
                                    'name' => '"Hear Me — Let Us Raise the Chalice of Love!"',
                                    'description' => 'When using Salon Solitaire, Furina gains "Center of Attention" for 10s. Throughout the duration, Furina\'s Normal Attacks, Charged Attacks, and Plunging Attacks are converted into Hydro DMG which cannot be overridden by any other elemental infusion. DMG is also increased by an amount equivalent to 18% of Furina\'s max HP. Throughout the duration, Furina\'s Normal Attacks (not including Arkhe: Seats Sacred and Secular Attacks), Charged Attacks, and the impact of Plunging Attacks will cause different effects up to every 0.1s after hitting opponents depending on her current Arkhe alignment:<br>Arkhe: Ousia Every 1s, all nearby characters in the party will be healed by 4% of Furina\'s max HP, for a duration of 2.9s. Triggering this effect again will extend its duration. Arkhe: Pneuma This Normal Attack (not including Arkhe: Seats Sacred and Secular Attacks), Charged Attack, or Plunging Attack ground impact DMG will be further increased by an amount equivalent to 25% of Furina\'s max HP. When any of the attacks mentioned previously hit an opponent, all nearby characters in the party will consume 1% of their current HP. During the duration of each instance of "Center of Attention," the above effects can be triggered up to 6 times. "Center of Attention" will end when its effects have triggered 6 times or when the duration expires.'
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
                            <h2 class="character-category">Furina Ascension Costs</h2>
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
                                        // Furinaの昇華アイテムの情報
                                        $ascensionItems = array(
                                            array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Varunada Lazurite Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Lakelight Lily", "Count3" => "3", "Material4" => "Whopperflower Nectar", "Count4" => "3"),
                                            array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Varunada Lazurite Fragment", "Count1" => "2", "Material2" => "Water That Failed To Transcend", "Count2" => "3", "Material3" => "Lakelight Lily", "Count3" => "10", "Material4" => "Whopperflower Nectar", "Count4" => "15"),
                                            array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Varunada Lazurite Fragment", "Count1" => "4", "Material2" => "Water That Failed To Transcend", "Count2" => "6", "Material3" => "Lakelight Lily", "Count3" => "20", "Material4" => "Shimmering Nectar", "Count4" => "12"),
                                            array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Varunada Lazurite Chunk", "Count1" => "8", "Material2" => "Water That Failed To Transcend", "Count2" => "3", "Material3" => "Lakelight Lily", "Count3" => "30", "Material4" => "Shimmering Nectar", "Count4" => "18"),
                                            array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Varunada Lazurite Chunk", "Count1" => "12", "Material2" => "Water That Failed To Transcend", "Count2" => "6", "Material3" => "Lakelight Lily", "Count3" => "45", "Material4" => "Energy Nectar", "Count4" => "12"),
                                            array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Varunada Lazurite Gemstone", "Count1" => "6", "Material2" => "Water That Failed To Transcend", "Count2" => "20", "Material3" => "Lakelight Lily", "Count3" => "60", "Material4" => "Energy Nectar", "Count4" => "24")
                                            // 他の昇華ランクも同様に追加
                                        );

                                        // 各昇華アイテムごとに処理
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
                                                        $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/furina/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
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
                                                                <?php echo $material; // アイテムごとにそのまま表示 ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Table Data end-->




                    </div>
                    <!--character team-->
                    <h2 class="character-category">AyatoShowcase</h2>

                    <div class="character-showcase" id="showcase">
                        <lite-youtube videoid="Ud6R9ziMNW8" params="rel=0"></lite-youtube>
                    </div>


                    <!--character end-->
                    <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>