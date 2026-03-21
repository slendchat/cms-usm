<?php
/**
 * Main template file.
 *
 * @package usm-theme
 */
?>
<?php get_header(); ?>

<main class="container">
  <?php
  $latest_posts = new WP_Query(
    array(
      'post_type'      => 'post',
      'posts_per_page' => 5,
      'post_status'    => 'publish',
    )
  );
  ?>

  <?php if ( $latest_posts->have_posts() ) : ?>
    <?php while ( $latest_posts->have_posts() ) : $latest_posts->the_post(); ?>
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
    <?php wp_reset_postdata(); ?>
  <?php else : ?>
    <section class="empty-state">
      <h2>No posts yet</h2>
      <p>Create your first post in WordPress admin and it will appear here.</p>
    </section>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
