<?php
/**
 * Plugin Name: USM-Notes
 * Description: Учебный плагин USM Notes, добавляет раздел "Заметки", позволяет создавать записи о задачах с приоритетами и датой напоминания.
 * Version: 1.0.0
 * Author: Artur Mamaliga
 */

/**
 * 
 * Пользовательский тип записи CPT "Заметки" (Notes) с помощью register_post_type().
 * массив labels для читаемости в админке.
 * массив args поведение типа записи.
 * Парамаетры:
 *    - public => true: записи видны на фронтенде.
 *    - supports: включает поля заголовка, редактора, автора, миниатюры.
 *    - has_archive => true: включает архивную страницу типа записи.
 *    - menu_icon: задает иконку.
 *    - show_in_rest => true: совместимость с редактором Gutenberg и REST.
 * register_post_type('notes', $args) - создает новый раздел.
 *
 * init регестрирует запись на после того как загржены базовые компоненты 
 * обеспечивает безопасность объявления типов записей.
 * @return void
 */
function usm_notes_register_cpt() {
	$labels = array(
		'name'                  => 'Заметки',
		'singular_name'         => 'Заметка',
		'menu_name'             => 'Заметки',
		'name_admin_bar'        => 'Заметка',
		'add_new'               => 'Добавить',
		'add_new_item'          => 'Добавить заметку',
		'new_item'              => 'Новая заметка',
		'edit_item'             => 'Редактировать заметку',
		'view_item'             => 'Просмотреть заметку',
		'all_items'             => 'Все заметки',
		'search_items'          => 'Искать заметки',
		'parent_item_colon'     => 'Родительская заметка:',
		'not_found'             => 'Заметки не найдены.',
		'not_found_in_trash'    => 'В корзине заметок не найдено.',
		'archives'              => 'Архив заметок',
		'attributes'            => 'Атрибуты заметки',
		'insert_into_item'      => 'Вставить в заметку',
		'uploaded_to_this_item' => 'Загружено для этой заметки',
	);

	$args = array(
		'labels'       => $labels,
		'public'       => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-edit-page',
		'show_in_rest' => true,
		'supports'     => array(
			'title',
			'editor',
			'author',
			'thumbnail',
		),
	);

	register_post_type( 'notes', $args );
}


/**
 * 
 * Регистрирует таксономию "Приоритет" (Priority) для CPT "Заметки".
 *
 * параметры:
 *    - register_taxonomy( 'priority', array( 'notes' ), $args ):
 *      priority - slug таксономии, notes - CPT, к которому она привязана.
 *    - hierarchical => true: иерархичность как категории.
 *    - public => true: доступ на фронтенде.
 *    - labels: подписи интерфейса.
 *    - show_ui/show_admin_column/query_var/show_in_rest => true:
 *      отображение в админке, колонка в списке записей, поддержка запросов REST.
 *
 * @return void
 */
function usm_notes_register_priority_taxonomy() {
	$labels = array(
		'name'              => 'Приоритеты',
		'singular_name'     => 'Приоритет',
		'search_items'      => 'Искать приоритеты',
		'all_items'         => 'Все приоритеты',
		'parent_item'       => 'Родительский приоритет',
		'parent_item_colon' => 'Родительский приоритет:',
		'edit_item'         => 'Редактировать приоритет',
		'update_item'       => 'Обновить приоритет',
		'add_new_item'      => 'Добавить новый приоритет',
		'new_item_name'     => 'Название нового приоритета',
		'menu_name'         => 'Приоритет',
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_rest'      => true,
	);

	register_taxonomy( 'priority', array( 'notes' ), $args );
}

/**
 * 
 * Создает приоритеты.
 * Низкий, Средний, Высокий.
 *
 * @return void
 */
function usm_notes_seed_priority_terms() {
	$default_terms = array( 'Низкий', 'Средний', 'Высокий' );

	foreach ( $default_terms as $term_name ) {
		if ( ! term_exists( $term_name, 'priority' ) ) {
			wp_insert_term( $term_name, 'priority' );
		}
	}
}

/**
 * 
 * Гарантирует, что термины будут созданы один раз
 *
 * @return void
 */
function usm_notes_maybe_seed_priority_terms() {
	if ( get_option( 'usm_notes_priority_terms_seeded' ) ) {
		return;
	}

	if ( ! taxonomy_exists( 'priority' ) ) {
		return;
	}

	usm_notes_seed_priority_terms();
	update_option( 'usm_notes_priority_terms_seeded', 1 );
}

