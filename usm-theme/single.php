<?php
/**
 * Single post template.
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
          <div class="post-meta">
            <?php echo esc_html( get_the_date() ); ?> | <?php the_author(); ?>
          </div>
          <div class="post-content">
            <?php the_content(); ?>
          </div>
        </article>

        <?php comments_template(); ?>
      <?php endwhile; ?>
    <?php else : ?>
      <section class="empty-state">
        <h2>Post not found</h2>
        <p>The requested post does not exist.</p>
      </section>
    <?php endif; ?>
  </main>

  <?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
