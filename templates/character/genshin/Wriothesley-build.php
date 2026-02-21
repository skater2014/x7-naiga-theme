<?php
/**
 * Template Name: Wriothesley.php
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
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Wriothesley.png';
                $image_alt = 'Wriothesley';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
                <!-- Character Header -->
                <div class="character-header">
                    <div class="character-title">
                        <h1 class="character-name">Genshin Impact Wriothesley Build</h1> <img class="character-element"
                            src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_cryo.png"
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
                        'Order' => 'Order',
                        'Shivada Jade Sliver' => 'Shivada Jade Sliver',
                        'Subdetection Unit' => 'Subdetection Unit',
                        'Meshing Gear' => 'Meshing Gear',
                        "Tourbillon Device" => "Tourbillon Device",
                        'Primordial Greenbloom' => 'Primordial Greenbloom',
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
                        <!-- Wriothesley Best Weapons -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Wriothesley Best Weapons</h2>
                            <div class="character-build-weapons">
                                <?php
                                $weapons = array(
                                    array("Cashflow Supervision", 1, 5),
                                    array("Tulaytullah's Remembrance", 2, 5),
                                    array("Skyward Atlas", 3, 5),
                                    array('Flowing Purity', 4, 4),
                                    array("The Widsith", 5, 4)
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

                        <!-- Wriothesley Best Artifacts -->
                        <div class="character-build-section">
                            <h2 class="character-build-section-title">Wriothesley Best Artifacts</h2>

                            <?php
                            $artifacts = array(
                                array('Marechaussee Hunter', 1),
                                array("Blizzard Strayer", 2),
                                array("Shimenawa's Reminiscence", 3),

                                array("Blizzard Strayer", 4),
                                array("Marechaussee Hunter", 4),

                                array("Marechaussee Hunter", 5),
                                array("Shimenawa's Reminiscence", 5),

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




                    <!-- Wriothesley Best Stats -->
                    <div class="character-stats">
                        <h2 class="character-stats-title">Wriothesley Best Stats</h2>
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
                    <h2 class="character-category">Best Wriothesley Teams</h2>
                    <!-- Wriothesley Freeze Team -->
                    <div class="character-team">
                        <div class="character-team-name">Wriothesley Vaporize Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Wriothesley_Teams1["Wriothesley"] = array("element" => "cryo", "rarity" => "rarity-5");
                                $Wriothesley_Teams1["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Wriothesley_Teams1["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                                $Wriothesley_Teams1["Xingqiu"] = array("element" => "hydro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Wriothesley_Teams1 as $name => $info):
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

                    <!-- Wriothesley/Ganyu Mono Cryo Team -->
                    <div class="character-team">
                        <div class="character-team-name">Wriothesley/Raiden National Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Wriothesley_Teams2["Wriothesley"] = array("element" => "cryo", "rarity" => "rarity-5");
                                $Wriothesley_Teams2["Raiden"] = array("element" => "electro", "rarity" => "rarity-5");
                                $Wriothesley_Teams2["Jean"] = array("element" => "anemo", "rarity" => "rarity-5");
                                $Wriothesley_Teams2["Bennett"] = array("element" => "pyro", "rarity" => "rarity-5");

                                // キャラクター情報を出力
                                foreach ($Wriothesley_Teams2 as $name => $info):
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
                    <!-- Wriothesley/Ganyu Furina Hydro Team -->
                    <div class="character-team">
                        <div class="character-team-name">Wriothesley/blooming intensely Teams
                            <div class="character-team-characters">
                                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();
                                
                                // 欲しいキャラクターの情報だけを取得
                                $Wriothesley_Teams3["Wriothesley"] = array("element" => "cryo", "rarity" => "rarity-5");
                                $Wriothesley_Teams3["Kuki Shinobu"] = array("element" => "electro", "rarity" => "rarity-4");
                                $Wriothesley_Teams3["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                                $Wriothesley_Teams3["Collei"] = array("element" => "dendro", "rarity" => "rarity-4");
                                // キャラクター情報を出力
                                foreach ($Wriothesley_Teams3 as $name => $info):
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
                    // Wriothesleyの情報を格納する配列
                    $WriothesleyInfo = array(
                        "NormalAttack" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png",
                            "title" => "Normal Attack",
                            "name" => "Forceful Fists of Frost",
                            "description" => "Normal Attack
                            Coalescing frost about his fist, Wriothesley will unleash powerful Repelling Fists, performing up to 5 rapid attacks that deal Cryo DMG.
                            Apart from this, Normal Attack combo count will not reset for a short time after using Icefang Rush or sprinting.

                            Charged Attack
                            Consumes a fixed amount of Stamina to leap and unleash a Vaulting Fist, dealing AoE Cryo DMG.

                            Plunging Attack
                            Plunges from mid-air to strike the ground below, damaging opponents along the path and dealing AoE Cryo DMG upon impact."
                        ),

                        "ElementalSkill" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/talent_2.png",
                            "title" => "Elemental Skill",
                            "name" => "Icefang Rush",
                            "description" => <<<EOT
                            Adjusting his breathing, rhythm, and pace, Wriothesley sprints forward a short distance, entering the Chilling Penalty state and unleashing more powerful attacks than before.Chilling Penalty
                                Increases Wriothesley's interruption resistance.
                                When his HP is above 50%, it will enhance the Repelling Fists of Normal Attack: Forceful Fists of Frost and increase its DMG. When such an attack hits, it will consume a fixed amount of Wriothesley's HP. HP can be lost this way once every 0.1s.
                                This effect will be canceled should Wriothesley leave the field.
                            EOT
                        ),

                        "ElementalBurst" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/talent_3.png",
                            "title" => "Elemental Burst",
                            "name" => "Darkgold Wolfbite",
                            "description" => "Activating his boxing gloves, Wriothesley strikes out with an icy straight, then uses Icicle Impact to cause multiple instances of AoE Cryo DMG in a frontal area.Arkhe: Ousia
                                After Icicle Impact ends, a Surging Blade will descend upon the opponent's position, dealing Ousia-aligned Cryo DMG."
                        ),
                    );

                    // 各情報を出力
                    ?>

                    <?php
                    // Wriothesley Passivesの情報を格納する配列
                    $passivesInfo = array(
                        "Ascension1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/talent_4.png",
                            "title" => "Ascension 1",
                            "name" => "There Shall Be a Plea for Justice",
                            "description" => "When Wriothesley's HP is less than 60%, he will obtain a Gracious Rebuke. The next Charged Attack of his Normal Attack: Forceful Fists of Frost will be enhanced to become Rebuke: Vaulting Fist. It will not consume Stamina, will deal 50% increased DMG, and after hitting will restore HP for Wriothesley equal to 30% of his Max HP.You can gain a Gracious Rebuke this way once every 5s."
                        ),
                        "Ascension4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/talent_5.png",
                            "title" => "Ascension 4",
                            "name" => "Discipline of the Supreme Arbitration",
                            "description" => "When Wriothesley's current HP increases or decreases, if he is in the Chilling Penalty state conferred by Icefang Rush, Chilling Penalty will gain one stack of Prosecution Edict. Max 5 stacks. Each stack will increase Wriothesley's ATK by 6%."
                        ),
                        "Passive" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/talent_6.png",
                            "title" => "Passive",
                            "name" => "The Duke's Grace",
                            "description" => "When Wriothesley crafts Weapon Ascension Materials, he has a 10% chance to receive double the product."
                        )
                    );
                    ?>

                    <?php
                    // Wriothesley Constellationsの情報を格納する配列
                    $constellationsInfo = array(
                        "Constellation1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/constellation_1.png",
                            "title" => "Constellation 1",
                            "name" => "Terror for the Evildoers.",
                            "description" => "The Gracious Rebuke from the Passive Talent \"There Shall Be a Plea for Justice\" is changed to this:
                            When Wriothesley's HP is less than 60% or while he is in the Chilling Penalty state caused by Icefang Rush, when the fifth attack of Repelling Fists hits, it will create a Gracious Rebuke. 1 Gracious Rebuke effect can be obtained every 2.5s.
                            Additionally, Rebuke: Vaulting Fist will obtain the following enhancement:
                            
                            The DMG Bonus gained will be further increased to 200%.
                            When it hits while Wriothesley is in the Chilling Penalty state, that state's duration is extended by 4s. 1 such extension can occur per 1 Chilling Penalty duration.
                            
                            You must first unlock the Passive Talent \"There Shall Be a Plea for Justice.\""
                        ),
                        "Constellation2" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/constellation_2.png",
                            "title" => "Constellation 2",
                            "name" => "Shackles for the Arrogant",
                            "description" => "When using Darkgold Wolfbite, each Prosecution Edict stack from the Passive Talent \"There Shall Be a Reckoning for Sin\" will increase said ability's DMG dealt by 40%.
                            You must first unlock the Passive Talent \"There Shall Be a Reckoning for Sin.\""
                        ),
                        "Constellation3" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/constellation_3.png",
                            "title" => "Constellation 3",
                            "name" => "Shackles for the Arrogant",
                            "description" => "Increases the Level of Normal Attack: Forceful Fists of Frost by 3. Maximum upgrade level is 15."
                        ),
                        "Constellation4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/constellation_4.png",
                            "title" => "Constellation 4",
                            "name" => "Redemption for the Suffering",
                            "description" => "The HP restored to Wriothesley through Rebuke: Vaulting Fist will be increased to 50% of his Max HP. You must first unlock the Passive Talent \"There Shall Be a Plea for Justice.\"
                            Additionally, when Wriothesley is healed, if the amount of healing overflows, the following effects will occur depending on whether he is on the field or not. If he is on the field, his ATK SPD will be increased by 20% for 4s. If he is off-field, all of your own party members' ATK SPD will be increased by 10% for 6s. These two methods of increasing ATK SPD cannot stack."
                        ),
                        "Constellation5" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/constellation_5.png",
                            "title" => "Constellation 5",
                            "name" => "Mercy for the Wronged",
                            "description" => "Increases the Level of Darkgold Wolfbite by 3. Maximum upgrade level is 15."
                        ),
                        "Constellation6" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Wriothesley/constellation_6.png",
                            "title" => "Constellation 6",
                            "name" => "Esteem for the Innocent.",
                            "description" => "The CRIT Rate of Rebuke: Vaulting Fist will be increased by 10%, and its CRIT DMG by 80%. When unleashed, it will also create an additional icicle that deals 100% of Rebuke: Vaulting Fist's Base DMG as Cryo DMG. DMG dealt this way is regarded as Charged Attack DMG. You must first unlock the Passive Talent \"There Shall Be a Plea for Justice.\""
                        )
                    );
                    ?>




                    <?php
                    // Wriothesley Passivesの情報を出力
                    ?>
                    <div class="character-skills" id="passives">
                        <h2 class="character-category">Wriothesley Passives</h2>
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
                    // Wriothesley Constellationsの情報を出力
                    ?>
                    <div class="character-skills" id="constellations">
                        <h2 class="character-category">Wriothesley Constellations</h2>
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
                        <h2 class="character-category">Wriothesley Talents</h2>
                        <?php foreach ($WriothesleyInfo as $skill): ?>
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
                        <h2 class="character-category">Wriothesley Ascension Costs</h2>
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
                                        array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Shivada Jade Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Subdetection Unit", "Count3" => "3", "Material4" => "Meshing Gear", "Count4" => "3"),

                                        array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Shivada Jade Fragment", "Count1" => "3", "Material2" => "Tourbillon Device", "Count2" => "2", "Material3" => "Subdetection Unit", "Count3" => "10", "Material4" => "Meshing Gear", "Count4" => "15"),

                                        array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Shivada Jade Fragment", "Count1" => "6", "Material2" => "Tourbillon Device", "Count2" => "4", "Material3" => "Subdetection Unit", "Count3" => "20", "Material4" => "Mechanical Spur Gear", "Count4" => "12"),

                                        array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Shivada Jade Chunk", "Count1" => "3", "Material2" => "Tourbillon Device", "Count2" => "8", "Material3" => "Subdetection Unit", "Count3" => "30", "Material4" => "Mechanical Spur Gear", "Count4" => "18"),

                                        array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Shivada Jade Chunk", "Count1" => "6", "Material2" => "Tourbillon Device", "Count2" => "12", "Material3" => "Subdetection Unit", "Count3" => "45", "Material4" => "Artificed Dynamic Gear", "Count4" => "12"),

                                        array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Shivada Jade Gemstone", "Count1" => "6", "Material2" => "Tourbillon Device", "Count2" => "20", "Material3" => "Subdetection Unit", "Count3" => "60", "Material4" => "Artificed Dynamic Gear", "Count4" => "24")
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
            <h2 class="character-category">Wriothesley Showcase</h2>
            <div class="character-showcase" id="showcase">
                <lite-youtube videoid="qtLC02LKtKo" params="rel=0"></lite-youtube>
            </div>


            <!--character end-->
            <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
</div>
<?php get_footer('home1'); ?>