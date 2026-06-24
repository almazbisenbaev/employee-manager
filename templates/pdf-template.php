<?php
if (!defined('ABSPATH') && !isset($employees)) {
    exit;
}

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
    <title>Список сотрудников</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'DejaVu Sans', 'DejaVuSans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #111827;
            padding: 40px;
        }
        h1 {
            font-family: 'DejaVu Sans', 'DejaVuSans', sans-serif;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 30px;
            color: #111827;
        }
        .em-table {
            width: 100%;
            border-collapse: collapse;
        }
        .em-table th,
        .em-table td {
            padding: 14px 16px;
            text-align: left;
        }
        .em-table th {
            background-color: #f9fafb;
            color: #4b5563;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e5e7eb;
        }
        .em-table td {
            border-bottom: 1px solid #f3f4f6;
        }
        .em-table tbody tr:last-child td {
            border-bottom: none;
        }
        .em-photo {
            width: 40px;
            height: 40px;
            object-fit: cover;
            display: block;
            border-radius: 8px;
        }
        .em-photo-placeholder {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            border-radius: 8px;
            color: #6b7280;
            font-size: 9px;
            font-weight: 600;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Список сотрудников</h1>

    <table class="em-table">
        <thead>
            <tr>
                <th style="width: 55px;">Фото</th>
                <th>ФИО</th>
                <th>Должность</th>
                <th>Отдел</th>
                <th>Дата приема</th>
                <th>Статус</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($processed_employees)) : foreach ($processed_employees as $data):
                $employee = $data['post'];
                $photo_data = $data['photo_data'];
                $full_name = get_field('full_name', $employee->ID);
                $position = get_field('position', $employee->ID);
                $department = get_field('department', $employee->ID);
                $hire_date = get_field('hire_date', $employee->ID);
                $status = get_field('status', $employee->ID);
            ?>
                <tr>
                    <td>
                        <?php if (!empty($photo_data)): ?>
                            <img src="<?php echo $photo_data; ?>" alt="" class="em-photo" width="40" height="40">
                        <?php else: ?>
                            <div class="em-photo-placeholder">Нет фото</div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($full_name); ?></td>
                    <td><?php echo esc_html($position); ?></td>
                    <td><?php echo esc_html($departments[$department] ?? $department); ?></td>
                    <td><?php echo esc_html($hire_date); ?></td>
                    <td><?php echo esc_html($statuses[$status] ?? $status); ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">Сотрудники не найдены</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
