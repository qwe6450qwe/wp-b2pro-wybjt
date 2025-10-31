<?php

// 添加设置菜单项
function b2_home_background_add_menu() {
    add_menu_page(
        __('B2背景图设置', 'b2-home-background'),
        __('B2背景图设置', 'b2-home-background'),
        'manage_options',
        'b2-home-background',
        'b2_home_background_settings_page',
        'dashicons-format-image',
        80
    );
}
add_action('admin_menu', 'b2_home_background_add_menu');

// 设置页面回调函数
function b2_home_background_settings_page() {
    // 保存设置
    if (isset($_POST['b2_home_background_save'])) {
        check_admin_referer('b2_home_background_nonce', 'b2_home_background_nonce');
        
        $settings = get_option('b2_home_background_settings', array());
        
        // 更新设置
        $settings['enabled'] = isset($_POST['enabled']) ? 1 : 0;
        $settings['image_url'] = sanitize_text_field($_POST['image_url']);
        $settings['priority'] = absint($_POST['priority']);
        $settings['repeat'] = sanitize_text_field($_POST['repeat']);
        $settings['position'] = sanitize_text_field($_POST['position']);
        $settings['opacity'] = absint($_POST['opacity']);
        $settings['blur'] = absint($_POST['blur']);
        
        update_option('b2_home_background_settings', $settings);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('设置已保存', 'b2-home-background') . '</p></div>';
    }
    
    // 获取当前设置
    $settings = get_option('b2_home_background_settings', array(
        'enabled' => 1,
        'image_url' => '',
        'priority' => 999,
        'repeat' => 'no-repeat',
        'position' => 'center center',
        'opacity' => 100,
        'blur' => 0
    ));
    
    // 重复选项
    $repeat_options = array(
        'no-repeat' => __('不重复', 'b2-home-background'),
        'repeat' => __('平铺', 'b2-home-background'),
        'repeat-x' => __('横向重复', 'b2-home-background'),
        'repeat-y' => __('纵向重复', 'b2-home-background')
    );
    
    // 位置选项
    $position_options = array(
        'center center' => __('居中', 'b2-home-background'),
        'top left' => __('左上角', 'b2-home-background'),
        'top center' => __('顶部居中', 'b2-home-background'),
        'top right' => __('右上角', 'b2-home-background'),
        'center left' => __('左侧居中', 'b2-home-background'),
        'center right' => __('右侧居中', 'b2-home-background'),
        'bottom left' => __('左下角', 'b2-home-background'),
        'bottom center' => __('底部居中', 'b2-home-background'),
        'bottom right' => __('右下角', 'b2-home-background')
    );
    
    ?>
    <div class="wrap">
        <h1><?php _e('B2背景图设置', 'b2-home-background'); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('b2_home_background_nonce', 'b2_home_background_nonce'); ?>
            
            <table class="form-table">
                <tbody>
                    <!-- 启用/禁用开关 -->
                    <tr>
                        <th scope="row">
                            <label for="enabled"><?php _e('启用插件', 'b2-home-background'); ?></label>
                        </th>
                        <td>
                            <label class="switch">
                                <input type="checkbox" id="enabled" name="enabled" <?php checked(isset($settings['enabled']) ? $settings['enabled'] : 0, 1); ?>>
                                <span class="slider round"></span>
                            </label>
                            <p class="description"><?php _e('开启或关闭背景图片功能', 'b2-home-background'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- 图片上传 -->
                    <tr>
                        <th scope="row">
                            <label for="image_url"><?php _e('背景图片', 'b2-home-background'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="image_url" name="image_url" class="regular-text" value="<?php echo isset($settings['image_url']) ? esc_attr($settings['image_url']) : ''; ?>">
                            <input type="button" id="upload_image_button" class="button" value="<?php _e('上传/选择图片', 'b2-home-background'); ?>">
                            <p class="description"><?php _e('选择一张图片作为网站背景', 'b2-home-background'); ?></p>
                            
                            <?php if (isset($settings['image_url']) && $settings['image_url']) : ?>
                                <div style="margin-top: 10px;">
                                    <img src="<?php echo esc_url($settings['image_url']); ?>" style="max-width: 300px; max-height: 200px; border: 1px solid #ddd;">
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <!-- 优先级设置 -->
                    <tr>
                        <th scope="row">
                            <label for="priority"><?php _e('优先级', 'b2-home-background'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="priority" name="priority" min="0" max="9999" value="<?php echo isset($settings['priority']) ? esc_attr($settings['priority']) : 999; ?>">
                            <p class="description"><?php _e('控制背景图的CSS优先级，数值越大优先级越高', 'b2-home-background'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- 重复方式 -->
                    <tr>
                        <th scope="row">
                            <label for="repeat"><?php _e('重复方式', 'b2-home-background'); ?></label>
                        </th>
                        <td>
                            <select id="repeat" name="repeat">
                                <?php foreach ($repeat_options as $value => $label) : ?>
                                    <option value="<?php echo esc_attr($value); ?>" <?php selected(isset($settings['repeat']) ? $settings['repeat'] : 'no-repeat', $value); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('设置背景图片的重复方式', 'b2-home-background'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- 位置调整 -->
                    <tr>
                        <th scope="row">
                            <label for="position"><?php _e('位置调整', 'b2-home-background'); ?></label>
                        </th>
                        <td>
                            <select id="position" name="position">
                                <?php foreach ($position_options as $value => $label) : ?>
                                    <option value="<?php echo esc_attr($value); ?>" <?php selected(isset($settings['position']) ? $settings['position'] : 'center center', $value); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('设置背景图片的显示位置', 'b2-home-background'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- 透明度控制 -->
                    <tr>
                        <th scope="row">
                            <label for="opacity"><?php _e('透明度', 'b2-home-background'); ?></label>
                        </th>
                        <td>
                            <input type="range" id="opacity" name="opacity" min="0" max="100" value="<?php echo isset($settings['opacity']) ? esc_attr($settings['opacity']) : 100; ?>" step="1">
                            <span id="opacity_value"><?php echo isset($settings['opacity']) ? esc_attr($settings['opacity']) : 100; ?>%</span>
                            <p class="description"><?php _e('调整背景图片的不透明度（0-100）', 'b2-home-background'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- 模糊度控制 -->
                    <tr>
                        <th scope="row">
                            <label for="blur"><?php _e('模糊度', 'b2-home-background'); ?></label>
                        </th>
                        <td>
                            <input type="range" id="blur" name="blur" min="0" max="100" value="<?php echo isset($settings['blur']) ? esc_attr($settings['blur']) : 0; ?>" step="1">
                            <span id="blur_value"><?php echo isset($settings['blur']) ? esc_attr($settings['blur']) : 0; ?></span>
                            <p class="description"><?php _e('调整背景图片的模糊程度（0-100，0为关闭，100为完全模糊）', 'b2-home-background'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php submit_button(__('保存设置', 'b2-home-background'), 'primary', 'b2_home_background_save'); ?>
        </form>
    </div>
    
    <style>
        /* 开关样式 */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }
        
        input:checked + .slider {
            background-color: #2196F3;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .slider.round {
            border-radius: 24px;
        }
        
        .slider.round:before {
            border-radius: 50%;
        }
    </style>
    
    <script>
        // 透明度滑块实时更新
        document.getElementById('opacity').addEventListener('input', function() {
            document.getElementById('opacity_value').textContent = this.value + '%';
        });
        
        // 模糊度滑块实时更新
        document.getElementById('blur').addEventListener('input', function() {
            document.getElementById('blur_value').textContent = this.value;
        });
        
        // 图片上传功能
        jQuery(document).ready(function($) {
            var mediaUploader;
            
            $('#upload_image_button').click(function(e) {
                e.preventDefault();
                
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                
                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: '<?php _e('选择背景图片', 'b2-home-background'); ?>',
                    button: {
                        text: '<?php _e('使用此图片', 'b2-home-background'); ?>'
                    },
                    multiple: false
                });
                
                mediaUploader.on('select', function() {
                    attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#image_url').val(attachment.url);
                });
                
                mediaUploader.open();
            });
        });
    </script>
    <?php
}

// 加载媒体上传脚本
function b2_home_background_enqueue_media() {
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'b2_home_background_enqueue_media');