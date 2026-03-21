<?php
/**
 * Archive template.
 *
 * @package usm-theme
 */
?>
<?php get_header(); ?>

<div class="container content-layout">
  <main class="main-content">
    <header class="post-card">
      <h1 class="post-title"><?php the_archive_title(); ?></h1>
      <?php the_archive_description( '<div class="post-meta">', '</div>' ); ?>
    </header>

    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <article class="post-card">
          <h2 class="post-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>
          <div class="post-meta">
            <?php echo esc_html( get_the_date() ); ?> | <?php the_author(); ?>
          </div>
          <div class="post-excerpt">
            <?php the_excerpt(); ?>
          </div>
          <a class="read-more" href="<?php the_permalink(); ?>">Read more</a>
        </article>
      <?php endwhile; ?>

      <?php the_posts_navigation(); ?>
    <?php else : ?>
      <section class="empty-state">
        <h2>No posts found</h2>
        <p>There are no posts in this archive yet.</p>
      </section>
    <?php endif; ?>
  </main>

  <?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