/**
 * Логика активации:
 * регистрирует CPT и таксономию в текущем запросе
 * добавляет стартовые термины приоритета
 * обновляет rewrite rules.
 *
 * @return void
 */
function usm_notes_activate_plugin() {
	usm_notes_register_cpt();
	usm_notes_register_priority_taxonomy();
	usm_notes_seed_priority_terms();
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'usm_notes_activate_plugin' );

/**
 * Метабокс "Дата напоминания" в редактор заметки.
 *
 * @return void
 */
function usm_notes_add_reminder_date_meta_box() {
	add_meta_box(
		'usm_notes_reminder_date',
		'Дата напоминания',
		'usm_notes_render_reminder_date_meta_box',
		'notes',
		'side',
		'default'
	);
}

/**
 * Выводит поле даты в метабоксе.
 *
 * @param WP_Post $post Текущая запись.
 * @return void
 */
function usm_notes_render_reminder_date_meta_box( $post ) {
	$value     = get_post_meta( $post->ID, '_usm_notes_reminder_date', true );
	$today     = current_time( 'Y-m-d' );
	$field_id  = 'usm_notes_reminder_date';

	wp_nonce_field( 'usm_notes_save_reminder_date', 'usm_notes_reminder_date_nonce' );

	echo '<label for="' . esc_attr( $field_id ) . '">Выберите дату:</label>';
	echo '<input type="date" id="' . esc_attr( $field_id ) . '" name="usm_notes_reminder_date" value="' . esc_attr( $value ) . '" min="' . esc_attr( $today ) . '" required style="width:100%;margin-top:8px;">';
}

/**
 * Сохраняет дату напоминания при сохранении заметки.
 *
 * @param int     $post_id ID записи.
 * @param WP_Post $post    Объект записи.
 * @return void
 */
function usm_notes_save_reminder_date_meta( $post_id, $post ) {
	if ( 'notes' !== $post->post_type ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if (
		! isset( $_POST['usm_notes_reminder_date_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['usm_notes_reminder_date_nonce'] ) ), 'usm_notes_save_reminder_date' )
	) {
		return;
	}

	$raw_date = isset( $_POST['usm_notes_reminder_date'] ) ? sanitize_text_field( wp_unslash( $_POST['usm_notes_reminder_date'] ) ) : '';

	if ( '' === $raw_date ) {
		usm_notes_set_admin_error( 'Поле "Дата напоминания" обязательно для заполнения.' );
		delete_post_meta( $post_id, '_usm_notes_reminder_date' );
		return;
	}

	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw_date ) ) {
		usm_notes_set_admin_error( 'Некорректный формат даты. Используйте YYYY-MM-DD.' );
		return;
	}

	$today = current_time( 'Y-m-d' );
	if ( $raw_date < $today ) {
		usm_notes_set_admin_error( 'Дата напоминания не может быть в прошлом.' );
		return;
	}

	update_post_meta( $post_id, '_usm_notes_reminder_date', $raw_date );
}

/**
 * Сохраняет сообщение об ошибке для текущего пользователя.
 *
 * @param string $message Текст ошибки.
 * @return void
 */
function usm_notes_set_admin_error( $message ) {
	$user_id = get_current_user_id();
	if ( $user_id ) {
		set_transient( 'usm_notes_reminder_error_' . $user_id, $message, 60 );
	}
}

/**
 * Показывает admin notice с ошибкой в админке.
 *
 * @return void
 */
function usm_notes_show_admin_error_notice() {
	if ( ! is_admin() ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'notes' !== $screen->post_type ) {
		return;
	}

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return;
	}

	$key     = 'usm_notes_reminder_error_' . $user_id;
	$message = get_transient( $key );

	if ( ! $message ) {
		return;
	}

	delete_transient( $key );
	echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
}

/**
 * Добавляет колонку даты напоминания в список заметок.
 *
 * @param array $columns Колонки списка.
 * @return array
 */
function usm_notes_add_reminder_date_column( $columns ) {
	$columns['usm_notes_reminder_date'] = 'Дата напоминания';
	return $columns;
}

/**
 * Выводит значение даты в колонке списка заметок.
 *
 * @param string $column  Ключ колонки.
 * @param int    $post_id ID записи.
 * @return void
 */
