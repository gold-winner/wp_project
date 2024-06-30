<?php
$site_url = site_url();
$image_path = '/wp-content/themes/divi-child/uploads/';
$linkedin_url = $site_url . $image_path . 'linkedin.png';
$social_url = $site_url . $image_path . 'social.png';
$instagram_url = $site_url . $image_path . 'instagram.png';
?>
<ul class="et-social-icons">

    <?php if ('on' === et_get_option('divi_show_linkedin_icon', 'on')) : 'false' ?>
        <li class="et-social-icon ">
            <a href="<?php echo esc_url(et_get_option('divi_linkedin_url', 'Linkedin URL')); ?>" class="icon">
                <img src="<?php echo esc_url($social_url); ?>" alt="Social Icon">
                <span><?php esc_html_e('Linkedin', 'Divi'); ?></span>
            </a>
        </li>
    <?php endif; ?>
    <?php if ('on' === et_get_option('divi_show_linkedin_icon', 'on')) : 'false' ?>
        <li class="et-social-icon ">
            <a href="<?php echo esc_url(et_get_option('divi_linkedin_url', 'Linkedin URL')); ?>" class="icon">
                <img src="<?php echo esc_url($linkedin_url); ?>" alt="Linkedin Icon">
                <span><?php esc_html_e('Linkedin', 'Divi'); ?></span>
            </a>
        </li>
    <?php endif; ?>

    <?php $et_instagram_default = (true === et_divi_is_fresh_install()) ? 'on' : 'false'; ?>
    <?php if ('on' === et_get_option('divi_show_instagram_icon', $et_instagram_default)) : ?>
        <li class="et-social-icon ">
            <a href="<?php echo esc_url(et_get_option('divi_instagram_url', 'Linkedin URL')); ?>" class="icon">
                <img src="<?php echo esc_url($instagram_url); ?>" alt="Instagram Icon">
                <span><?php esc_html_e('Instagram', 'Divi'); ?></span>
            </a>
        </li>
    <?php endif; ?>
</ul>