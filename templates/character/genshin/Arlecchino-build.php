<?php
/**
 * Template Name: Arlecchino.php
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Arlecchino.png';
                $image_alt = 'Arlecchino';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Arlecchino Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_pyro.png"
                            alt="Geo">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_sword.png"
                            alt="Polearm">Polearm </div>
                    <div class="character-role">Main DPS</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

                    $materials = array(
                        'Order' => 'Order',
                        'Fading Candle' => 'Fading Candle',
                        'Agnidus Agate Sliver' => 'Agnidus Agate Sliver',
                        'Rainbow Rose' => 'Rainbow Rose',
                        "Recruit's Insignia" => "Recruit's Insignia",
                        'Fragment of a Golden Melody' => 'Fragment of a Golden Melody',
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
                        <!-- Arlecchino Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Arlecchino Best Weapons</h2>
                            <div class="character-build-weapons">
                                <?php
                                $weapons = array(
                                    array("Crimson Moon's Semblance", 1, 5),
                                    array('Primordial Jade Winged-Spear', 2, 5),
                                    array('Staff of the Scarlet Sands', 3, 5),
                                    array('Deathmatch', 4, 3),
                                    array('Blackcliff Pole', 5, 3)
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

                        <!-- Arlecchino Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Arlecchino Best Artifacts</h2>

                            <?php
                            $artifacts = array(
                                array('Fragment of Harmonic Whimsy', 1),
                                array("Gladiator's Finale", 2),
                                array("Gilded Dreams", 3),
                                array("Crimson Witch of Flames", 4),
                                array("Fragment of Harmonic Whimsy", 4),
                                array("Fragment of Harmonic Whimsy", 5),
                                array("Gladiator's Finale", 5)
                            );


                            $manualRanks = array(4, 4, 4, 2, 2, 2, 2);

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




                    <!-- Arlecchino Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Arlecchino Best Stats</h2>
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
                    <h2 class="character-category">Best Arlecchino Teams</h2>
                    <!-- Arlecchino Freeze Team -->
                    <div class="character-team">
                        <div class="character-team-name">Arlecchino Vaporize Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Arlecchino_Teams1["Arlecchino"] = array("element" => "pyro", "rarity" => "rarity-5");
                                $Arlecchino_Teams1["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Arlecchino_Teams1["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                $Arlecchino_Teams1["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Arlecchino_Teams1 as $name => $info):
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

                    <!-- Arlecchino/Ganyu Mono Cryo Team -->
                    <div class="character-team">
                        <div class="character-team-name">Arlecchino/Raiden National Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Arlecchino_Teams2["Arlecchino"] = array("element" => "pyro", "rarity" => "rarity-5");
                                $Arlecchino_Teams2["Raiden"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Arlecchino_Teams2["Jean"] = array("element" => "anemo", "rarity" => "rarity-5");
                                $Arlecchino_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-5");

                                // キャラクター情報を出力
                                foreach ($Arlecchino_Teams2 as $name => $info):
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
                    <!-- Arlecchino/Ganyu Furina Hydro Team -->
                    <div class="character-team">
                        <div class="character-team-name">Arlecchino/blooming intensely Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Arlecchino_Teams3["Arlecchino"] = array("element" => "pyro", "rarity" => "rarity-5");
                                $Arlecchino_Teams3["Kuki Shinobu"] = array("element" => "electro", "rarity" => "rarity-4");
                                $Arlecchino_Teams3["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Arlecchino_Teams3["Collei"] = array("element" => "dendro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Arlecchino_Teams3 as $name => $info):
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
                    // Arlecchinoの情報を格納する配列
                    $ArlecchinoInfo = array(
                        "NormalAttack" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png",
                            "title" => "Normal Attack",
                            "name" => "Invitation to a Beheading",
                            "description" => "Normal Attack
                            Performs up to 6 consecutive spear strikes.
                            
                            Charged Attack
                            Consumes a fixed amount of Stamina, dashing toward a nearby opponent and cleaving once.
                            Continuously holding this button will cause Arlecchino to consume Stamina and engage in up to 5s of high-speed movement.
                            
                            Plunging Attack
                            Plunges from mid-air to strike the ground below, damaging opponents along the path and dealing AoE DMG upon impact.
                            
                            Masque of the Red Death\"Masque of the Red Death\" state, where her Normal, Charged, and Plunging Attacks will be converted to deal Pyro DMG. This cannot be overridden.
                            When in the \"Masque of the Red Death\" state, Arlecchino's Normal Attacks will deal extra DMG to opponents on hit that scales off her ATK multiplied by a certain ratio of her current Bond of Life percentage. This will consume 7.5% of said current Bond of Life. Her Bond of Life can be consumed this way every 0.03s. When her Bond of Life is consumed in this manner, All Is Ash's CD will decrease by 0.8s."
                        ),
                        "ElementalSkill" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_2.png",
                            "title" => "Elemental Skill",
                            "name" => "All Is Ash",
                            "description" => <<<EOT
                            Summons forth Balemoon Bloodfire, dealing Pyro DMG to multiple nearby opponents and performing a dash-cleave against one of them, dealing AoE Pyro DMG.
                            Opponents hit by the aforementioned attack will have a Blood-Debt Directive applied to them.
                            
                            Blood-Debt Directive
                            Lasts 30s. Every 5s, it will deal 1 instance of Pyro DMG to the opponent. Max 2 instances. This DMG will be considered Elemental Skill DMG.
                            When Arlecchino uses a Charged Attack or her Elemental Burst, Balemoon Rising, she will absorb and clear nearby Blood-Debt Directives. Each Directive absorbed grants her a Bond of Life worth 65% of her Max HP.
                            The maximum value of the Bond of Life she can be granted through Blood-Debt Directives within 35s after using her Elemental Skill is 145% of her Max HP. Using the Elemental Skill again during this duration will restart the count on duration and the limit on the value of Bond of Life she may gain from Blood-Debt Directives.
                            EOT
                        ),

                        "ElementalBurst" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_3.png",
                            "title" => "Elemental Burst",
                            "name" => "Balemoon Rising",
                            "description" => "Arlecchino's great wing of Balemoon Bloodfire beats as she absorbs and clears Blood-Debt Directives around her. She deals AoE Pyro DMG before clearing the CD of All Is Ash and healing herself. The healing is based on her Bond of Life value and ATK."
                        ),
                    );

                    // 各情報を出力
                    ?>

                    <?php
                    // Arlecchino Passivesの情報を格納する配列
                    $passivesInfo = array(
                        "Ascension1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_4.png",
                            "title" => "Ascension 1",
                            "name" => "Agony Alone May Be Repaid",
                            "description" => "Blood-Debt Directives have the following characteristics:
        Arlecchino will be granted a Bond of Life worth 130% of her Max HP when an opponent to which she herself applied a Directive is defeated.
        5s after a Directive is applied, it will be upgraded to a Blood-Debt Due. When absorbed, it will instead grant Arlecchino a Bond of Life worth 130%.
        A Bond of Life created in the aforementioned ways cannot exceed the original limit on the value of Bonds of Life obtained through All Is Ash."
                        ),

                        "Ascension4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_5.png",
                            "title" => "Ascension 4",
                            "name" => "Strength Alone Can Defend",
                            "description" => "Arlecchino gains 1% All Elemental and Physical RES for every 100 ATK she has in excess of 1,000. The maximum RES increase she can gain this way for each is 20%."
                        ),
                        "Passive" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_6.png",
                            "title" => "Passive",
                            "name" => "The Balemoon Alone May Know",
                            "description" => "While in combat, Arlecchino gains a 40% Pyro DMG Bonus and can only be healed through Balemoon Rising."
                        )
                    );

                    // Arlecchino Constellationsの情報を格納する配列
                    $constellationsInfo = array(
                        "Constellation1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/constellation_1.png",
                            "title" => "Constellation 1",
                            "name" => "All Reprisals and Arrears, Mine to Bear...",
                            "description" => "Masque of the Red Death is further enhanced, the value of the increase is 100%. Additionally, Arlecchino's interruption resistance is increased when she performs Normal or Charged Attacks while affected by the Masque of the Red Death."
                        ),

                        "Constellation2" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/constellation_2.png",
                            "title" => "Constellation 2",
                            "name" => "In Five Colors Dyed",
                            "description" => "For 10s after using Hiyoku: Twin Blades, a simplified automaton doll, \"Kinu,\" will be summoned next to your active character every 3s. Kinu will attack nearby opponents, dealing AoE Geo DMG equivalent to 170% of Tamoto's DMG. DMG dealt this way is considered Elemental Skill DMG."
                        ),
                        "Constellation3" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/constellation_3.png",
                            "title" => "Constellation 3",
                            "name" => "You Shall Become a New Member of Our Family...",
                            "description" => "Increases the Level of Normal Attack: Invitation to a Beheading by 3.Maximum upgrade level is 15."
                        ),
                        "Constellation4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/constellation_4.png",
                            "title" => "Constellation 4",
                            "name" => "You Shall Love and Protect Each Other Henceforth...",
                            "description" => "When Arlecchino successfully absorbs a Blood-Debt Directive, Balemoon Rising's CD will decrease by 2s and 15 Elemental Energy will be restored to her. This effect can occur once every 10s."
                        ),
                        "Constellation5" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/constellation_5.png",
                            "title" => "Constellation 5",
                            "name" => "For Alone, We Are as Good as Dead...",
                            "description" => "Increases the Level of Balemoon Rising by 3.Maximum upgrade level is 15."
                        ),
                        "Constellation6" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/constellation_6.png",
                            "title" => "Constellation 6",
                            "name" => "From This Day On, We Shall Delight in New Life Together.",
                            "description" => "The DMG of Balemoon Rising is increased by Arlecchino's ATK multiplied by 700% of Arlecchino's current Bond of Life percentage.
                            For 20s after Arlecchino uses All Is Ash, both her Normal Attacks and Elemental Burst gain 10% increased CRIT Rate and 70% increased CRIT DMG. This effect can be triggered up to once every 15s."
                        ),
                    );
                    ?>


                    <?php
                    // Arlecchino Passivesの情報を出力
                    ?>
                    <div class="character-skills" id="passives">
                        <h2 class="character-category">Arlecchino Passives</h2>
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
                    // Arlecchino Constellationsの情報を出力
                    ?>
                    <div class="character-skills" id="constellations">
                        <h2 class="character-category">Arlecchino Constellations</h2>
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
                        <h2 class="character-category">Arlecchino Talents</h2>
                        <?php foreach ($ArlecchinoInfo as $skill): ?>
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
                        <h2 class="character-category">Arlecchino Ascension Costs</h2>
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
                                        array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Agnidus Agate Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Rainbow Rose", "Count3" => "3", "Material4" => "Recruit's Insignia", "Count4" => "3"),
                                        array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Agnidus Agate Fragment", "Count1" => "3", "Material2" => "Fragment of a Golden Melody", "Count2" => "2", "Material3" => "Rainbow Rose", "Count3" => "10", "Material4" => "Recruit's Insignia", "Count4" => "15"),
                                        array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Agnidus Agate Fragment", "Count1" => "6", "Material2" => "Fragment of a Golden Melody", "Count2" => "4", "Material3" => "Rainbow Rose", "Count3" => "20", "Material4" => "Sergeant's Insignia", "Count4" => "12"),
                                        array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Agnidus Agate Fragment", "Count1" => "3", "Material2" => "Fragment of a Golden Melody", "Count2" => "8", "Material3" => "Rainbow Rose", "Count3" => "30", "Material4" => "Sergeant's Insignia", "Count4" => "18"),
                                        array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Agnidus Agate Fragment", "Count1" => "6", "Material2" => "Fragment of a Golden Melody", "Count2" => "12", "Material3" => "Rainbow Rose", "Count3" => "45", "Material4" => "Lieutenant's Insignia", "Count4" => "12"),
                                        array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Agnidus Agate Gemstone", "Count1" => "6", "Material2" => "Fragment of a Golden Melody", "Count2" => "20", "Material3" => "Rainbow Rose", "Count3" => "60", "Material4" => "Lieutenant's Insignia", "Count4" => "24")
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
                                                    $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/farming/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
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
            <h2 class="character-category">Arlecchino Showcase</h2>
            <div class="character-showcase" id="showcase">
                <lite-youtube videoid="wIJZPUbjv6Y" params="rel=0"></lite-youtube>
            </div>


            <!--character end-->
            <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>