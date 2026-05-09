<?php
/****************************************

	Template Name: page-character.php
  Tekken7 character
	固定ページを表示するための
	テンプレートファイルです。

	page.php のコードに関しては、
	CHAPTER 12 で詳しく解説しています。

*****************************************/

get_header('nobootstrap'); ?>
<?php //get_header('50'); ?>

<!-- page.php -->
<h2>Intoroduce Tekken Characters</h2>

<p>Players can choose from a diverse cast that hails from a variety of ethnic backgrounds and fighting styles. A few characters have supernatural origin, such as Devil and Ogre, 
while animal characters like Kuma the bear and Roger the 
kangaroo provide comic relief.[1] In the story mode of the game, each character generally has their own personal reasons for entering the tournament and competing for the prize.</p>

<P>Only four characters have appeared as playable characters in all seven main Tekken installments to date: Heihachi Mishima, Nina Williams, Paul Phoenix, Yoshimitsu although Kuma and King but with two different characters also appeared in all Tekken main installments , though their appearances since Tekken 3 are their successors rather than the originals. Although Heihachi and Kuma are playable in all home versions of Tekken, they are both unplayable in the arcade version of the first Tekken and only appear as opponents.</P>

<P>Five characters: Anna Williams, Kazuya Mishima, Lee Chaolan, Lei Wulong and Marshall Law would come close, having appeared in six installments (though Kazuya and Law made cameo appearances in the third game). Jack also have appeared in six main installments with 6 different characters (Jack, Jack-2, Gun Jack, Jack-5, Jack-6 and Jack-7), with Tekken 4 being the game a Jack cyborg was absent (although a Jack-4 was created, this version was mass-produced instead of only one produced like the other Jack cyborgs)</p>

<!--bootstrap grit-->
	<div class="container">
    <div class="row">
<!--画像の名前、最初は大文字にしてpng 保存する-->
<?php
$args = array("Akuma","Alisa","Anna","Armorking","Asuka","Bob", "Bryan","Claudio","Deviljin","Dragunov","Eddy","Eliza","Fahkumram","Feng","Ganryu","Geese","Gigas","Heihachi","Hwoarang","Jack7","Jin","Josie","Julia","Katarina","Kazumi","Kazuya","King","Kuma","Kunimitsu","Lars","Law","Lee","Lei","Leo","Leroy","Lili","Lidia","Luckychloe","Marduk","Masterraven","Miguel","Negan","Nina","Noctis","Paul","Shaheen","Steve","Xiaoyu","Yoshimitsu","Zafina");

foreach ($args as $key => $value) : ?> 
<!--print $value. '_' . 'thumbnail' . 'png'-->

    <!-- Team Member 1 -->
    <div class="col-xs-6 col-sm-6 col-md-3 mb-3">
      <div class="card border-0 shadow"><img src="<?php echo get_template_directory_uri(); ?>/images/<?php echo $value; ?>_thumbnail.png" alt="" />
        <!--Slug Page Name- about-character name-tekken7>-->
        <div class="card-body text-center">
          <a  href="https://kaztokyo.sakura.ne.jp/about-<?php echo $value; ?>-tekken7/"><?php echo $value; ?></a>
        </div>
      </div>
    </div>
   
<!--$value = "$name"; 
'value' . '_thumbnail.png'

)'value' . '' 
-->
<?php endforeach; ?>


</div>
<!-- /.row -->

</div>
<!-- /.container -->
  	
   <!-- /page.php -->

<?php //get_footer();
get_footer('home1'); ?>