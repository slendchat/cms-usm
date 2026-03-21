<?php
/**
 * Page template.
 *
 * @package usm-theme
 */
?>
<?php get_header(); ?>

<div class="container content-layout">
  <main class="main-content">
    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <article class="post-card">
          <h1 class="post-title"><?php the_title(); ?></h1>
          <div class="post-content">
            <?php the_content(); ?>
          </div>
        </article>

        <?php comments_template(); ?>
      <?php endwhile; ?>
    <?php else : ?>
      <section class="empty-state">
        <h2>Page not found</h2>
        <p>The requested page does not exist.</p>
      </section>
    <?php endif; ?>
  </main>

  <?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
