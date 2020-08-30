<?php
require 'header.php';
include 'about_text.php';
?>
<section>
<p>This website collects the top 10 headlines for the major UK news sources 24/7 for analysis, research and interest.</p><br>

<ul>
	<li>Due to a bug, there is no data between 23/07/2020 and 21/08/2020.</li>
	<li>Began collecting on the 19th November 2018.</li>
</ul>
<br>
<div>
<p>The two books that inspired this website are <a href='propaganda'><em>Propaganda: The Formation of Men's Attitudes</em></a> by <a href='https://en.wikipedia.org/wiki/Jacques_Ellul' target='_blank'><em>Jacques Ellul</em></a> and <a href='https://www.nickdavies.net/2008/02/05/introducing-flat-earth-news/' target='_blank'><em>Flat Earth News</em></a> by <a href='https://en.wikipedia.org/wiki/Nick_Davies' target='_blank'><em>Nick Davies.</em></a>
</div>
<br>
<div>
  <p><a target='_blank' href='http://podcasts.joerogan.net/podcasts/cenk-uygur'>Cenk Uygur on the Joe Rogan Podcast:</a></p><br>
  <audio controls>
    <source src='view/media/cenk_uygur_joe_rogan.mp3' type='audio/mpeg'>
    Audio unsupported
  </audio>
  <br>
  <br>
  <p>"Sensation sells papers."</p>
  <p><em><a href="https://web.archive.org/web/20110616183949/http://www.pressgazette.co.uk/story.asp?storycode=42394">https://web.archive.org/web/20110616183949/http://www.pressgazette.co.uk/story.asp?storycode=42394/</a> - Paul Dacre - Editor of the Daily Mail</em></p>
<br>
<br>
</div>
  </section>
<?php
$charts->bar_charts();
require 'footer.php';
?>
