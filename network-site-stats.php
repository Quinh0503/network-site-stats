<?php
/**
 * Plugin Name: Network Site Stats
 * Description: Hiển thị thống kê các site con trong mạng lưới Multisite.
 * Version: 1.0
 * Author: Quinh Quinh
 * Network: true
 */

// Ngăn chặn truy cập trực tiếp vào file
if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Tạo Menu trong Network Admin
add_action( 'network_admin_menu', 'nss_register_network_menu' );

function nss_register_network_menu() {
    add_menu_page(
        'Site Stats',               // Tiêu đề trang
        'Network Stats',            // Tên menu hiển thị
        'manage_network',           // Quyền hạn (chỉ Super Admin)
        'network-site-stats',       // Slug của menu
        'nss_display_stats_page',   // Hàm hiển thị nội dung
        'dashicons-chart-bar',      // Icon
        25                          // Vị trí
    );
}

// 2. Hàm hiển thị nội dung trang thống kê
function nss_display_stats_page() {
    // Lấy danh sách tất cả các site
    $sites = get_sites();

    echo '<div class="wrap">';
    echo '<h1>Thống kê mạng lưới các trang web con</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th>ID</th>
                <th>Tên Site</th>
                <th>Số bài viết</th>
                <th>Ngày đăng bài mới nhất</th>
            </tr>
          </thead>';
    echo '<tbody>';

    foreach ( $sites as $site ) {
        $blog_id = $site->blog_id;

        // Chuyển ngữ cảnh sang site con để lấy dữ liệu
        switch_to_blog( $blog_id );

        $site_name = get_bloginfo( 'name' );
        $post_count = wp_count_posts()->publish; // Chỉ đếm bài viết đã xuất bản
        
        // Lấy ngày của bài viết mới nhất
        $recent_posts = wp_get_recent_posts( array( 'numberposts' => 1, 'post_status' => 'publish' ) );
        $last_post_date = !empty($recent_posts) ? $recent_posts[0]['post_date'] : 'Chưa có bài viết';

        echo "<tr>
                <td>{$blog_id}</td>
                <td><strong>{$site_name}</strong></td>
                <td>{$post_count}</td>
                <td>{$last_post_date}</td>
              </tr>";

        // Quan trọng: Trả lại ngữ cảnh về trang hiện tại sau khi lấy xong
        restore_current_blog();
    }

    echo '</tbody></table></div>';
}