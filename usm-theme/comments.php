<?php
/**
 * Comments template.
 *
 * @package usm-theme
 */

if ( post_password_required() ) {
  return;
}

if ( ! comments_open() && ! have_comments() ) {
  return;
}
?>
<section id="comments" class="comments-section">
  <div class="post-card">
    <?php if ( have_comments() ) : ?>
      <h2 class="post-title">
        <?php
        printf(
          esc_html(
            _n( '%s Comment', '%s Comments', get_comments_number(), 'usm-theme' )
          ),
          esc_html( number_format_i18n( get_comments_number() ) )
        );
        ?>
      </h2>

      <ol class="comment-list">
        <?php
        wp_list_comments(
          array(
            'style'      => 'ol',
            'short_ping' => true,
          )
        );
        ?>
      </ol>

      <?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php
    if ( ! comments_open() && get_comments_number() ) :
      ?>
      <p class="post-meta">Comments are closed.</p>
    <?php endif; ?>

    <?php comment_form(); ?>
  </div>
</section>
