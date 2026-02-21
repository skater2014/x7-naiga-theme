<?php
/**
 * Template Name: Neuvillette.php
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Neuvillette.png';
                $image_alt = 'Neuvillette';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Neuvillette Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_geo.png"
                            alt="Geo">
                    </div>
                    <div class="character-path"> <img class="character-path-icon"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_catalyst.png"
                            alt="Catalyst">Catalyst </div>
                    <div class="character-role">Main DPS</div>
                </div>
                <!-- Character Materials -->
                <div class="character-materials">
                    <?php
                    $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

                    $materials = array(
                        'Equity' => 'Equity',
                        'Varunada Lazurite Sliver' => 'Varunada Lazurite Sliver',
                        'Transoceanic Pearl' => 'Transoceanic Pearl',
                        'Lumitoile' => 'Lumitoile',
                        "Fontemer Unihorn" => "Fontemer Unihorn",
                        'Everamber' => 'Everamber',
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
                        <!-- Neuvillette Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Neuvillette Best Weapons</h2>
                            <div class="character-build-weapons">
                                <?php
                                $weapons = array(
                                    array("Tome of the Eternal Flow", 1, 5),
                                    array('Sacrificial Jade', 2, 3),
                                    array("Jadefall's Splendor", 3, 5),
                                    array('Prototype Amber', 4, 4),
                                    array("Kagura's Verity", 5, 5)
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

                        <!-- Neuvillette Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Neuvillette Best Artifacts</h2>

                            <?php
                            $artifacts = array(
                                array('Marechaussee Hunter', 1),
                                array("Heart of Depth", 2),

                                array("Heart of Depth", 3),
                                array("Marechaussee Hunter", 3),

                                array("Marechaussee Hunter", 4),
                                array("Tenacity of the Millelith", 4),

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




                    <!-- Neuvillette Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Neuvillette Best Stats</h2>
                        <div class="character-stats-item"><b>Sands:</b> ATK%</div>
                        <div class="character-stats-item"><b>Goblet:</b> Hydro DMG%</div>
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
                    <h2 class="character-category">Best Neuvillette Teams</h2>
                    <!-- Neuvillette Freeze Team -->
                    <div class="character-team">
                        <div class="character-team-name">Neuvillette Vaporize Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Neuvillette_Teams1["Neuvillette"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Neuvillette_Teams1["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Neuvillette_Teams1["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                $Neuvillette_Teams1["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Neuvillette_Teams1 as $name => $info):
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

                    <!-- Neuvillette/Ganyu Mono Cryo Team -->
                    <div class="character-team">
                        <div class="character-team-name">Neuvillette/Raiden National Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Neuvillette_Teams2["Neuvillette"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Neuvillette_Teams2["Raiden"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Neuvillette_Teams2["Jean"] = array("element" => "anemo", "rarity" => "rarity-5");
                                $Neuvillette_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-5");

                                // キャラクター情報を出力
                                foreach ($Neuvillette_Teams2 as $name => $info):
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
                    <!-- Neuvillette/Ganyu Furina Hydro Team -->
                    <div class="character-team">
                        <div class="character-team-name">Neuvillette/blooming intensely Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Neuvillette_Teams3["Neuvillette"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Neuvillette_Teams3["Kuki Shinobu"] = array("element" => "electro", "rarity" => "rarity-4");
                                $Neuvillette_Teams3["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Neuvillette_Teams3["Collei"] = array("element" => "dendro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Neuvillette_Teams3 as $name => $info):
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
                    // Neuvilletteの情報を格納する配列
                    $NeuvilletteInfo = array(
                        "NormalAttack" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png",
                            "title" => "Normal Attack",
                            "name" => "As Water Seeks Equilibrium",
                            "description" => "Normal Attack
                            With light flourishes, Neuvillette commands the tides to unleash a maximum of 3 attacks, dealing Hydro DMG.
                            Charged Attack Empowerment: Legal Evaluation
                            While charging up, Neuvillette will gather the power of water, forming it into a Seal of Arbitration. In this state, Neuvillette can move and change facing, and also absorb any Sourcewater Droplets in a certain AoE.
                            Every Droplet he absorbs will increase the formation speed of the Seal, and will heal Neuvillette.
                            When the charging is stopped, if the Symbol has yet to be formed, then a Charged Attack will be unleashed. If it has been formed, then a Charged Attack: Equitable Judgment will be unleashed.

                            Charged Attack
                            Consumes a fixed amount of Stamina to attack opponents with a rupturing blast of water, dealing AoE Hydro DMG.
                            Charged Attack: Equitable Judgment
                            Unleashes surging torrents, dealing continuous AoE Hydro DMG to all opponents in a straight-line area in front of him.
                            Equitable Judgment will not consume any Stamina and lasts 3s.
                            If Neuvillette's HP is above 50%, he will continuously lose HP while using this attack.

                            Plunging Attack
                            Gathering the might of Hydro, Neuvillette plunges towards the ground from mid-air, damaging all opponents in his path. Deals AoE Hydro DMG upon impact with the ground."
                        ),

                        "ElementalSkill" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Neuvillette/talent_2.png",
                            "title" => "Elemental Skill",
                            "name" => "O Tears, I Shall Repay",
                            "description" => <<<EOT
                            Summons a Raging Waterfall that will deal AoE Hydro DMG to opponents in front of Neuvillette based on his Max HP. After hitting an opponent, this skill will generate 3 Sourcewater Droplets near that opponent.Arkhe: Pneuma At certain intervals, when the Raging Waterfall descends, a Spiritbreath Thorn will descend that will pierce opponents, dealing Pneuma-aligned Hydro DMG.
                            EOT
                        ),

                        "ElementalBurst" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Neuvillette/talent_3.png",
                            "title" => "Elemental Burst",
                            "name" => "O Tides, I Have Returned",
                            "description" => "Unleashes waves that will deal AoE Hydro DMG based on Neuvillette's Max HP. After a short interval, 2 waterfalls will descend and deal Hydro DMG in a somewhat smaller AoE, and will generate 6 Sourcewater Droplets within an area in front."
                        ),
                    );

                    // 各情報を出力
                    ?>

                    <?php
                    // Neuvillette Passivesの情報を格納する配列
                    $passivesInfo = array(
                        "Ascension1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_4.png",
                            "title" => "Ascension 1",
                            "name" => "Heir to the Ancient Sea's Authority",
                            "description" => "When a party member triggers a Vaporize, Frozen, Electro-Charged, Bloom, Hydro Swirl, or a Hydro Crystallize reaction on opponents, 1 stack of Past Draconic Glories will be granted to Neuvillette for 30s. Max 3 stacks. Past Draconic Glories causes Charged Attack: Equitable Judgment to deal 110%/125%/160% of its original DMG. The stacks of Past Draconic Glories created by each kind of Elemental Reaction exist independently."
                        ),
                        "Ascension4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_5.png",
                            "title" => "Ascension 4",
                            "name" => "Discipline of the Supreme Arbitration",
                            "description" => "For each 1% of Neuvillette's current HP greater than 30% of Max HP, he will gain 0.6% Hydro DMG Bonus. A maximum bonus of 30% can be obtained this way."
                        ),
                        "Passive" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Arlecchino/talent_6.png",
                            "title" => "Passive",
                            "name" => "Gather Like the Tide",
                            "description" => "Increases underwater Sprint SPD for your own party members by 15%. Not stackable with Passive Talents that provide the exact same effects."
                        )
                    );

                    // Neuvillette Constellationsの情報を格納する配列
                    $constellationsInfo = array(
                        "Constellation1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Neuvillette/constellation_1.png",
                            "title" => "Constellation 1",
                            "name" => "Venerable Institution.",
                            "description" => "When Neuvillette takes the field, he will obtain 1 stack of Past Draconic Glories from the Passive Talent 'Heir to the Ancient Sea's Authority.' You must first unlock the Passive Talent 'Heir to the Ancient Sea's Authority.' Additionally, his interruption resistance will be increased while using the Charged Attack Empowerment: Legal Evaluation and the Charged Attack: Equitable Judgment."
                        ),
                        "Constellation2" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Neuvillette/constellation_2.png",
                            "title" => "Constellation 2",
                            "name" => "Juridical Exhortation",
                            "description" => "The Passive Talent 'Heir to the Ancient Sea's Authority' will be enhanced: Each stack of Past Draconic Glories will increase the CRIT DMG of Charged Attack: Equitable Judgment by 14%. The maximum increase that can be achieved this way is 42%. You must first unlock the Passive Talent 'Heir to the Ancient Sea's Authority.'"
                        ),
                        "Constellation3" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Neuvillette/constellation_3.png",
                            "title" => "Constellation 3",
                            "name" => "Ancient Postulation",
                            "description" => "Increases the Level of Normal Attack: As Water Seeks Equilibrium by 3. Maximum upgrade level is 15."
                        ),
                        "Constellation4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Neuvillette/constellation_4.png",
                            "title" => "Constellation 4",
                            "name" => "Crown of Commiseration",
                            "description" => "When Neuvillette is on the field and is healed, 1 Sourcewater Droplet will be generated. This effect can occur once every 4s."
                        ),
                        "Constellation5" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Neuvillette/constellation_5.png",
                            "title" => "Axiomatic Judgment",
                            "name" => "Constellation 5",
                            "description" => "Increases the Level of O Tides, I Have Returned by 3. Maximum upgrade level is 15."
                        ),
                        "Constellation6" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Neuvillette/constellation_6.png",
                            "title" => "Constellation 6",
                            "name" => "Wrathful Recompense.",
                            "description" => "When using Charged Attack: Equitable Judgment, Neuvillette can absorb nearby Sourcewater Droplets in an AoE. Each absorbed Droplet will increase the duration of Charged Attack: Equitable Judgment by 1s. Additionally, when Equitable Judgment hits opponents, it will fire off 2 additional currents every 2s, each of which will deal 10% of Neuvillette's Max HP as Hydro DMG. DMG dealt this way will count as DMG dealt by Equitable Judgment."
                        )
                    );
                    ?>



                    <?php
                    // Neuvillette Passivesの情報を出力
                    ?>
                    <div class="character-skills" id="passives">
                        <h2 class="character-category">Neuvillette Passives</h2>
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
                    // Neuvillette Constellationsの情報を出力
                    ?>
                    <div class="character-skills" id="constellations">
                        <h2 class="character-category">Neuvillette Constellations</h2>
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
                        <h2 class="character-category">Neuvillette Talents</h2>
                        <?php foreach ($NeuvilletteInfo as $skill): ?>
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
                        <h2 class="character-category">Neuvillette Ascension Costs</h2>
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
                                        array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Varunada Lazurite Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Lumitoile", "Count3" => "3", "Material4" => "Transoceanic Pearl", "Count4" => "3"),

                                        array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Varunada Lazurite Fragment", "Count1" => "3", "Material2" => "Fontemer Unihorn", "Count2" => "2", "Material3" => "Lumitoile", "Count3" => "10", "Material4" => "Transoceanic Pearl", "Count4" => "15"),

                                        array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Varunada Lazurite Chunk", "Count1" => "6", "Material2" => "Fontemer Unihorn", "Count2" => "4", "Material3" => "Lumitoile", "Count3" => "20", "Material4" => "Transoceanic Chunk", "Count4" => "12"),

                                        array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Varunada Lazurite Chunk", "Count1" => "3", "Material2" => "Fontemer Unihorn", "Count2" => "8", "Material3" => "Lumitoile", "Count3" => "30", "Material4" => "Transoceanic Chunk", "Count4" => "18"),

                                        array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Varunada Lazurite Chunk", "Count1" => "6", "Material2" => "Fontemer Unihorn", "Count2" => "12", "Material3" => "Lumitoile", "Count3" => "45", "Material4" => "Xenochromatic Crystal", "Count4" => "12"),

                                        array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Varunada Lazurite Gemstone", "Count1" => "6", "Material2" => "Fontemer Unihorn", "Count2" => "20", "Material3" => "Lumitoile", "Count3" => "60", "Material4" => "Xenochromatic Crystal", "Count4" => "24")
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
            <h2 class="character-category">Neuvillette Showcase</h2>
            <div class="character-showcase" id="showcase">
                <lite-youtube videoid="YUzbK2uG9q0" params="rel=0"></lite-youtube>
            </div>


            <!--character end-->
            <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>