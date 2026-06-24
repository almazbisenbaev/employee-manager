<?php
if (!defined('ABSPATH') && !isset($employee)) {
    exit;
}

$full_name = get_field('full_name', $employee->ID);
$position = get_field('position', $employee->ID);
$department = get_field('department', $employee->ID);
$hire_date = get_field('hire_date', $employee->ID);
$status = get_field('status', $employee->ID);

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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo esc_html($full_name); ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'DejaVu Sans', 'DejaVuSans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #111827;
            padding: 40px;
        }
        .em-employee-card {
            max-width: 100%;
        }
        .em-employee-header {
            display: flex;
            gap: 28px;
            margin-bottom: 36px;
            padding-bottom: 28px;
            border-bottom: 2px solid #e5e7eb;
        }
        .em-photo-wrapper {
            flex-shrink: 0;
        }
        .em-photo {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 12px;
        }
        .em-photo-placeholder {
            width: 140px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            border-radius: 12px;
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }
        .em-info-header {
            flex-grow: 1;
        }
        .em-employee-name {
            font-family: 'DejaVu Sans', 'DejaVuSans', sans-serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1.2;
            color: #111827;
        }
        .em-employee-position {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 4px;
            color: #374151;
        }
        .em-employee-details {
            margin-top: 30px;
        }
        .em-detail-row {
            display: flex;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f3f4f6;
        }
        .em-detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .em-detail-label {
            font-weight: 700;
            width: 130px;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            color: #4b5563;
        }
        .em-detail-value {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
        }
    </style>
</head>
<body>
    <div class="em-employee-card">
        <div class="em-employee-header">
            <div class="em-photo-wrapper">
                <?php if (has_post_thumbnail($employee->ID)): ?>
                    <?php echo get_the_post_thumbnail($employee->ID, [140, 140], ['class' => 'em-photo']); ?>
                <?php else: ?>
                    <div class="em-photo-placeholder">Нет фото</div>
                <?php endif; ?>
            </div>
            <div class="em-info-header">
                <div class="em-employee-name"><?php echo esc_html($full_name); ?></div>
                <div class="em-employee-position"><?php echo esc_html($position); ?></div>
            </div>
        </div>
        <div class="em-employee-details">
            <div class="em-detail-row">
                <div class="em-detail-label">Отдел</div>
                <div class="em-detail-value"><?php echo esc_html($departments[$department] ?? $department); ?></div>
            </div>
            <div class="em-detail-row">
                <div class="em-detail-label">Дата приема</div>
                <div class="em-detail-value"><?php echo esc_html($hire_date); ?></div>
            </div>
            <div class="em-detail-row">
                <div class="em-detail-label">Статус</div>
                <div class="em-detail-value"><?php echo esc_html($statuses[$status] ?? $status); ?></div>
            </div>
        </div>
    </div>
</body>
</html>