function usm_notes_render_reminder_date_column( $column, $post_id ) {
	if ( 'usm_notes_reminder_date' !== $column ) {
		return;
	}

	$value = get_post_meta( $post_id, '_usm_notes_reminder_date', true );
	echo $value ? esc_html( $value ) : '—';
}

/**
 * Шорткод [usm_notes priority="slug" before_date="YYYY-MM-DD"].
 *
 * @param array $atts Атрибуты шорткода.
 * @return string
 */
function usm_notes_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'priority'    => '',
			'before_date' => '',
		),
		$atts,
		'usm_notes'
	);

	$priority_slug = sanitize_title( $atts['priority'] );
	$before_date   = sanitize_text_field( $atts['before_date'] );

	$args = array(
		'post_type'      => 'notes',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'meta_key'       => '_usm_notes_reminder_date',
		'meta_type'      => 'DATE',
		'orderby'        => array(
			'meta_value' => 'ASC',
			'date'       => 'DESC',
		),
	);

	if ( '' !== $priority_slug ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'priority',
				'field'    => 'slug',
				'terms'    => $priority_slug,
			),
		);
	}

	if ( '' !== $before_date && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $before_date ) ) {
		$args['meta_query'] = array(
			array(
				'key'     => '_usm_notes_reminder_date',
				'value'   => $before_date,
				'compare' => '<=',
				'type'    => 'DATE',
			),
		);
	}

	$query = new WP_Query( $args );
	ob_start();

	echo '<style>
.usm-notes-list{list-style:none;padding:0;margin:0;display:grid;gap:12px}
.usm-notes-item{border:1px solid #dcdcde;border-radius:8px;padding:12px;background:#fff}
.usm-notes-title{margin:0 0 8px;font-size:18px}
.usm-notes-meta{margin:0 0 8px;color:#50575e;font-size:14px}
.usm-notes-excerpt{margin:0;color:#1d2327}
.usm-notes-empty{padding:12px;border:1px dashed #c3c4c7;border-radius:8px}
</style>';

	if ( ! $query->have_posts() ) {
		echo '<p class="usm-notes-empty">Нет заметок с заданными параметрами</p>';
		return (string) ob_get_clean();
	}

	echo '<ul class="usm-notes-list">';
	while ( $query->have_posts() ) {
		$query->the_post();

		$post_id       = get_the_ID();
		$title         = get_the_title();
		$excerpt       = get_the_excerpt();
		$reminder_date = get_post_meta( $post_id, '_usm_notes_reminder_date', true );
		$terms         = get_the_terms( $post_id, 'priority' );
		$priority_text = 'Не указан';

		if ( is_array( $terms ) && ! empty( $terms ) ) {
			$names = wp_list_pluck( $terms, 'name' );
			$priority_text = implode( ', ', array_map( 'esc_html', $names ) );
		}

		echo '<li class="usm-notes-item">';
		echo '<h3 class="usm-notes-title">' . esc_html( $title ) . '</h3>';
		echo '<p class="usm-notes-meta"><strong>Дата:</strong> ' . esc_html( $reminder_date ? $reminder_date : 'Не указана' ) . ' | <strong>Приоритет:</strong> ' . $priority_text . '</p>';
		echo '<p class="usm-notes-excerpt">' . esc_html( $excerpt ) . '</p>';
		echo '</li>';
	}
	echo '</ul>';

	wp_reset_postdata();
	return (string) ob_get_clean();
}

// Регистрирует CPT и таксономию.
add_action( 'init', 'usm_notes_register_cpt' );
add_action( 'init', 'usm_notes_register_priority_taxonomy' );
add_action( 'init', 'usm_notes_maybe_seed_priority_terms', 20 );

// Метабокс, сохранение и вывод даты напоминания.
add_action( 'add_meta_boxes', 'usm_notes_add_reminder_date_meta_box' );
add_action( 'save_post', 'usm_notes_save_reminder_date_meta', 10, 2 );
add_action( 'admin_notices', 'usm_notes_show_admin_error_notice' );
add_filter( 'manage_notes_posts_columns', 'usm_notes_add_reminder_date_column' );
add_action( 'manage_notes_posts_custom_column', 'usm_notes_render_reminder_date_column', 10, 2 );
add_shortcode( 'usm_notes', 'usm_notes_shortcode' );
