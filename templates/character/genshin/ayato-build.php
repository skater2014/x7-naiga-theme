<?php
/**
 * Template Name: ayato-build.php
 * Description: Template for displaying Genshin Impact character builds AYATO.
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
        $image_url = 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/Ayato.png';
        $image_alt = 'Ayato';
        $image_classes = 'character-portrait rarity-5';

        echo '<img src="' . esc_url($image_url) . '" class="' . esc_attr($image_classes) . '" alt="' . esc_attr($image_alt) . '">';
        ?>
        <!-- Character Header -->
        <div class="character-header">
          <div class="character-title">
            <h1 class="character-name">Genshin Impact AyatoBuild</h1> <img class="character-element"
              src="https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/element_hydro.png" alt="Hydro">
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
            'elegance' => 'elegance',
            'Mistsplitter_Reforged' => 'Mistsplitter_Reforged',
            'Vayuda Turquoise Sliver' => 'Vayuda Turquoise Sliver',
            'Dew of Repudiation' => 'Dew of Repudiation',
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
            <!-- AyatoBest Weapons -->
            <div class="character-build-section">
              <h2 class="character-build-section-title">AyatoBest Weapons</h2>
              <div class="character-build-weapons">
                <?php
                $weapons = array(
                  array('Haran Geppaku Futsu', 1, 5),
                  array('Primordial Jade Cutter', 2, 5),
                  array('Mistsplitter Reforged', 3, 5),
                  array('The Black Sword', 4, 4),
                  array('Summit Shaper', 5, 5)
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
                  $imageUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/ayato/{$encodedWeaponName}.png";
                  ?>
                  <!-- 武器情報を表示するブロック -->
                  <div class="character-build-weapon">
                    <!-- 武器ランクを表示 -->
                    <div class="character-build-weapon-rank"><?php echo esc_html($weaponRank); ?></div>
                    <!-- 武器アイコンを表示（クラスに手動で追加したレアリティ情報も含む）-->
                    <img class="character-build-weapon-icon<?php echo $additionalClasses; ?>"
                      src="<?php echo esc_url($imageUrl); ?>" alt="<?php echo esc_attr($weaponName); ?>">
                    <!-- 武器名を表示 -->
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
                array('Heart of Depth', 1),
                array("Gladiator's Finale", 2),
                array("Nymph's Dream", 3),
                array("Gladiator's Finale", 4),
                array('Heart of Depth', 4),
                array("Blizzard Strayer", 5),
              );

              $manualRanks = array(4, 4, 4, 2, 2, 4);
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
          <!-- Character Stats -->
          <div class="character-stats">
            <h2 class="character-stats-title">AyatoBest Stats</h2>
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
          <h2 class="character-category">Best AyatoTeams</h2>
          <!-- AyatoFreeze Team -->
          <div class="character-team">
            <div class="character-team-name">Ayato Main DPS
              <div class="character-team-characters">
                <?php
                // 既存のキャラクター情報を取得
                //$characters = get_genshin_characters();
                
                // 欲しいキャラクターの情報だけを取得
                $Ayato_Teams1["Ayato"] = array("element" => "hydro", "rarity" => "rarity-5");
                $Ayato_Teams1["Fischl"] = array("element" => "electro", "rarity" => "rarity-4");
                $Ayato_Teams1["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");
                $Ayato_Teams1["Kazuha"] = array("element" => "anemo", "rarity" => "rarity-5");
                // キャラクター情報を出力
                foreach ($Ayato_Teams1 as $name => $info):
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

          <!-- Ayato/Ganyu Mono Cryo Team -->
          <div class="character-team">
            <div class="character-team-name">Ayato Superconduct
              <div class="character-team-characters">
                <?php
                // 既存のキャラクター情報を取得
                //$characters = get_genshin_characters();
                
                // 欲しいキャラクターの情報だけを取得
                $Ayato_Teams2["Ayato"] = array("element" => "hydro", "rarity" => "rarity-5");
                $Ayato_Teams2["Barbara"] = array("element" => "hydro", "rarity" => "rarity-4");
                $Ayato_Teams2["Beidou"] = array("element" => "electro", "rarity" => "rarity-4");
                $Ayato_Teams2["Jean"] = array("element" => "anemo", "rarity" => "rarity-5");
                // キャラクター情報を出力
                foreach ($Ayato_Teams2 as $name => $info):
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
          <!-- Ayato/Ganyu Furina Hydro Team -->
          <div class="character-team">
            <div class="character-team-name">Ayato Vapitizer
              <div class="character-team-characters">
                <?php
                // 既存のキャラクター情報を取得
                //$characters = get_genshin_characters();
                
                // 欲しいキャラクターの情報だけを取得
                $Ayato_Teams3["Ayato"] = array("element" => "hydro", "rarity" => "rarity-5");
                $Ayato_Teams3["Fischl"] = array("element" => "electro", "rarity" => "rarity-4");
                $Ayato_Teams3["Xiangling"] = array("element" => "pyro", "rarity" => "rarity-4");
                $Ayato_Teams3["Bennett"] = array("element" => "pyro", "rarity" => "rarity-4");

                // キャラクター情報を出力
                foreach ($Ayato_Teams3 as $name => $info):
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
          // Ayatoの情報を格納する配列
          $AyatoInfo = array(
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
              "description" => "After using Kamisato Art: Hyouka, Kamisato Ayato's Normal and Charged attacks deal 30% increased DMG for 6s."
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
              "description" => "When Kamisato Ayato's Normal or Charged Attacks deal Cryo DMG to opponents, it has a 50% chance of decreasing the CD of Kamisato Art: Hyouka by 0.3s. This effect can occur once every 0.1s."
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
              "description" => "Kamisato Ayatogains Usurahi Butou every 10s, increasing her Charged Attack DMG by 298%. This buff will be cleared 0.5s after Ayato's Charged ATK hits an opponent, after which the timer for this ability will restart."
            )
          );
          ?>

          <div class="character-skills" id="talents">
            <h2 class="character-category">AyatoTalents</h2>

            <?php
            // 配列でタレント情報を定義
            $talents = array(
              array(
                'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/UI_GachaTypeIcon_Sword.png',
                'title' => 'Normal Attack',
                'name' => 'Kamisato Art: Marobashi',
                'description' => 'Normal Attack
                            Performs up to 5 rapid strikes.
                            Charged Attack
                            Consumes a certain amount of Stamina to dash forward and perform an iai.
                            Plunging Attack
                            lunges from mid-air to strike the ground below, damaging enemies along the path and dealing AoE DMG upon impact.
                            .'
              ),


              array(
                'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu%20Tekken7/images/genshin/ayato/talent_3.png',
                'title' => 'Elemental Skill',
                'name' => 'Kamisato Art: Kyouka',
                'description' => 'Kamisato Ayato shifts positions and enters the Takimeguri Kanka state. After this shift, he will leave a watery illusion at his original location. After it is formed, the watery illusion will explode if opponents are nearby or after its duration ends, dealing AoE Hydro DMG.

                      Takimeguri Kanka
                      In this state, Kamisato Ayato uses his Shunsuiken to engage in blindingly fast attacks, causing DMG from his Normal Attacks to be converted into AoE Hydro DMG. This cannot be overridden.
                      It also has the following properties:

                      After a Shunsuiken attack hits an opponent, it will grant Ayato the Namisen effect, increasing the DMG dealt by Shunsuiken based on Ayato\'s current Max HP. The initial maximum number of Namisen stacks is 4, and 1 stack can be gained through Shunsuiken every 0.1s. This effect will be dispelled when Takimeguri Kanka ends.
                      Kamisato Ayato\'s resistance to interruption is increased.
                      Unable to use Charged or Plunging Attacks.

                      Takimeguri Kanka will be cleared when Ayato leaves the field. Using Kamisato Art: Kyouka again while in the Takimeguri Kanka state will reset and replace the pre-existing state.'
              ),

              array(
                'icon' => 'https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/talent_4.png',
                'title' => 'Elemental Burst',
                'name' => 'Kamisato Art: Suiyuu',
                'description' => 'Unveils a garden of purity that silences the cacophony within. While this space exists, Bloomwater Blades will constantly rain down and attack opponents within its AoE, dealing Hydro DMG and increasing the Normal Attack DMG of characters within.
.'
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
          // AyatoPassivesの情報を出力
          ?>
          <div class="character-skills" id="passives">
            <h2 class="character-category">AyatoPassives</h2>
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
          // AyatoConstellationsの情報を出力
          ?>
          <div class="character-skills" id="constellations">
            <h2 class="character-category">AyatoConstellations</h2>
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
                    array("Rank" => "1", "Lvl" => "20", "Cost" => "20000", "Material1" => "Varunada Lazurite Sliver", "Count1" => "1", "Material2" => "", "Count2" => "", "Material3" => "Sakura Bloom", "Count3" => "3", "Material4" => "Old Handguard", "Count4" => "3"),
                    array("Rank" => "2", "Lvl" => "40", "Cost" => "40000", "Material1" => "Varunada Lazurite Fragment", "Count1" => "3", "Material2" => "Dew of Repudiation", "Count2" => "2", "Material3" => "Sakura Bloom", "Count3" => "10", "Material4" => "Old Handguard", "Count4" => "15"),
                    array("Rank" => "3", "Lvl" => "50", "Cost" => "60000", "Material1" => "Varunada Lazurite Fragment", "Count1" => "6", "Material2" => "Dew of Repudiation", "Count2" => "4", "Material3" => "Sakura Bloom", "Count3" => "20", "Material4" => "Kageuchi Handguard", "Count4" => "12"),
                    array("Rank" => "4", "Lvl" => "60", "Cost" => "80000", "Material1" => "Varunada Lazurite Chunk", "Count1" => "3", "Material2" => "Dew of Repudiation", "Count2" => "8", "Material3" => "Sakura Bloom", "Count3" => "30", "Material4" => "Kageuchi Handguard", "Count4" => "18"),
                    array("Rank" => "5", "Lvl" => "70", "Cost" => "100000", "Material1" => "Varunada Lazurite Chunk", "Count1" => "6", "Material2" => "Dew of Repudiation", "Count2" => "12", "Material3" => "Sakura Bloom", "Count3" => "45", "Material4" => "Famed Handguard", "Count4" => "12"),
                    array("Rank" => "6", "Lvl" => "80", "Cost" => "120000", "Material1" => "Varunada Lazurite Gemstone", "Count1" => "6", "Material2" => "Dew of Repudiation", "Count2" => "20", "Material3" => "Sakura Bloom", "Count3" => "60", "Material4" => "Famed Handguard", "Count4" => "24")
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
                          $materialUrl = "https://gamewidth.net/wp-content/themes/Xiaoyu Tekken7/images/genshin/ayato/" . ucfirst(str_replace(" ", "_", $material)) . ".png";
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
              <!--character team-->
              <h2 class="character-category">AyatoShowcase</h2>

              <div class="character-showcase" id="showcase">
                <lite-youtube videoid="bTcBcNcaFf4" params="rel=0"></lite-youtube>
              </div>


              <!--character end-->
              <!--<div><?php //echo do_shortcode('[mwai_chatbot id="default"]'); ?></div>-->
  </main>
</div>
<?php get_footer('home1'); ?>