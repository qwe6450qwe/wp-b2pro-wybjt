<?php
/**
 * B2首页背景图插件 - 前端实现
 * 
 * 处理背景图的前端显示逻辑，替换主题原有背景
 *
 * @package B2首页背景图插件
 * @author MIKOLA
 */

// 确保文件被正确加载
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 输出背景图样式
 */
function b2_home_background_output() {
    // 获取设置
    $settings = get_option('b2_home_background_settings', array(
        'enabled' => 0,
        'image_url' => '',
        'priority' => 999,
        'repeat' => 'no-repeat',
        'position' => 'center center',
        'opacity' => 100
    ));
    
    // 检查是否启用
    if (!isset($settings['enabled']) || !$settings['enabled'] || !isset($settings['image_url']) || empty($settings['image_url'])) {
        return;
    }
    
    // 获取设置的值
    $opacity = isset($settings['opacity']) ? intval($settings['opacity']) : 100;
    $repeat = isset($settings['repeat']) ? $settings['repeat'] : 'no-repeat';
    $position = isset($settings['position']) ? $settings['position'] : 'center center';
    $priority = isset($settings['priority']) ? intval($settings['priority']) : 999;
    $blur = isset($settings['blur']) ? intval($settings['blur']) : 0;
    
    // 计算透明度
    $opacity_value = $opacity / 100;
    
    // 计算模糊度（将0-100映射到0-20px的模糊范围，这个范围比较美观）
    $blur_value = $blur * 0.2; // 0对应0px，100对应20px
    
    // 输出样式，完全覆盖主题背景
    echo '<style id="b2-home-background-style">
        /* 重置主题背景样式 */
        .site {
            background-image: none !important;
            background-color: transparent !important;
        }
        
        /* 重置页面背景 */
        .b2-page-bg {
            background-color: transparent !important;
        }
        
        .b2-page-bg::before {
            display: none !important;
        }
        
        /* 重置更多可能影响背景的元素 */
        .b2-bg {
            background-image: none !important;
            background-color: transparent !important;
        }
        
        .b2-body-bg {
            background-color: transparent !important;
        }
        
        /* 设置我们的背景图 */
        body {
            background: none !important;
            background-color: transparent !important;
        }
        
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("' . esc_url($settings['image_url']) . '");
            background-repeat: ' . esc_attr($repeat) . ';
            background-position: ' . esc_attr($position) . ';
            background-size: cover;
            opacity: ' . esc_attr($opacity_value) . ';
            ' . ($blur_value > 0 ? 'filter: blur(' . esc_attr($blur_value) . 'px);' : '') . '
            z-index: -9999; /* 固定为非常低的值，确保只在最底层 */
            pointer-events: none;
        }
    </style>';
}
add_action('wp_head', 'b2_home_background_output', 1000); // 使用更高的优先级确保覆盖主题样式

/**
 * 添加背景图容器
 */
function b2_home_background_add_container() {
    // 获取设置
    $settings = get_option('b2_home_background_settings', array(
        'enabled' => 0,
        'image_url' => ''
    ));
    
    // 如果插件未启用或没有图片URL，则不添加容器
    if (!isset($settings['enabled']) || !$settings['enabled'] || !isset($settings['image_url']) || empty($settings['image_url'])) {
        return;
    }
    
    echo '<div class="b2-home-background"></div>';
}
add_action('wp_body_open', 'b2_home_background_add_container');

/**
 * 优化图片加载
 */
function b2_home_background_optimize_image_loading() {
    // 获取设置
    $settings = get_option('b2_home_background_settings', array(
        'enabled' => 0,
        'image_url' => ''
    ));
    
    // 如果插件未启用或没有图片URL，则不执行优化
    if (!isset($settings['enabled']) || !$settings['enabled'] || !isset($settings['image_url']) || empty($settings['image_url'])) {
        return;
    }
    
    // 添加preload以优化背景图加载
    echo '<link rel="preload" href="' . esc_url($settings['image_url']) . '" as="image">';
}
add_action('wp_head', 'b2_home_background_optimize_image_loading', 1);

/**
 * 响应式调整
 */
function b2_home_background_responsive() {
    // 获取设置
    $settings = get_option('b2_home_background_settings', array(
        'enabled' => 0,
        'image_url' => ''
    ));
    
    // 如果插件未启用或没有图片URL，则不输出响应式样式
    if (!isset($settings['enabled']) || !$settings['enabled'] || !isset($settings['image_url']) || empty($settings['image_url'])) {
        return;
    }
    
    // 输出响应式样式
    echo '<style id="b2-home-background-responsive">
        @media (max-width: 768px) {
            body::before {
                background-attachment: scroll;
            }
        }
        
        /* 确保在小屏幕上也能良好显示 */
        @media (max-width: 480px) {
            body::before {
                background-size: cover;
            }
        }
    </style>';
}
add_action('wp_head', 'b2_home_background_responsive', 1001);