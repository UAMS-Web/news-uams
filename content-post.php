<h1><?php the_title() ?></h1>

<div id="mobile-sidebar">

  <button id="mobile-sidebar-menu" class="visible-xs" aria-hidden="true" tabindex="1">

      <div aria-hidden="true" id="ham">
        <span></span>
      <span></span>
      <span></span>
      <span></span>
      </div>
    <div id="mobile-sidebar-title" class="page_item">

      <?php
            // limitation of the characters
            $text = get_the_title();
echo text_cut($text, 27, true);
function text_cut($text, $length, $dots): string
{
    // $text =get_the_title();
    $text = trim(preg_replace('#[\s\n\r\t]{2,}#', ' ', $text));
    $text_temp = $text;
    while (substr($text, $length, 1) !== ' ') {
        $length--;
        if ($length > strlen($text)) {
            break;
        }
    }
    $text = substr($text, 0, $length);

    return $text.(($dots == true && $text !== '' && strlen($text_temp) > $length) ? '...' : '');
}
?>

      </div>
  </button>
  <div id="mobile-sidebar-links" aria-hidden="true">  <?php uams_sidebar_menu_mobile(); ?></div>
</div>

<?php the_content(); ?>

<?php if (get_post_meta(get_the_ID(), 'include_boilerplate', true)) { ?>
  <!-- #Begin UAMS Boilerplate -->
  <hr size="1" width="85%"/><br/>
  <p>UAMS is the state’s only health sciences university, with colleges of Medicine, Nursing, Pharmacy, Health Professions and Public Health; a graduate school; a hospital; a main campus in Little Rock; a Northwest Arkansas regional campus in Fayetteville; a statewide network of regional campuses; and eight institutes: the Winthrop P. Rockefeller Cancer Institute, Jackson T. Stephens Spine &amp; Neurosciences Institute, Harvey &amp; Bernice Jones Eye Institute, Psychiatric Research Institute, Donald W. Reynolds Institute on Aging, Translational Research Institute, Institute for Digital Health &amp; Innovation and the Institute for Community Health Innovation. UAMS includes UAMS Health, a statewide health system that encompasses all of UAMS’ clinical enterprise. UAMS is the only adult Level 1 trauma center in the state. UAMS has 3,553 students and 902 medical residents and fellows. It is the state’s largest public employer with about 12,000 employees, including 1,200 physicians who provide care to patients at UAMS, its regional campuses, Arkansas Children’s, the VA Medical Center and Baptist Health. Visit <a href="http://www.uams.edu/">www.uams.edu</a> or <a href="http://www.uamshealth.com/" title="http://www.uamshealth.com/">www.uamshealth.com</a>. Find us on <a href="https://www.facebook.com/UAMShealth">Facebook</a>, <a href="https://twitter.com/uamshealth">Twitter</a>, <a href="https://www.youtube.com/user/UAMSHealth">YouTube</a> or <a href="https://instagram.com/uamshealth/">Instagram</a>.</p>
  <p class="center-text">###</p>
  <!-- #End UAMS Boilerplate -->
<?php } ?>