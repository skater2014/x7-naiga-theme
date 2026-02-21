<?php
/**
 * Template Name: Miko-build.php
 * Description: Template for displaying Genshin Impact character builds Miko.
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Yae Miko.png';
                $image_alt = 'Nahida';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Yae Miko Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/element_electro.png"
                            alt="Electro">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_catalyst.png"
                            alt="Catalyst">Catalyst</div>
                    <div class="character-role">Sub DPS</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/farming/';

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
                        <!-- Yae Miko Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Yae Miko Best Weapons</h2>
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


                        <!-- Yae Miko Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Yae Miko Best Artifacts</h2>

                            <?php
                            // アーティファクト ランク
                            // Yae Mikoの最適なアーティファクトの情報
                            $artifacts = array(
                                array("Deepwood Memories", 1, 4),
                                array("Gilded Dreams", 1, 4),
                                array("Flower of Paradise Lost", 2, 2),
                                array("Gilded Dreams", 2, 2),
                                array("Gilded Dreams", 3, 3),
                                array("Wanderer's Troupe", 3, 3),
                                array("Emblem of Severed Fate", 4, 4),
                                array("Gilded Dreams", 4, 4)
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
                        <h2 class="character-stats-title">Yae Miko Best Stats</h2>
                        <div class="character-stats-item"><b>Sands:</b> Energy Recharge / ATK%</div>
                        <div class="character-stats-item"><b>Goblet:</b> Electro DMG</div>
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
                        <h2 class="character-category">Best Yae Miko Teams</h2>
                        <!-- AyatoFreeze Team -->
                        <div class="character-team">
                            <div class="character-team-name">Kokomi Electro-Charged
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $YaeMiko_Teams1["Yae Miko"] = array("element" => "electro", "rarity" => "rarity-5");
                                    $YaeMiko_Teams1["Kokomi"] = array("element" => "hydro", "rarity" => "rarity-5");
                                    $YaeMiko_Teams1["Fischl"] = array("element" => "electro", "rarity" => "rarity-4");
                                    $YaeMiko_Teams1["Kazuha"] = array("element" => "anemo", "rarity" => "rarity-5");
                                    // キャラクター情報を出力
                                    foreach ($YaeMiko_Teams1 as $name => $info):
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
                                    // 欲しいキャラクターの情報だけを取得
                                    $YaeMiko_Teams2["Yae Miko"] = array("element" => "electro", "rarity" => "rarity-5");
                                    $YaeMiko_Teams2["Alhaitham"] = array("element" => "dendro", "rarity" => "rarity-5");
                                    $YaeMiko_Teams2["Kuki Shinobu"] = array("element" => "electro", "rarity" => "rarity-4");
                                    $YaeMiko_Teams2["Nahida"] = array("element" => "dendro", "rarity" => "rarity-5");

                                    // キャラクター情報を出力  検証した結果でどちらかを統一しないといけない。名前_.png 名前 .png　今回は名前 .png使う。
                                    foreach ($YaeMiko_Teams2 as $name => $info) {
                                        // スペースをアンダースコアに変換
                                        $imageNameWithUnderscore = str_replace(' ', '_', $name);
                                        // URLエンコード
                                        $encodedImageName = rawurlencode($imageNameWithUnderscore);

                                        // 画像のファイル名にアンダースコアが含まれていない場合は、元の名前を使用
                                        $imageNameForURL = file_exists("https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/{$imageNameWithUnderscore}.png") ? $imageNameWithUnderscore : $name;

                                        // URL生成
                                        $imageUrl = get_template_directory_uri() . "/images/genshin/" . str_replace(' ', '%20', rawurlencode($imageNameForURL)) . ".png";
                                        ?>
                                        <!-- 以下、$imageUrl を使って画像を表示するコードを追加してください -->
                                        <div class="character-portrait">
                                            <a
                                                href="https://kaztokyo.sakura.ne.jp/genshin-impact-blog-<?php echo strtolower($name); ?>-best-build">
                                                <img class="character-icon <?php echo $info['rarity']; ?>"
                                                    src="<?php echo $imageUrl; ?>" width="70px" height="70px"
                                                    alt="<?php echo $name; ?>">
                                                <img class="character-type"
                                                    src="<?php echo get_template_directory_uri(); ?>/images/genshin/element_<?php echo $info['element']; ?>.png"
                                                    width="24px" height="24px" alt="<?php echo $info['element']; ?>">
                                                <div class="character-name"><?php echo $name; ?></div>
                                            </a>
                                        </div>
                                    <?php } // foreach ループの終了位置 ?>
                                    <!-- Add character information as needed -->
                                </div>
                            </div>
                        </div>
                        <!-- Ayaka/Ganyu Furina Hydro Team -->

                        <div class="character-team">
                            <div class="character-team-name">Raiden Overload
                                <div class="character-team-characters">
                                    <?php
                                    // 既存のキャラクター情報を取得
                                    //$characters = get_genshin_characters();
                                    
                                    // 欲しいキャラクターの情報だけを取得
                                    $YaeMiko_Teams3["Yae Miko"] = array("element" => "electro", "rarity" => "rarity-5");
                                    $YaeMiko_Teams3["Raiden"] = array("element" => "electro", "rarity" => "rarity-5");
                                    $YaeMiko_Teams3["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                    $YaeMiko_Teams3["Faruzan"] = array("element" => "anemo", "rarity" => "rarity-4");

                                    // キャラクター情報を出力
                                    foreach ($YaeMiko_Teams3 as $name => $info):
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
                            <h2 class="character-category">Yae Miko Talents</h2>
                            <?php
                            $talents = array(
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Catalyst.png',
                                    "title" => "Normal Attack",
                                    "name" => "Spiritfox Sin-Eater",
                                    "description" => "Normal Attack
                                              Summons forth kitsune spirits, initiating a maximum of 3 attacks that deal Electro DMG.

                                              Charged Attack
                                              Consumes a certain amount of Stamina to deal Electro DMG after a short casting time.

                                              Plunging Attack
                                              Gathering the might of Electro, Yae Miko plunges towards the ground from mid-air, damaging all opponents in her path. Deals AoE Electro DMG upon impact with the ground.."
                                ),
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/talent_2.png',
                                    "title" => "Elemental Skill",
                                    "name" => "Yakan Evocation: Sesshou Sakura",
                                    "description" => "Moves swiftly, leaving a Sesshou Sakura behind. Sesshou Sakura Periodically strikes one nearby opponent with lightning, dealing Electro DMG. When there are other Sesshou Sakura nearby, their level will increase, boosting the DMG dealt by these lightning strikes. This skill has three charges. A maximum of 3 Sesshou Sakura can exist simultaneously. The initial level of each Sesshou Sakura is 1, and the initial highest level each sakura can reach is 3. If a new Sesshou Sakura is created too close to an existing one, the existing one will be destroyed."
                                ),
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/talent_3.png',
                                    "title" => "Elemental Burst",
                                    "name" => "Great Secret Art: Tenko Kenshin",
                                    "description" => "Summons a lightning strike, dealing AoE<span class=\"electro\"> Electro </span>DMG. When she uses this skill, Yae Miko will unseal nearby Sesshou Sakura, destroying their outer forms and transforming them into Tenko Thunderbolts that descend from the skies, dealing Electro DMG. Each Sesshou Sakura destroyed in this way will create one Tenko Thunderbolt."
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
                            <h2 class="character-category">Yae Miko Passives</h2>
                            <?php
                            $passives = array(
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/talent_4.png',
                                    "title" => "Ascension 1",
                                    "name" => "Meditations of a Yako",
                                    "description" => "When she crafts Character Talent Material she has a set chance to create an extra Talent Material from the same region of a random type. The rarity of this material will be the same materials consumed during crafting."
                                ),
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/talent_5.png',
                                    "title" => "Ascension 4",
                                    "name" => "Enlightened Blessing",
                                    "description" => "Yae Miko's Elemental Mastery will increase the DMG dealt by the Sesshou Sakura."
                                ),
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/talent_6.png',
                                    "title" => "Passive",
                                    "name" => "The Shrine's Sacred Shade",
                                    "description" => "When Yae Miko uses her Elemental Burst, each Sesshou Sakura destroyed resets the cooldown for one charge of her Elemental Skill."
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
                            <h2 class="character-category">Yae Miko Constellations</h2>
                            <?php
                            $constellations = array(
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/constellation_1.png',
                                    "title" => "Constellation 1",
                                    "name" => "Yakan Offering",
                                    "description" => "Each time Great Secret Art: Tenko Kenshin activates a Tenko Thunderbolt, Yae Miko will restore 8 Elemental Energy for herself."
                                ),
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/constellation_2.png',
                                    "title" => "Constellation 2",
                                    "name" => "Fox's Mooncall",
                                    "description" => "Sesshou Sakura start at Level 2 when created, their max level is increased to 4, and their attack range is increased by 60%."
                                ),
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/constellation_3.png',
                                    "title" => "Constellation 3",
                                    "name" => "The Seven Glamours",
                                    "description" => "Increases the Level of Yakan Evocation: Sesshou Sakura by 3. Maximum upgrade level is 15."
                                ),
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/constellation_4.png',
                                    "title" => "Constellation 4",
                                    "name" => "Sakura Channeling",
                                    "description" => "When Sesshou Sakura lightning hits opponents, the Electro DMG Bonus of all nearby party members is increased by 20% for 5s."
                                ),
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/constellation_5.png',
                                    "title" => "Constellation 5",
                                    "name" => "Mischievous Teasing",
                                    "description" => "Increases the Level of Great Secret Art: Tenko Kenshin by 3. Maximum upgrade level is 15."
                                ),
                                array(
                                    "icon" => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/constellation_6.png',
                                    "title" => "Constellation 6",
                                    "name" => "Forbidden Art: Daisesshou",
                                    "description" => "The Sesshou Sakura's attacks will ignore 60% of the opponent's DEF."
                                )
                            );

                            // 各情報を出力
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
                            <h2 class="character-category">Yae Miko Ascension Costs</h2>
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
                                            array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Vajrada Amethyst Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Sea Ganoderma", "Count3" => "3", "Material4" => "Old Handguard", "Count4" => "3"),
                                            array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Vajrada Amethyst Fragment", "Count1" => "3", "Material2" => "Dragonheir's False Fin", "Count2" => "2", "Material3" => "Sea Ganoderma", "Count3" => "10", "Material4" => "Old Handguard", "Count4" => "15"),
                                            array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Vajrada Amethyst Fragment", "Count1" => "6", "Material2" => "Dragonheir's False Fin", "Count2" => "4", "Material3" => "Sea Ganoderma", "Count3" => "20", "Material4" => "Kageuchi Handguard", "Count4" => "12"),
                                            array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Vajrada Amethyst Chunk", "Count1" => "3", "Material2" => "Dragonheir's False Fin", "Count2" => "8", "Material3" => "Sea Ganoderma", "Count3" => "30", "Material4" => "Kageuchi Handguard", "Count4" => "18"),
                                            array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Vajrada Amethyst Chunk", "Count1" => "6", "Material2" => "Dragonheir's False Fin", "Count2" => "12", "Material3" => "Sea Ganoderma", "Count3" => "45", "Material4" => "Famed Handguard", "Count4" => "12"),
                                            array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Vajrada Amethyst Gemstone", "Count1" => "6", "Material2" => "Dragonheir's False Fin", "Count2" => "20", "Material3" => "Sea Ganoderma", "Count3" => "60", "Material4" => "Famed Handguard", "Count4" => "24")
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
                                                        $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Miko/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
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
<?php get_footer('home1'); ?>