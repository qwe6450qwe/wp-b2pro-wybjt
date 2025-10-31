<?php
/**
 * Plugin Name: B2首页背景图插件
 * Plugin URI: https://example.com/b2-home-background
 * Description: 为B2Pro主题提供全局背景图片定制化管理功能
 * Version: 1.2.0
 * Author: MIKOLA
 * Author URI: https://example.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: b2-home-background
 * Domain Path: /languages
 */

// 确保插件安全加载
if (!defined('ABSPATH')) {
    exit;
}

// 定义插件常量
define('B2_HOME_BACKGROUND_VERSION', '1.2.0');
define('B2_HOME_BACKGROUND_DIR', plugin_dir_path(__FILE__));
define('B2_HOME_BACKGROUND_URL', plugin_dir_url(__FILE__));

// 加载插件功能
function b2_home_background_load() {
    // 加载设置页面
    require_once B2_HOME_BACKGROUND_DIR . 'includes/settings.php';
    // 加载前端功能
    require_once B2_HOME_BACKGROUND_DIR . 'includes/frontend.php';
}

b2_home_background_load();

// 加载插件CSS文件
function b2_home_background_enqueue_styles() {
    $settings = get_option('b2_home_background_settings', array('enabled' => 0));
    
    // 仅在插件启用时加载样式
    if (isset($settings['enabled']) && $settings['enabled']) {
        wp_enqueue_style(
            'b2-home-background',
            B2_HOME_BACKGROUND_URL . 'assets/css/style.css',
            array('b2-main'), // 依赖主题主样式，确保在其之后加载
            B2_HOME_BACKGROUND_VERSION
        );
        
        // 添加内联样式作为额外保障，确保背景被正确重置
        $inline_css = "
            .site, .b2-bg, .b2-page-bg, .b2-body-bg {
                background-image: none !important;
                background-color: transparent !important;
            }
            .b2-page-bg::before {
                display: none !important;
            }
            body {
                background: none !important;
                background-color: transparent !important;
            }
        ";
        wp_add_inline_style('b2-home-background', $inline_css);
    }
}
add_action('wp_enqueue_scripts', 'b2_home_background_enqueue_styles', 1001); // 提高优先级确保覆盖主题样式

// 激活插件
register_activation_hook(__FILE__, 'b2_home_background_activate');
function b2_home_background_activate() {
    // 设置默认值
    $default_settings = array(
        'enabled' => 1,
        'image_url' => '',
        'priority' => 999,
        'repeat' => 'no-repeat',
        'position' => 'center center',
        'opacity' => 100
    );
    
    if (!get_option('b2_home_background_settings')) {
        add_option('b2_home_background_settings', $default_settings);
    }
}

// 注销插件
register_deactivation_hook(__FILE__, 'b2_home_background_deactivate');
function b2_home_background_deactivate() {
    // 可以在这里清理数据，但不删除设置
}

// 卸载插件
register_uninstall_hook(__FILE__, 'b2_home_background_uninstall');
function b2_home_background_uninstall() {
    // 删除插件设置数据
    delete_option('b2_home_background_settings');
}

// 加载文本域
function b2_home_background_load_textdomain() {
    load_plugin_textdomain('b2-home-background', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'b2_home_background_load_textdomain');