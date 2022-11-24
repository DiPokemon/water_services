<?php
class WaterServices {

private static $plugin_url;
protected static $plugin_basename;

protected static $file;
protected static $model;

/**
 * Run
 */
public function __construct( $file ){

	// Vars
	self::$plugin_url = plugins_url( '/', $file );
	self::$plugin_basename = plugin_basename( $file );
	self::$file = $file;

	// Model
	self::$model = new WaterServicesModel();

	// Подключаем в админке
	if (is_admin()) {
		// Hooks
		add_action('admin_menu', array(__CLASS__, 'register_plugin_button_in_admin_menu'));

		// Подключаем в админке текущего плагина
		if (self::is_this_plugin_admin_page()) {
			// Hooks
			add_action('admin_footer', array(__CLASS__, 'media_selector_print_scripts'));

			// Filters
			// add_filter('mce_external_plugins', array(__CLASS__, 'enqueue_plugin_scripts'));
			// add_filter("mce_buttons", array(__CLASS__, 'register_buttons_editor'));

			// Handlers (add, edit, delete)
			$this->routing_handlers();

			// Ajax
			// подключаем AJAX обработчики, только когда в этом есть смысл
			// if( defined('DOING_AJAX') && DOING_AJAX ){
			// 	// wp_ajax_(action_name)
			// 	add_action('wp_ajax_list_for_tiny_mce', array(__CLASS__, 'ajax_list_for_tiny_mce_callback'));
			// }
		}
	}

	// Стили для админки и клиента
	// add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_static'));

	// Shortcodes
    add_shortcode('water_services', array(__CLASS__, 'replace_shortcode') );
}



/**
 * Активация плагина
 */
function activate(){
	// Добавить таблицу в БД при активации плагина
	// Источник: https://wp-kama.ru/function/register_activation_hook
	global $wpdb;

	if ($wpdb->get_var("SHOW TABLES LIKE '" . WATER_SERVICES_DB_TABLE_NAME . "'") != WATER_SERVICES_DB_TABLE_NAME)
	{
		$sql = "CREATE TABLE " . WATER_SERVICES_DB_TABLE_NAME . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			title tinytext NULL,
			img_title text NULL,
			description text NULL,
			alt tinytext NULL,
			image_attachment_id int(11) NULL,
			UNIQUE KEY id (id)
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		// dbDelta содержится в ABSPATH . 'wp-admin/includes/upgrade.php'
		// Назначение dbDelta: создание и обновление таблицы
      	dbDelta($sql);

      	// Добавить в таблицу options инфу о версии таблицы бд
      	add_option(WATER_SERVICES_PLUGIN_NAME . "_db_version", WATER_SERVICES_PLUGIN_DB_VERSION);
	}
}



/**
 * Добавить кнопку в меню админки
 */
static function register_plugin_button_in_admin_menu(){
	// Источник 1: https://wp-kama.ru/function/add_menu_page
	// Источник 2: https://truemisha.ru/blog/wordpress/administration-menus.html
	add_menu_page(
		WATER_SERVICES_PLUGIN_NAME_RU, 						// содержимое <title>
		WATER_SERVICES_PLUGIN_NAME_RU,							// название пункта в меню
		'manage_options',										// уровень доступа (взял из примера)
		WATER_SERVICES_PLUGIN_NAME,							// URL страницы с плагином
		array(__CLASS__, 'render_admin_page'), 					// функция, генерирующая страницу
		plugins_url( WATER_SERVICES_PLUGIN_NAME . '/static/images/admin-menu-button.png' ) // адрес иконки
	);
}

/**
 * Генерация админской страницы
 */
static function render_admin_page(){
	if (is_admin()) {
		if ( (isset($_GET['page'])) && ($_GET['page'] == WATER_SERVICES_PLUGIN_NAME) ) {
			switch ((isset($_GET['view']) ? $_GET['view'] : '')) {
				case 'add':
				    include dirname(self::$file) . '/views/form.php';
				    break;

				case 'edit':
					if (isset($_GET['data_id'])) {
						self::$model->get( $_GET['data_id'] );
						include dirname(self::$file) . '/views/form.php';
					}
				    break;

				default:
				    include dirname(self::$file) . '/views/index.php';
			}
		}
	}
}



/**
 * Обработчик событий (add, edit, delete)
 */
function routing_handlers(){
	if (is_admin()) {
		if ( (isset($_GET['page'])) && ($_GET['page'] == WATER_SERVICES_PLUGIN_NAME) ) {
			// Начальные данные
			$id = null;
			$title = null;
			$description 		  = null;
			$alt		  = null;
			$img_title		  = null;
			$image_attachment_id = null;


			// Обработка $_POST и $_GET
			if (isset($_GET['data_id']))
				$id = $_GET['data_id'];

			if (isset($_POST['data_id']))
				$id = $_POST['data_id'];

			if (isset($_POST['data_title']))
				$title = $_POST['data_title'];

			if (isset($_POST['data_img_title']))
				$img_title = $_POST['data_img_title'];

			if (isset($_POST['data_description']))
				$description = $_POST['data_description'];	

			if (isset($_POST['data_alt']))
				$alt = $_POST['data_alt'];

			if (isset($_POST['data_image_attachment_id']))
				$image_attachment_id = $_POST['data_image_attachment_id'];


			// Понять какое событие и выполнить его
			switch ((isset($_GET['action']) ? $_GET['action'] : '')) {
			case 'add':
				self::$model->title		= $title;
				self::$model->img_title  = $img_title;
				self::$model->description 		= $description;
				self::$model->alt		= $alt;
				self::$model->image_attachment_id = $image_attachment_id;
				self::$model->save();
				print('<script>window.location = "' . WATER_SERVICES_PLUGIN_ADMIN_URL . '"</script>');
				break;
			case 'edit':
				self::$model->title		= $title;
				self::$model->img_title  = $img_title;
				self::$model->description 		= $description;
				self::$model->alt		= $alt;
				self::$model->image_attachment_id = $image_attachment_id;
				self::$model->save();
				print('<script>window.location = "' . WATER_SERVICES_PLUGIN_ADMIN_URL . '"</script>');
				break;
			case 'delete':
				self::$model->delete( $id );
				print('<script>window.location = "' . WATER_SERVICES_PLUGIN_ADMIN_URL . '"</script>');
				break;
			}

		}
	}
}



static function enqueue_static() {
	wp_enqueue_style(WATER_SERVICES_PLUGIN_NAME . '_style', plugins_url( WATER_SERVICES_PLUGIN_NAME . '/static/css/style.css' ));
}



/**
 * TinyMCE
 */
	/**
	 * Подключить скрипты
	 */
	static function enqueue_plugin_scripts($plugin_array)
	{
	    // Enqueue TinyMCE plugin script with its ID.
	    $plugin_array["management_button_plugin"] = plugins_url( WATER_SERVICES_PLUGIN_NAME . '/static/js/modify_tiny_mce.js' );
	    return $plugin_array;
	}
	/**
	 * Добавить user кнопку в TinyMCE
	 */
	static function register_buttons_editor($buttons)
	{
		// Источник: http://qnimate.com/adding-buttons-to-wordpress-visual-editor/

	    // Register buttons with their id.
	    array_push($buttons, "management_tinymce_button");
	    return $buttons;
	}



/**
 * Окно для прикрепления медиа файлов
 */
static function media_selector_print_scripts() {
	$my_saved_attachment_post_id = get_option( WATER_SERVICES_PLUGIN_NAME.'media_selector_attachment_id', 0 );
	?><script type='text/javascript'>
		jQuery( document ).ready( function( $ ) {
			// Uploading files
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
			var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
			jQuery('#upload_image_button').on('click', function( event ){
				event.preventDefault();
				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;
				} else {
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}
				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Выберите изображение',
					button: {
						text: 'Задать изображение',
					},
					multiple: false	// Set to true to allow multiple files to be selected
				});
				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();
					// Do something with attachment.id and/or attachment.url here
					$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
					$( '#image_attachment_id' ).val( attachment.id );
					// Restore the main post ID
					wp.media.model.settings.post.id = wp_media_post_id;
				});
					// Finally, open the modal
					file_frame.open();
			});
			// Restore the main ID when the add media button is pressed
			jQuery( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			});
		});
	</script><?php
}



/**
 * Not-ajax callback
 */
static function replace_shortcode() {
	include dirname(self::$file) . '/views/replace_shortcode.php';
}



/**
 * Ajax callback
 */
static function ajax_list_for_tiny_mce_callback() {
	// источник: https://wp-kama.ru/id_2018/ajax-v-wordpress.html#ajax-v-admin-paneli-wordpress

	include dirname(self::$file) . '/views/replace_shortcode.php';

	wp_die(); // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция
}



/**
 * Вспомогательная функция: пользователь в панели управления и в текущем плагине?
 */
static function is_this_plugin_admin_page() {
	return is_admin() && ((isset($_GET['page'])) && ($_GET['page'] == WATER_SERVICES_PLUGIN_NAME));
}

}
