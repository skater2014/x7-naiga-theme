<?php
/**
 * Template Name: Genshin Impact Character Build
 * Description: Template for displaying Genshin Impact character builds AYAKA.
 */

get_header(); ?>
  <style>
    @media only screen and (min-width: 991px) {
        body {
            width: 1080px;
            padding: 0px 15px;
            margin: 0px auto;
        }
    }
  </style>
  <div class="row">
    <main class="content">
      <div class="character">
        <!-- Character Intro Section -->
        <div class="character-intro">
          <!-- Character Image -->
          <?php
                $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin.png';
                $image_alt = 'Ayaka';
                $image_classes = 'character-portrait rarity-5';

                echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
                ?>
            <!-- Character Header -->
            <div class="character-header">
              <div class="character-title">
                <h1 class="character-name">Genshin Impact Ayaka Build</h1> <img class="character-element" src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Elements/Element_Cryo.png" alt="Cryo"> </div>
              <div class="character-path"> <img class="character-path-icon" src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_sword.png" alt="Sword">Sword </div>
              <div class="character-role">Main DPS</div>
            </div>
           <!-- Character Materials -->
          <div class="character-materials">
              <?php
              $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/';

              $materials = array(
                  'Shivada_Jade_Sliver' => 'Shivada Jade Sliver',
                  'Perpetual_Heart' => 'Perpetual Heart',
                  'Sakura_Bloom' => 'Sakura Bloom',
                  'Old_Handguard' => 'Old Handguard',
              );

              foreach ($materials as $image_filename => $material_name) :
                  $image_file_path = $image_base_path . $image_filename . '.png';
              ?>
                  <div class="character-materials-item">
                      <img class="character-materials-icon" src="<?php echo esc_url($image_file_path); ?>" alt="<?php echo esc_attr($material_name); ?>">
                      <div class="character-materials-name">
                          <?php echo esc_html($material_name); ?>
                      </div>
                  </div>
              <?php endforeach; ?>
          </div>
          <!-- Character Build Section -->

            <div class="character-build">
              <!-- Ayaka Best Weapons -->
              <div class="character-build-section">
                <h2 class="character-build-section-title">Ayaka Best Weapons</h2>
                <?php
                        $weapons = array(
                            array('Mistsplitter Reforged', 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Mistsplitter_Reforged.png'),
                            array('Haran Geppaku Futsu', 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Haran_Geppaku_Futsu.png'),
                            array('Light of Foliar Incision', 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Light_of_Foliar_Incision.png'),
                            array('Summit Shaper', 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Summit_Shaper.png'),
                            array('Amenoma Kageuchi', 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/Amenoma_Kageuchi.png')
                        );

                        foreach ($weapons as $weapon) :
                        ?>
                  <div class="character-build-weapon">
                    <div class="character-build-weapon-rank">1</div> <img class="character-build-weapon-icon rarity-5" src="<?php echo esc_url($weapon[1]); ?>" alt="<?php echo esc_attr($weapon[0]); ?>">
                    <div class="character-build-weapon-name">
                      <?php echo esc_html($weapon[0]); ?> </div>
                  </div>
                  <?php endforeach; ?> </div>
              <!-- Ayaka Best Artifacts -->
              <div class="character-build-section">
                <h2 class="character-build-section-title">Ayaka Best Artifacts</h2>
                <?php
                        $artifacts = array(
                            array('Blizzard Strayer', 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/blizzard_strayer.png', 4),
                            array('Blizzard Strayer', 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/blizzard_strayer.png', 2),
                            array("Shimenawa's Reminiscence", 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/shimenawa\'s_reminiscence.png', 2),
                        );

                        foreach ($artifacts as $artifact) :
                        ?>
                  <div class="character-build-weapon">
                    <div class="character-build-weapon-rank">
                      <?php echo esc_html($artifact[2]); ?> </div>
                    <div class="character-build-weapon-content <?php echo ($artifact[2] === 4) ? 'full' : ''; ?>"> <img class="character-build-weapon-icon rarity-5" src="<?php echo esc_url($artifact[1]); ?>" alt="<?php echo esc_attr($artifact[0]); ?>">
                      <div class="character-build-weapon-name">
                        <?php echo esc_html($artifact[0]); ?> </div>
                      <div class="character-build-weapon-count">
                        <?php echo esc_html($artifact[2]); ?> </div>
                    </div>
                  </div>
                  <?php endforeach; ?> </div>
            </div>
            <!-- Character Stats -->
            <div class="character-stats">
              <h2 class="character-stats-title">Ayaka Best Stats</h2>
              <div class="character-stats-item"><b>Sands:</b> ATK%</div>
              <div class="character-stats-item"><b>Goblet:</b> Cryo DMG</div>
              <div class="character-stats-item"><b>Circlet:</b> CRIT DMG / ATK%</div>
              <div class="character-stats-item full"><b>Substats:</b> CRIT DMG > ATK% > Energy Recharge</div>
            </div>
            <!-- Character Credit Link --><a href="https://docs.google.com/spreadsheets/d/e/2PACX-1vRq-sQxkvdbvaJtQAGG6iVz2q2UN9FCKZ8Mkyis87QHFptcOU3ViLh0_PJyMxFSgwJZrd10kbYpQFl1/pubhtml#" target="_blank" class="character-credit">Character Builds by Genshin Impact Helper →</a> </div>
        <div class="wrapper-lb1">
          <div id="nn_lb1" data-google-query-id="COHK6NXXo4MDFZXTFgUd6x0GDA">
            <div id="google_ads_iframe_6928793,21828116410/Genshin.GG-60892aa72813e/Genshin.GG-LB1-60892d2613552_1__container__" style="border: 0pt none; display: inline-block; width: 970px; height: 90px;"><iframe frameborder="0" src="https://df6d9c8fd84dae25b30d85f52fa1b953.safeframe.googlesyndication.com/safeframe/1-0-40/html/container.html" id="google_ads_iframe_6928793,21828116410/Genshin.GG-60892aa72813e/Genshin.GG-LB1-60892d2613552_1" title="3rd party ad content" name="" scrolling="no" marginwidth="0" marginheight="0" width="970" height="90" data-is-safeframe="true" sandbox="allow-forms allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts allow-top-navigation-by-user-activation" allow="attribution-reporting" role="region" aria-label="Advertisement" tabindex="0" data-google-container-id="2" style="border: 0px; vertical-align: bottom;" data-load-complete="true"></iframe></div>
          </div>
        </div>
        <div class="wrapper-mpu1" style="margin-bottom: 0px;">
          <div id="nn_mobile_mpu2"></div>
        </div>
        <!-- Character Navigation -->
        <div class="character-navigation"> <a class="character-navigation-link">Teams</a> <a class="character-navigation-link">Character Teams</a>
          <!-- Character Teams Section -->
        </div>
        <div class="character-teams" id="teams">
          <h2 class="character-category">Best Ayaka Teams</h2>
          <!-- Ayaka Freeze Team -->
          <div class="character-team">
            <div class="character-team-name">Ayaka Freeze
              <div class="character-team-characters">
                <?php
                                // 既存のキャラクター情報を取得
                                //$characters = get_genshin_characters();

                                // 欲しいキャラクターの情報だけを取得
                                $Ayaka_Teams1 = array(
                                    "Ayaka" => array("element" => "cryo", "rarity" => "rarity-5"),
                                    "Shenhe" => array("element" => "cryo", "rarity" => "rarity-5"),
                                    "Kokomi" => array("element" => "hydro", "rarity" => "rarity-5"),
                                    "Kazuha" => array("element" => "anemo", "rarity" => "rarity-5")
                                );
                                // キャラクター情報を出力
                                foreach ($Ayaka_Teams1 as $name => $info) :
                                    ?>
                  <div class="character-portrait character-teams"> <a href="https://kaztokyo.sakura.ne.jp/genshin-impact-blog-<?php echo strtolower($name); ?>-best-build">
                                            <img class="character-icon <?php echo $info['rarity']; ?>" src="<?php echo get_template_directory_uri(); ?>/images/genshin/<?php echo $name; ?>.png" width="70px" height="70px" alt="<?php echo $name; ?>">
                                            <img class="character-type" src="<?php echo get_template_directory_uri(); ?>/images/genshin/element_<?php echo $info['element']; ?>.png" width="24px" height="24px" alt="<?php echo $info['element']; ?>">
                                            <div class="character-name"><?php echo $name; ?></div>
                                        </a> </div>
                  <?php endforeach; ?> </div>
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
                                $Ayaka_Teams2 = array(
                                    "Ayaka" => array("element" => "cryo", "rarity" => "rarity-5"),
                                    "Mona" => array("element" => "hydro", "rarity" => "rarity-5"),
                                    "Diona" => array("element" => "cryo", "rarity" => "rarity-5"),
                                    "Venti" => array("element" => "anemo", "rarity" => "rarity-5")
                                );
                                // キャラクター情報を出力
                                foreach ($Ayaka_Teams2 as $name => $info) :
                                    ?>
                  <div class="character-portrait"> <a href="https://kaztokyo.sakura.ne.jp/genshin-impact-blog-<?php echo strtolower($name); ?>-best-build">
                                            <img class="character-icon <?php echo $info['rarity']; ?>" src="<?php echo get_template_directory_uri(); ?>/images/genshin/<?php echo $name; ?>.png" width="70px" height="70px" alt="<?php echo $name; ?>">
                                            <img class="character-type" src="<?php echo get_template_directory_uri(); ?>/images/genshin/element_<?php echo $info['element']; ?>.png" width="24px" height="24px" alt="<?php echo $info['element']; ?>">
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
                            $Ayaka_Teams3 = array(
                                "Ayaka" => array("element" => "cryo", "rarity" => "rarity-5"),
                                "Furina" => array("element" => "hydro", "rarity" => "rarity-5"),
                                "Jean" => array("element" => "cryo", "rarity" => "rarity-5"),
                                "Shenhe" => array("element" => "anemo", "rarity" => "rarity-5")
                            );
                            // キャラクター情報を出力
                            foreach ($Ayaka_Teams3 as $name => $info) :
                                ?>
                  <div class="character-portrait"> <a href="https://kaztokyo.sakura.ne.jp/genshin-impact-blog-<?php echo strtolower($name); ?>-best-build">
                                        <img class="character-icon <?php echo $info['rarity']; ?>" src="<?php echo get_template_directory_uri(); ?>/images/genshin/<?php echo $name; ?>.png" width="70px" height="70px" alt="<?php echo $name; ?>">
                                        <img class="character-type" src="<?php echo get_template_directory_uri(); ?>/images/genshin/element_<?php echo $info['element']; ?>.png" width="24px" height="24px" alt="<?php echo $info['element']; ?>">
                                        <div class="character-name"><?php echo $name; ?></div>
                                    </a> </div>
                  <?php endforeach; ?>
                  <!-- Add character information as needed -->
              </div>
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
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/talent_2.png",
                            "title" => "Elemental Skill",
                            "name" => "Kamisato Art: Hyouka",
                            "description" => "Summons blooming ice to launch nearby opponents, dealing AoE Cryo DMG."
                        ),
                        "ElementalBurst" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/talent_3.png",
                            "title" => "Elemental Burst",
                            "name" => "Kamisato Art: Soumetsu",
                            "description" => "Summons forth a snowstorm with flawless poise, unleashing a Frostflake Seki no To that moves forward continuously. Frostflake Seki no To A storm of whirling icy winds that slashes repeatedly at every enemy it touches, dealing Cryo DMG The snowstorm explodes after its duration ends, dealing AoE Cryo DMG."
                        ),
                        "RightClick" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/talent_4.png",
                            "title" => "Right Click",
                            "name" => "Kamisato Art: Senho",
                            "description" => "Ayaka consumes Stamina and cloaks herself in a frozen fog that moves with her. In Senho form, she moves swiftly upon water. When she reappears, the following effects occur: Ayaka unleashes frigid energy to apply Cryo on nearby opponents. Coldness condenses around Ayaka's blade, infusing her attacks with Cryo for a brief period."
                        )
                    );

                    // 各情報を出力
                    ?>
            <?php
                    // Ayaka Passivesの情報を格納する配列
                    $passivesInfo = array(
                        "Ascension1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/talent_4.png",
                            "title" => "Ascension 1",
                            "name" => "Amatsumi Kunitsumi Sanctification",
                            "description" => "After using Kamisato Art: Hyouka, Kamisato Ayaka's Normal and Charged attacks deal 30% increased DMG for 6s."
                        ),
                        "Ascension4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/talent_5.png",
                            "title" => "Ascension 4",
                            "name" => "Kanten Senmyou Blessing",
                            "description" => "When the Cryo application at the end of Kamisato Art: Senho hits an opponent, Kamisato Ayaka gains the following effects: Restores 10 Stamina Gains 18% Cryo DMG Bonus for 10s."
                        ),
                        "Passive" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/talent_6.png",
                            "title" => "Passive",
                            "name" => "Fruits of Shinsa",
                            "description" => "When Ayaka crafts Weapon Ascension Materials, she has a 10% chance to receive double the product."
                        )
                    );

                    // Ayaka Constellationsの情報を格納する配列
                    $constellationsInfo = array(
                        "Constellation1" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/constellation_1.png",
                            "title" => "Constellation 1",
                            "name" => "Snowswept Sakura",
                            "description" => "When Kamisato Ayaka's Normal or Charged Attacks deal Cryo DMG to opponents, it has a 50% chance of decreasing the CD of Kamisato Art: Hyouka by 0.3s. This effect can occur once every 0.1s."
                        ),
                        "Constellation2" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/constellation_2.png",
                            "title" => "Constellation 2",
                            "name" => "Blizzard Blade Seki no To",
                            "description" => "When casting Kamisato Art: Soumetsu, unleashes 2 smaller additional Frostflake Seki no To, each dealing 20% of the original storm's DMG."
                        ),
                        "Constellation3" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/constellation_3.png",
                            "title" => "Constellation 3",
                            "name" => "Frostbloom Kamifubuki",
                            "description" => "Increases the Level of Kamisato Art: Soumetsu by 3. Maximum upgrade level is 15."
                        ),
                        "Constellation4" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/constellation_4.png",
                            "title" => "Constellation 4",
                            "name" => "Ebb and Flow",
                            "description" => "Opponents damaged by Kamisato Art: Soumetsu's Frostflake Seki no To will have their DEF decreased by 30% for 6s."
                        ),
                        "Constellation5" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/constellation_5.png",
                            "title" => "Constellation 5",
                            "name" => "Blossom Cloud Irutsuki",
                            "description" => "Increase the Level of Kamisato Art: Hyouka by 3. Maximum upgrade level is 15."
                        ),
                        "Constellation6" => array(
                            "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/constellation_6.png",
                            "title" => "Constellation 6",
                            "name" => "Dance of Suigetsu",
                            "description" => "Kamisato Ayaka gains Usurahi Butou every 10s, increasing her Charged Attack DMG by 298%. This buff will be cleared 0.5s after Ayaka's Charged ATK hits an opponent, after which the timer for this ability will restart."
                        )
                    );
                    ?>

                      <div class="character-skills" id="talents">
                        <h2 class="character-category">Ayaka Talents</h2>
                        
                        <?php
                        // 配列でタレント情報を定義
                        $talents = array(
                          array(
                            'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png',
                            'title' => 'Normal Attack',
                            'name' => 'Kamisato Art: Kabuki',
                            'description' => 'Normal Attack Performs up to 5 rapid strikes. Charged Attack Consumes a certain amount of Stamina to unleash a continuous stream of sword ki. Plunging Attack lunges from mid-air to strike the ground below, damaging enemies along the path and dealing AoE DMG upon impact.'
                          ),
                          array(
                            'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/talent_2.png',
                            'title' => 'Elemental Skill',
                            'name' => 'Kamisato Art: Hyouka',
                            'description' => 'Summons blooming ice to launch nearby opponents, dealing AoE Cryo DMG.'
                          ),
                          array(
                            'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/talent_3.png',
                            'title' => 'Elemental Burst',
                            'name' => 'Kamisato Art: Soumetsu',
                            'description' => 'Summons forth a snowstorm with flawless poise, unleashing a Frostflake Seki no To that moves forward continuously. Frostflake Seki no To A storm of whirling icy winds that slashes repeatedly at every enemy it touches, dealing Cryo DMG The snowstorm explodes after its duration ends, dealing AoE Cryo DMG.'
                          ),
                          array(
                            'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/talent_4.png',
                            'title' => 'Right Click',
                            'name' => 'Kamisato Art: Senho',
                            'description' => 'Ayaka consumes Stamina and cloaks herself in a frozen fog that moves with her. In Senho form, she moves swiftly upon water. When she reappears, the following effects occur: Ayaka unleashes frigid energy to apply Cryo on nearby opponents. Coldness condenses around Ayaka\'s blade, infusing her attacks with Cryo for a brief period.'
                          )
                        );
                        
                        // タレント情報をループで表示
                        foreach ($talents as $talent) :
                        ?>
                          <div class="character-skill">
                            <div class="character-skill-header">
                              <img class="character-skill-icon" src="<?php echo esc_url($talent['icon']); ?>" alt="<?php echo esc_attr($talent['title']); ?>">
                              <h3 class="character-skill-title"><?php echo esc_html($talent['title']); ?></h3>
                            </div>
                            <div class="character-skill-body">
                              <h2 class="character-skill-name"><?php echo esc_html($talent['name']); ?></h2>
                              <div class="character-skill-description"><?php echo esc_html($talent['description']); ?></div>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>


              <?php
                    // Ayaka Passivesの情報を出力
                    ?>
                <div class="character-skills" id="passives">
                  <h2 class="character-category">Ayaka Passives</h2>
                  <?php foreach ($passivesInfo as $passive) : ?>
                  <div class="character-skill">
                    <div class="character-skill-header"><img class="character-skill-icon" src="<?php echo $passive['icon']; ?>" alt="<?php echo $passive['name']; ?>">
                      <h2 class="character-skill-title">
                        <?php echo $passive['title']; ?> </h2>
                    </div>
                    <div class="character-skill-body">
                      <h2 class="character-skill-name">
                        <?php echo $passive['name']; ?> </h2>
                      <div class="character-skill-description">
                        <?php echo $passive['description']; ?> </div>
                    </div>
                  </div>
                  <?php endforeach; ?> </div>
                <?php
                    // Ayaka Constellationsの情報を出力
                    ?>
                  <div class="character-skills" id="constellations">
                    <h2 class="character-category">Ayaka Constellations</h2>
                    <?php foreach ($constellationsInfo as $constellation) : ?>
                    <div class="character-skill">
                      <div class="character-skill-header"><img class="character-skill-icon" src="<?php echo $constellation['icon']; ?>" alt="<?php echo $constellation['name']; ?>">
                        <h2 class="character-skill-title">
                          <?php echo $constellation['title']; ?> </h2>
                      </div>
                      <div class="character-skill-body">
                        <h2 class="character-skill-name">
                          <?php echo $constellation['name']; ?> </h2>
                        <div class="character-skill-description">
                          <?php echo $constellation['description']; ?> </div>
                      </div>
                    </div>
                    <?php endforeach; ?> </div>
                  <div class="character-skills" id="talents">
                    <h2 class="character-category">Ayaka Talents</h2>
                    <?php foreach ($AyakaInfo as $skill) : ?>
                    <div class="character-skill">
                      <div class="character-skill-header"><img class="character-skill-icon" src="<?php echo $skill['icon']; ?>" alt="<?php echo $skill['name']; ?>">
                        <h2 class="character-skill-title">
                          <?php echo $skill['title']; ?> </h2>
                      </div>
                      <div class="character-skill-body">
                        <h2 class="character-skill-name">
                          <?php echo $skill['name']; ?> </h2>
                        <div class="character-skill-description">
                          <?php echo $skill['description']; ?> </div>
                      </div>
                    </div>
                    <?php endforeach; ?> </div>
                  <div class="character-ascension" id="ascension">
                    <h2 class="character-category">Ayaka Ascension Costs</h2>
                    <div class="ReactTable table">
                      <div class="rt-body" role="grid">
                        <?php echo do_shortcode('[table id=38 /]'); ?> </div>
                    </div>
                  </div>
                  <div class="-loading">
                    <div class="-loading-inner">Loading...</div>
                  </div>
        </div>
        <!--character team-->
        <h2 class="character-category">Ayaka Showcase</h2>
        <div class="character-showcase" id="showcase">
         <iframe class="character-showcase-video" frameborder="0" src="https://www.youtube.com/embed/FUWG8z4hqEg"></iframe>
        </div>


      <!--character end-->
      <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
    </main>
  </div>
  <?php get_footer(); ?>