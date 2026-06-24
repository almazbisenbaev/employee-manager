<?php

get_header();

// Фильтры из параметров урл
$filter_department = isset($_GET['filter_department']) ? sanitize_text_field($_GET['filter_department']) : '';
$filter_position = isset($_GET['filter_position']) ? sanitize_text_field($_GET['filter_position']) : '';
$filter_status = isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : '';

$departments = [
    'development' => 'Разработка',
    'marketing' => 'Маркетинг',
    'sales' => 'Продажи',
    'hr' => 'HR'
];

$statuses = [
    'active' => 'Работает',
    'fired' => 'Уволен',
    'vacation' => 'В отпуске'
];
?>

<div class="em-container">

    <h1><?php post_type_archive_title(); ?></h1>

    <form method="get" class="em-filter-form">
        <div class="em-filter-group">
            <label for="filter_department">Отдел</label>
            <div class="em-filter-input-wrapper">
                <select name="filter_department" id="filter_department">
                    <option value="">Все отделы</option>
                    <?php foreach ($departments as $key => $label): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($filter_department, $key); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="em-filter-group">
            <label for="filter_position">Должность</label>
            <div class="em-filter-input-wrapper">
                <input type="text" name="filter_position" id="filter_position" value="<?php echo esc_attr($filter_position); ?>" placeholder="Поиск по должности">
            </div>
        </div>

        <div class="em-filter-group">
            <label for="filter_status">Статус</label>
            <div class="em-filter-input-wrapper">
                <select name="filter_status" id="filter_status">
                    <option value="">Все статусы</option>
                    <?php foreach ($statuses as $key => $label): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($filter_status, $key); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="em-filter-buttons">
            <button type="submit" class="em-button em-button-primary">Применить</button>
            <a href="<?php echo esc_url(get_post_type_archive_link('employees')); ?>" class="em-button">Сбросить</a>
        </div>

    </form>

    <div class="em-pdf-buttons">
        <a href="<?php echo esc_url(admin_url('admin-post.php?action=em_download_pdf&em_pdf_nonce=' . wp_create_nonce('em_download_pdf') . (isset($_GET['filter_department']) ? '&filter_department=' . urlencode($filter_department) : '') . (isset($_GET['filter_position']) ? '&filter_position=' . urlencode($filter_position) : '') . (isset($_GET['filter_status']) ? '&filter_status=' . urlencode($filter_status) : ''))); ?>" class="em-button em-button-primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 19H21V21H3V19ZM13 13.1716L19.0711 7.1005L20.4853 8.51472L12 17L3.51472 8.51472L4.92893 7.1005L11 13.1716V2H13V13.1716Z"></path></svg>
            Скачать PDF
        </a>
        <button id="em-preview-pdf-button" class="em-button">Предпросмотр PDF</button>
    </div>


    <div class="em-table-wrapper">
        <table class="em-table">
            <thead>
                <tr>
                    <th>Фото</th>
                    <th>ФИО</th>
                    <th>Должность</th>
                    <th>Отдел</th>
                    <th>Дата приема</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (have_posts()) : while (have_posts()) : the_post();
                    $full_name = get_field('full_name');
                    $position = get_field('position');
                    $department = get_field('department');
                    $hire_date = get_field('hire_date');
                    $status = get_field('status');
                ?>
                    <tr>
                        <td>
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail([44, 44], ['class' => 'em-photo']); ?>
                            <?php else: ?>
                                <div class="em-photo-placeholder">Нет фото</div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($full_name); ?></td>
                        <td><?php echo esc_html($position); ?></td>
                        <td><?php echo esc_html($departments[$department] ?? $department); ?></td>
                        <td><?php echo esc_html($hire_date); ?></td>
                        <td><?php echo esc_html($statuses[$status] ?? $status); ?></td>
                        <td>
                            <div class="em-actions">
                                <button class="em-button em-preview-single-pdf"
                                        data-preview-url="<?php echo esc_url(admin_url('admin-post.php?action=em_preview_single_pdf&employee_id=' . get_the_ID() . '&em_pdf_nonce=' . wp_create_nonce('em_download_single_pdf'))); ?>"
                                        data-download-url="<?php echo esc_url(admin_url('admin-post.php?action=em_download_single_pdf&employee_id=' . get_the_ID() . '&em_pdf_nonce=' . wp_create_nonce('em_download_single_pdf'))); ?>"
                                        data-employee-name="<?php echo esc_attr($full_name); ?>">
                                    Предпросмотр
                                </button>
                                <a href="<?php echo esc_url(admin_url('admin-post.php?action=em_download_single_pdf&employee_id=' . get_the_ID() . '&em_pdf_nonce=' . wp_create_nonce('em_download_single_pdf'))); ?>" class="em-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 19H21V21H3V19ZM13 13.1716L19.0711 7.1005L20.4853 8.51472L12 17L3.51472 8.51472L4.92893 7.1005L11 13.1716V2H13V13.1716Z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">Сотрудники не найдены</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="em-pdf-modal" class="em-pdf-modal">
    <div class="em-pdf-modal-content">
        <div class="em-pdf-modal-header">
            <h2 id="em-modal-title">Предпросмотр PDF</h2>
            <div class="em-pdf-modal-actions">
                <a id="em-modal-download-link" href="#" class="em-button em-button-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 19H21V21H3V19ZM13 13.1716L19.0711 7.1005L20.4853 8.51472L12 17L3.51472 8.51472L4.92893 7.1005L11 13.1716V2H13V13.1716Z"></path></svg>
                    Скачать PDF
                </a>
                <button id="em-print-pdf-button" class="em-button">Печать</button>
                <button id="em-close-pdf-modal" class="em-pdf-modal-close">&times;</button>
            </div>
        </div>
        <div class="em-pdf-modal-body">
            <iframe id="em-pdf-iframe" class="em-pdf-modal-iframe" src=""></iframe>
        </div>
    </div>
</div>

<?php get_footer(); ?>
