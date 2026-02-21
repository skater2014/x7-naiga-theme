<?php
/**
 * Template Name: chiori-build.php
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
        $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Chiori.png';
        $image_alt = 'Chiori';
        $image_classes = 'character-portrait rarity-5';

        echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
        ?>
        <!-- Character Header -->
        <div class="character-header">
          <div class="character-title">
            <h1 class="character-name">Genshin Impact Chiori Build</h1> <img class="character-element"
              src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_geo.png" alt="Geo">
          </div>
          <div class="character-path"> <img class="character-path-icon"
              src="https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/weapon_sword.png"
              alt="Sword">Sword </div>
          <div class="character-role">Main DPS</div>
        </div>
        <!-- Character Materials -->
        <div class="character-materials">
          <?php
          $image_base_path = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/farming/';

          $materials = array(
            'Light' => 'Light',
            'Lightless Silk String' => 'Lightless Silk String',
            'Prithiva Topaz Sliver' => 'Prithiva Topaz Sliver',
            'Dendrobium' => 'Dendrobium',
            'Spectral Husk' => 'Spectral Husk',
            'Artificed Spare Clockwork Component — Coppelia' => 'Artificed Spare Clockwork Component — Coppelia',
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
            <!-- Chiori Best Weapons -->
            <div class="character-build-section">
              <h2 class="character-build-section-title">Chiori Best Weapons</h2>
              <div class="character-build-weapons">
                <?php
                $weapons = array(
                  array('Uraku Misugiri', 1, 5),
                  array('Harbinger of Dawn', 2, 3),
                  array('Cinnabar Spindle', 3, 4),
                  array('Wolf-Fang', 4, 4),
                  array('Primordial Jade Cutter', 5, 5)
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

            <!-- Chiori Best Artifacts -->
            <div class="character-build-section">
              <h2 class="character-build-section-title">Chiori Best Artifacts</h2>

              <?php
              $artifacts = array(
                array('Golden Troupe', 1),
                array('Husk of Opulent Dreams', 2),
                array("Archaic Petra", 3),
                array("Husk of Opulent Dreams", 3),
                array("Archaic Petra", 4),
                array("Golden Troupe", 4),
                array("Noblesse Oblige", 5),
              );

              $manualRanks = array(4, 4, 2, 2, 2, 2, 4);

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




          <!-- Chiori Best Stats -->
          <div class="character-stats">
            <h2 class="character-stats-title">Chiori Best Stats</h2>
            <div class="character-stats-item"><b>Sands:</b> DEF%</div>
            <div class="character-stats-item"><b>Goblet:</b> Geo DMG/DEF%</div>
            <div class="character-stats-item"><b>Circlet:</b> CRIT Rate/CRIT DMG/DEF%</div>
            <div class="character-stats-item full"><b>Substats:</b> CRIT Rate/ CRIT DMG > DEF% > ATK</div>
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
          <h2 class="character-category">Best Chiori Teams</h2>
          <!-- Chiori Freeze Team -->
          <div class="character-team">
            <div class="character-team-name">Chiori Suoer Geo Teams
              <div class="character-team-characters">
                <?php
                // 既存のキャラクター情報を取得
                //$characters = get_genshin_characters();
                
                // 欲しいキャラクターの情報だけを取得
                $Chiori_Teams1["Chiori"] = array("element" => "geo", "rarity" => "rarity-5");
                $Chiori_Teams1["Itto"] = array("element" => "geo", "rarity" => "rarity-5");
                $Chiori_Teams1["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                $Chiori_Teams1["Gorou"] = array("element" => "geo", "rarity" => "rarity-4");
                // キャラクター情報を出力
                foreach ($Chiori_Teams1 as $name => $info):
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

          <!-- Chiori/Ganyu Mono Cryo Team -->
          <div class="character-team">
            <div class="character-team-name">Chiori/Raiden National Teams
              <div class="character-team-characters">
                <?php
                // 既存のキャラクター情報を取得
                //$characters = get_genshin_characters();
                
                // 欲しいキャラクターの情報だけを取得
                $Chiori_Teams2["Chiori"] = array("element" => "geo", "rarity" => "rarity-5");
                $Chiori_Teams2["Raiden"] = array("element" => "electro", "rarity" => "rarity-5");
                $Chiori_Teams2["Jean"] = array("element" => "anemo", "rarity" => "rarity-5");
                $Chiori_Teams2["Bennett"] = array("element" => "dendro", "rarity" => "rarity-5");

                // キャラクター情報を出力
                foreach ($Chiori_Teams2 as $name => $info):
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
          <!-- Chiori/Ganyu Furina Hydro Team -->
          <div class="character-team">
            <div class="character-team-name">Chiori/Geo Offensive Teams
              <div class="character-team-characters">
                <?php
                // 既存のキャラクター情報を取得
                //$characters = get_genshin_characters();
                
                // 欲しいキャラクターの情報だけを取得
                $Chiori_Teams3["Chiori"] = array("element" => "geo", "rarity" => "rarity-5");
                $Chiori_Teams3["Hu Tao"] = array("element" => "pyro", "rarity" => "rarity-5");
                $Chiori_Teams3["Yelan"] = array("element" => "hydro", "rarity" => "rarity-5");
                $Chiori_Teams3["Zhongli"] = array("element" => "dendro", "rarity" => "rarity-5");
                // キャラクター情報を出力
                foreach ($Chiori_Teams3 as $name => $info):
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
          // Chioriの情報を格納する配列
          $ChioriInfo = array(
            "NormalAttack" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png",
              "title" => "Normal Attack",
              "name" => "Weaving Blade",
              "description" => "Normal Attack Performs up to 5 rapid strikes. Charged Attack Consumes a certain amount of Stamina to unleash a continuous stream of sword ki. Plunging Attack lunges from mid-air to strike the ground below, damaging enemies along the path and dealing AoE DMG upon impact."
            ),
            "ElementalSkill" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/chiori/talent_2.png",
              "title" => "Elemental Skill",
              "name" => "Fluttering Hasode",
              "description" => "Dashes nimbly forward with silken steps. Once this dash ends, Chiori will summon the automaton doll \"Tamoto\" beside her and sweep her blade upward, dealing AoE Geo DMG to nearby opponents based on her ATK and DEF. Holding the Skill will cause it to behave differently.

                            Hold
                            Enter Aiming Mode to adjust the dash direction.

                            Tamoto
                            Will slash at nearby opponents at intervals, dealing AoE Geo DMG based on Chiori's ATK and DEF. While active, if there are nearby Geo Construct(s) or Geo Construct(s) are created nearby, an additional Tamoto will be summoned next to your active character. Only 1 additional Tamoto can be summoned in this manner, and its duration is independently counted."
            ),

            "ElementalBurst" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/chiori/talent_3.png",
              "title" => "Elemental Burst",
              "name" => "Hiyoku: Twin Blades",
              "description" => "Twin blades leave their sheaths as Chiori slices with the clean cuts of a master tailor, dealing AoE Geo DMG based on her ATK and DEF."
            ),
          );

          // 各情報を出力
          ?>
          <?php
          // Chiori Passivesの情報を格納する配列
          $passivesInfo = array(
            "Ascension1" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/chiori/talent_4.png",
              "title" => "Ascension 1",
              "name" => "Tailor-Made",
              "description" => "Gain different effects depending on the next action you take within a short duration after using Fluttering Hasode's upward sweep. If you (Press/Tap) the Elemental Skill, you will trigger the Tapestry effect. If you (Press/Tap) your Normal Attack, the Tailoring effect will be triggered instead

                        Tapestry
                        Switches to the next character in your roster.
                        Grants all your party members \"Seize the Moment\": When your active party member's Normal Attacks, Charged Attacks, and Plunging Attacks hit a nearby opponent, \"Tamoto\" will execute a coordinated attack, dealing 100% of Fluttering Hasode's upward sweep DMG as AoE Geo DMG at the opponent's location. DMG dealt this way is considered Elemental Skill DMG.
                        \"Seize the Moment\" lasts 8s, and 1 of \"Tamoto\"'s coordinated attack can be unleashed every 2s. 2 such coordinated attacks can occur per \"Seize the Moment\" effect duration.

                        Tailoring
                        Chiori gains Geo infusion for 5s.

                        When on the field, if Chiori does not either (Press/Tap) her Elemental Skill or use a Normal Attack within a short time after using Fluttering Hasode's upward sweep, the Tailoring effect will be triggered by default."
            ),

            "Ascension4" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/chiori/talent_5.png",
              "title" => "Ascension 4",
              "name" => "The Finishing Touch",
              "description" => "When a nearby party member creates a Geo Construct, Chiori will gain 20% Geo DMG Bonus for 20s."
            ),
            "Passive" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/chiori/talent_6.png",
              "title" => "Passive",
              "name" => "Brocaded Collar's Beauteous Silhouette",
              "description" => "When any party member is wearing an outfit apart from their default outfit, or is wearing a wind glider other than the Wings of First Flight, your party members will obtain the Swift Stride effect: Movement SPD is increased by 10%. This effect does not take effect in Domains, Trounce Domains and the Spiral Abyss. Swift Stride does not stack."
            )
          );

          // Chiori Constellationsの情報を格納する配列
          $constellationsInfo = array(
            "Constellation1" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/chiori/constellation_1.png",
              "title" => "Constellation 1",
              "name" => "Six Paths of Sage Silkcraft",
              "description" => "The AoE of the automaton doll \"Tamoto\" summoned by Fluttering Hasode is increased by 50%.
                        Additionally, if there is a Geo party member other than Chiori, Fluttering Hasode will trigger the following after the dash is completed:
                        Summon an additional Tamoto. Only one additional Tamoto can exist at the same time, whether summoned by Chiori this way or through the presence of a Geo Construct.
                        Triggers the Passive Talent \"The Finishing Touch.\" This effect requires you to first unlock the Passive Talent \"The Finishing Touch.\"."
            ),

            "Constellation2" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/chiori/constellation_2.png",
              "title" => "Constellation 2",
              "name" => "In Five Colors Dyed",
              "description" => "For 10s after using Hiyoku: Twin Blades, a simplified automaton doll, \"Kinu,\" will be summoned next to your active character every 3s. Kinu will attack nearby opponents, dealing AoE Geo DMG equivalent to 170% of Tamoto's DMG. DMG dealt this way is considered Elemental Skill DMG."
            ),
            "Constellation3" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/chiori/constellation_3.png",
              "title" => "Constellation 3",
              "name" => "Four Brocade Embellishments",
              "description" => "Increases the Level of Fluttering Hasode by 3.Maximum upgrade level is 15."
            ),
            "Constellation4" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/chiori/constellation_4.png",
              "title" => "Constellation 4",
              "name" => "A Tailor's Three Courtesies",
              "description" => "For 8s after triggering either follow-up effect of the Passive Talent \"Tailor-Made,\" when your current active character's Normal, Charged, or Plunging Attacks hit a nearby opponent, a simplified automaton doll, \"Kinu,\" will be summoned near this opponent. You can summon 1 Kinu every 1s in"
            ),
            "Constellation5" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/chiori/constellation_5.png",
              "title" => "Constellation 5",
              "name" => "Two Silken Plumules",
              "description" => "Increases the Level of Hiyoku: Twin Blades by 3.Maximum upgrade level is 15."
            ),
            "Constellation6" => array(
              "icon" => "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/chiori/constellation_6.png",
              "title" => "Constellation 6",
              "name" => "Sole Principle Pursuit",
              "description" => "After triggering a follow-up effect of the Passive Talent \"Tailor-Made,\" Chiori's own Fluttering Hasode's CD is decreased by 12s. Must unlock the Passive \"Tailor-Made\" first.
                            In addition, the DMG dealt by Chiori's own Normal Attacks is increased by an amount equal to 235% of her own DEF."
            ),
          );
          ?>

          <?php
          // Chiori Passivesの情報を出力
          ?>
          <div class="character-skills" id="passives">
            <h2 class="character-category">Chiori Passives</h2>
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
          // Chiori Constellationsの情報を出力
          ?>
          <div class="character-skills" id="constellations">
            <h2 class="character-category">Chiori Constellations</h2>
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
            <h2 class="character-category">Chiori Talents</h2>
            <?php foreach ($ChioriInfo as $skill): ?>
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
            <h2 class="character-category">Chiori Ascension Costs</h2>
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
                    // 千織の昇華アイテムの情報
                    $ascensionItems = array(

                      array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Prithiva Topaz Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Dendrobium", "Count3" => "3", "Material4" => "Spectral Husk", "Count4" => "3"),

                      array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Prithiva Topaz Fragment", "Count1" => "3", "Material2" => "Artificed Spare Clockwork Component — Coppelia", "Count2" => "2", "Material3" => "Dendrobium", "Count3" => "10", "Material4" => "Spectral Husk", "Count4" => "15"),

                      array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Prithiva Topaz Fragment", "Count1" => "6", "Material2" => "Perpetual Heart", "Count2" => "4", "Material3" => "Dendrobium", "Count3" => "20", "Material4" => "Spectral Husk", "Count4" => "12"),

                      array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Prithiva Topaz Fragment", "Count1" => "3", "Material2" => "Artificed Spare Clockwork Component — Coppelia", "Count2" => "8", "Material3" => "Dendrobium", "Count3" => "30", "Material4" => "Spectral Husk", "Count4" => "18"),

                      array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Prithiva Topaz Fragment", "Count1" => "6", "Material2" => "Perpetual Heart", "Count2" => "12", "Material3" => "Dendrobium", "Count3" => "45", "Material4" => "Spectral Husk", "Count4" => "12"),

                      array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Prithiva Topaz Gemstone", "Count1" => "6", "Material2" => "Perpetual Heart", "Count2" => "20", "Material3" => "Dendrobium", "Count3" => "60", "Material4" => "Spectral Husk", "Count4" => "24")
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
        <h2 class="character-category">Chiori Showcase</h2>
        <div class="character-showcase" id="showcase">
          <lite-youtube videoid="QeCsTWMFrmg" params="rel=0"></lite-youtube>
        </div>


        <!--character end-->
        <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
  </main>
</div>
<?php get_footer('home1'); ?>