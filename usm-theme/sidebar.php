<?php
/**
 * Sidebar template.
 *
 * @package usm-theme
 */
?>
<aside class="sidebar">
  <section class="post-card">
    <h2 class="post-title">Sidebar</h2>
    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
      <?php dynamic_sidebar( 'sidebar-1' ); ?>
    <?php else : ?>
      <p>Add widgets to Sidebar in the WordPress admin area.</p>
    <?php endif; ?>
  </section>
</aside>
