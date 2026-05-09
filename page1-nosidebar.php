<?php
/****************************************

	Template Name: page1-nosidebar

	固定ページを表示するための
	テンプレートファイルです。

	page.php のコードに関しては、
	CHAPTER 12 で詳しく解説しています。

*****************************************/

get_header(); ?>
<!-- page.php -->
<H1>Intoroduce Tekken Characters</H1> 

<p>Players can choose from a diverse cast that hails from a variety of ethnic backgrounds and fighting styles. A few characters have supernatural origin, such as Devil and Ogre, while animal characters like Kuma the bear and Roger the kangaroo provide comic relief.[1] In the story mode of the game, each character generally has their own personal reasons for entering the tournament and competing for the prize.

Only four characters have appeared as playable characters in all seven main Tekken installments to date: Heihachi Mishima, Nina Williams, Paul Phoenix, Yoshimitsu although Kuma and King but with two different characters also appeared in all Tekken main installments , though their appearances since Tekken 3 are their successors rather than the originals. Although Heihachi and Kuma are playable in all home versions of Tekken, they are both unplayable in the arcade version of the first Tekken and only appear as opponents.

Five characters: Anna Williams, Kazuya Mishima, Lee Chaolan, Lei Wulong and Marshall Law would come close, having appeared in six installments (though Kazuya and Law made cameo appearances in the third game). Jack also have appeared in six main installments with 6 different characters (Jack, Jack-2, Gun Jack, Jack-5, Jack-6 and Jack-7), with Tekken 4 being the game a Jack cyborg was absent (although a Jack-4 was created, this version was mass-produced instead of only one produced like the other Jack cyborgs)</p>

	<div class="container">
    <div class="row">

<?php
$args = array("akuma","alisa","anna","armorking","asuka","bob", "bryan","claudio","deviljin","dragunov","eddy","eliza","fahkumram","feng","ganryu","geese","gigas","heihachi","hwoarang","jack7","jin","josie","julia","katarina","kazumi","kazuya","king","kuma","kunimitsu","lars","law","lee","lei","leo","leroy","lili","luckychloe","marduk","masterraven","miguel","negan","nina","noctis","paul","shaheen","steve","xiaoyu","yoshimitsu","zafina");

foreach ($args as $key => $value) : ?> 
<!--print $value. '_' . 'thumbnail' . 'png'-->

    <!-- Team Member 1 -->
    <div class="col-xs-6 col-sm-6 col-md-3 mb-3">
      <div class="card border-0 shadow"><img src="<?php echo get_template_directory_uri(); ?>/images/<?php echo $value; ?>_thumbnail.png" alt="" />
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
get_footer(); ?>