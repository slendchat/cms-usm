<?php
/**
 * Plugin Name: USM-Notes
 * Description: Учебный плагин USM Notes, добавляет раздел «Заметки», позволяет создавать записи о задачах с приоритетами и датой напоминания.
 * Version: 1.0.0
 * Author: Artur Mamaliga
 */

/**
 * 
 * Пользовательский тип записи CPT «Заметки» (Notes) с помощью register_post_type().
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
 * Регистрирует таксономию «Приоритет» (Priority) для CPT «Заметки».
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
 * 1) регистрируем CPT и таксономию в текущем запросе;
 * 2) добавляем стартовые термины приоритета;
 * 3) обновляем rewrite rules.
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

// Регистрирует CPT и таксономию.
add_action( 'init', 'usm_notes_register_cpt' );
add_action( 'init', 'usm_notes_register_priority_taxonomy' );
add_action( 'init', 'usm_notes_maybe_seed_priority_terms', 20 );
