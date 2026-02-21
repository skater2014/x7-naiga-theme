<?php
/**
 * Template Name: ayaka-build.php
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
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9458790149381361"
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
        $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Ayaka.png';
        $image_alt = 'Ayaka';
        $image_classes = 'character-portrait rarity-5';

        echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
        ?>
        <!-- Character Header -->
        <div class="character-header">
          <div class="character-title">
            <h1 class="character-name">Genshin Impact Ayaka Build</h1> <img class="character-element"
              src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_cryo.png" alt="Cryo">
          </div>
          <div class="character-path"> <img class="character-path-icon"
              src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_sword.png"
              alt="Sword">Sword </div>
          <div class="character-role">Main DPS</div>
        </div>
        <!-- Character Materials -->
        <div class="character-materials">
          <?php
          $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/';

          $materials = array(
            'Elegance' => 'Elegance',
            'Bloodjade Branch' => 'Bloodjade Branch',
            'Shivada_Jade_Sliver' => 'Shivada Jade Sliver',
            'Perpetual_Heart' => 'Perpetual Heart',
            'Sakura_Bloom' => 'Sakura Bloom',
            'Old_Handguard' => 'Old Handguard',
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
            <!-- Ayaka Best Weapons -->
            <div class="character-build-section">
              <h2 class="character-build-section-title">Ayaka Best Weapons</h2>
              <div class="character-build-weapons">
                <?php
                $weapons = array(
                  array('Mistsplitter Reforged', 1, 5),
                  array('Haran Geppaku Futsu', 2, 5),
                  array('Light of Foliar Incision', 3, 5),
                  array('Summit Shaper', 4, 4),
                  array('Amenoma Kageuchi', 5, 5)
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

            <!-- Ayaka Best Artifacts -->
            <div class="character-build-section">
              <h2 class="character-build-section-title">Ayaka Best Artifacts</h2>

              <?php
              // アーティファクト名 ランク
              $artifacts = array(
                array('Blizzard Strayer', 1),
                array('Noblesse Oblige', 2),
                array('Emblem of Severed Fate', 3),
                array("Shimenawa's Reminiscence", 4),
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




          <!-- Ayaka Best Stats -->
          <div class="character-stats">
            <h2 class="character-stats-title">Ayaka Best Stats</h2>
            <div class="character-stats-item"><b>Sands:</b> ATK%</div>
            <div class="character-stats-item"><b>Goblet:</b> Cryo DMG</div>
            <div class="character-stats-item"><b>Circlet:</b> CRIT DMG / ATK%</div>
            <div class="character-stats-item full"><b>Substats:</b> CRIT DMG > ATK% > Energy Recharge</div>
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
                foreach ($Ayaka_Teams1 as $name => $info):
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
                $Ayaka_Teams2 = array(
                  "Ayaka" => array("element" => "cryo", "rarity" => "rarity-5"),
                  "Mona" => array("element" => "hydro", "rarity" => "rarity-5"),
                  "Diona" => array("element" => "cryo", "rarity" => "rarity-5"),
                  "Venti" => array("element" => "anemo", "rarity" => "rarity-5")
                );
                // キャラクター情報を出力
                foreach ($Ayaka_Teams2 as $name => $info):
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
                $Ayaka_Teams3 = array(
                  "Ayaka" => array("element" => "cryo", "rarity" => "rarity-5"),
                  "Furina" => array("element" => "hydro", "rarity" => "rarity-5"),
                  "Jean" => array("element" => "cryo", "rarity" => "rarity-5"),
                  "Shenhe" => array("element" => "anemo", "rarity" => "rarity-5")
                );
                // キャラクター情報を出力
                foreach ($Ayaka_Teams3 as $name => $info):
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
          // Ayakaの情報を格納する配列
          $AyakaInfo = array(
            "NormalAttack" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png",
              "title" => "Normal Attack",
              "name" => "Kamisato Art: Kabuki",
              "description" => "Normal Attack Performs up to 5 rapid strikes. Charged Attack Consumes a certain amount of Stamina to unleash a continuous stream of sword ki. Plunging Attack lunges from mid-air to strike the ground below, damaging enemies along the path and dealing AoE DMG upon impact."
            ),
            "ElementalSkill" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/talent_2.png",
              "title" => "Elemental Skill",
              "name" => "Kamisato Art: Hyouka",
              "description" => "Summons blooming ice to launch nearby opponents, dealing AoE Cryo DMG."
            ),
            "ElementalBurst" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/talent_3.png",
              "title" => "Elemental Burst",
              "name" => "Kamisato Art: Soumetsu",
              "description" => "Summons forth a snowstorm with flawless poise, unleashing a Frostflake Seki no To that moves forward continuously. Frostflake Seki no To A storm of whirling icy winds that slashes repeatedly at every enemy it touches, dealing Cryo DMG The snowstorm explodes after its duration ends, dealing AoE Cryo DMG."
            ),
            "RightClick" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/talent_4.png",
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
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/talent_4.png",
              "title" => "Ascension 1",
              "name" => "Amatsumi Kunitsumi Sanctification",
              "description" => "After using Kamisato Art: Hyouka, Kamisato Ayaka's Normal and Charged attacks deal 30% increased DMG for 6s."
            ),
            "Ascension4" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/talent_5.png",
              "title" => "Ascension 4",
              "name" => "Kanten Senmyou Blessing",
              "description" => "When the Cryo application at the end of Kamisato Art: Senho hits an opponent, Kamisato Ayaka gains the following effects: Restores 10 Stamina Gains 18% Cryo DMG Bonus for 10s."
            ),
            "Passive" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/talent_6.png",
              "title" => "Passive",
              "name" => "Fruits of Shinsa",
              "description" => "When Ayaka crafts Weapon Ascension Materials, she has a 10% chance to receive double the product."
            )
          );

          // Ayaka Constellationsの情報を格納する配列
          $constellationsInfo = array(
            "Constellation1" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/constellation_1.png",
              "title" => "Constellation 1",
              "name" => "Snowswept Sakura",
              "description" => "When Kamisato Ayaka's Normal or Charged Attacks deal Cryo DMG to opponents, it has a 50% chance of decreasing the CD of Kamisato Art: Hyouka by 0.3s. This effect can occur once every 0.1s."
            ),
            "Constellation2" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/constellation_2.png",
              "title" => "Constellation 2",
              "name" => "Blizzard Blade Seki no To",
              "description" => "When casting Kamisato Art: Soumetsu, unleashes 2 smaller additional Frostflake Seki no To, each dealing 20% of the original storm's DMG."
            ),
            "Constellation3" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/constellation_3.png",
              "title" => "Constellation 3",
              "name" => "Frostbloom Kamifubuki",
              "description" => "Increases the Level of Kamisato Art: Soumetsu by 3. Maximum upgrade level is 15."
            ),
            "Constellation4" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/constellation_4.png",
              "title" => "Constellation 4",
              "name" => "Ebb and Flow",
              "description" => "Opponents damaged by Kamisato Art: Soumetsu's Frostflake Seki no To will have their DEF decreased by 30% for 6s."
            ),
            "Constellation5" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/constellation_5.png",
              "title" => "Constellation 5",
              "name" => "Blossom Cloud Irutsuki",
              "description" => "Increase the Level of Kamisato Art: Hyouka by 3. Maximum upgrade level is 15."
            ),
            "Constellation6" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/constellation_6.png",
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
                'description' => 'Normal Attack　Performs up to 5 rapid strikes. Charged Attack Consumes a certain amount of Stamina to unleash a continuous stream of sword ki.Plunging Attack lunges from mid-air to strike the ground below, damaging enemies along the path and dealing AoE DMG upon impact..'
              ),
              array(
                'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/talent_2.png',
                'title' => 'Elemental Skill',
                'name' => 'Kamisato Art: Soumetsu',
                'description' => 'Summons forth a snowstorm with flawless poise, unleashing a Frostflake Seki no To that moves forward continuously.Frostflake Seki no To A storm of whirling icy winds that slashes repeatedly at every enemy it touches, dealing Cryo DMG The snowstorm explodes after its duration ends, dealing AoE Cryo DMG..'
              ),
              array(
                'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/talent_2.png',
                'title' => 'Elemental Burst',
                'name' => 'Kamisato Art: Soumetsu',
                'description' => 'Ayaka consumes Stamina and cloaks herself in a frozen fog that moves with her. In Senho form, she moves swiftly upon water. When she reappears, the following effects occur: Ayaka unleashes frigid energy to apply Cryo on nearby opponents. Coldness condenses around Ayaka\'s blade, infusing her attacks with Cryo for a brief period.'
              ),
              array(
                'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayaka/talent_4.png',
                'title' => 'Right Click',
                'name' => 'Kamisato Art: Senho',
                'description' => 'Ayaka consumes Stamina and cloaks herself in a frozen fog that moves with her. In Senho form, she moves swiftly upon water. When she reappears, the following effects occur: Ayaka unleashes frigid energy to apply Cryo on nearby opponents. Coldness condenses around Ayaka\'s blade, infusing her attacks with Cryo for a brief period.'
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
          // Ayaka Constellationsの情報を出力
          ?>
          <div class="character-skills" id="constellations">
            <h2 class="character-category">Ayaka Constellations</h2>
            <?php foreach ($constellationsInfo as $constellation): ?>
              <div class="character-skill">
                <div class="character-skill-header"><img class="character-skill-icon"
                    src="<?php echo $constellation['icon']; ?>" alt="<?php echo $constellation['name']; ?>">
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
            <h2 class="character-category">Ayaka Talents</h2>
            <?php foreach ($AyakaInfo as $skill): ?>
              <div class="character-skill">
                <div class="character-skill-header"><img class="character-skill-icon" src="<?php echo $skill['icon']; ?>"
                    alt="<?php echo $skill['name']; ?>">
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
            <h2 class="character-category">Ayaka Ascension Costs</h2>
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
                <div class="rt-tbody" style="min-width: 1200px;">
                  <!--Table Data-->
                  <div class="rt-tbody" style="min-width: 1200px;">
                    <?php
                    // アヤカの昇華アイテムの情報
                    $ascensionItems = array(
                      array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Shivada Jade Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Sakura Bloom", "Count3" => "3", "Material4" => "Old Handguard", "Count4" => "3"),
                      array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Shivada Jade Fragment", "Count1" => "3", "Material2" => "Perpetual Heart", "Count2" => "2", "Material3" => "Sakura Bloom", "Count3" => "10", "Material4" => "Old Handguard", "Count4" => "15"),
                      array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Shivada Jade Fragment", "Count1" => "6", "Material2" => "Perpetual Heart", "Count2" => "4", "Material3" => "Sakura Bloom", "Count3" => "20", "Material4" => "Kageuchi Handguard", "Count4" => "12"),
                      array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Shivada Jade Chunk", "Count1" => "3", "Material2" => "Perpetual Heart", "Count2" => "8", "Material3" => "Sakura Bloom", "Count3" => "30", "Material4" => "Kageuchi Handguard", "Count4" => "18"),
                      array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Shivada Jade Chunk", "Count1" => "6", "Material2" => "Perpetual Heart", "Count2" => "12", "Material3" => "Sakura Bloom", "Count3" => "45", "Material4" => "Famed Handguard", "Count4" => "12"),
                      array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Shivada Jade Gemstone", "Count1" => "6", "Material2" => "Perpetual Heart", "Count2" => "20", "Material3" => "Sakura Bloom", "Count3" => "60", "Material4" => "Famed Handguard", "Count4" => "24")
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
                            $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/ayaka/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
                            ?>
                            <div class="rt-td" role="gridcell" style="flex: 150 0 auto; width: 150px;">
                              <?php if ($material != ""): ?>
                                <div class="table-image-wrapper">
                                  <img class="table-image" src="<?= $materialUrl; ?>" alt="<?= $material; ?>">
                                  <span class="table-image-count"><?= $ascensionItem[$countKey]; ?></span>
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
              </div>
            </div>
          </div>




        </div>
        <!--character team-->
        <h2 class="character-category">Ayaka Showcase</h2>
        <div class="character-showcase" id="showcase">
          <lite-youtube videoid="7A1lmUjOrZc" params="rel=0"></lite-youtube>
        </div>


        <!--character end-->
        <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
  </main>
</div>
<?php get_footer('home1'); ?>