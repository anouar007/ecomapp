<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <style>
        @font-face {
            font-family: 'Amiri';
            src: url('<?php echo e(public_path('fonts/amiri-regular.ttf')); ?>') format('truetype');
        }
        body {
            font-family: 'Amiri', sans-serif;
            font-size: 12px;
            color: #333;
            direction: rtl;
            text-align: right;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #4e73df;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        .table th, .table td {
            padding: 0.5rem;
            border: 1px solid #e3e6f0;
            text-align: right;
        }
        .table th {
            background-color: #f8f9fc;
            font-weight: bold;
            color: #4e73df;
        }
        .text-end {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .bg-primary {
            background-color: #4e73df;
            color: white;
        }
        .bg-light {
            background-color: #f8f9fc;
        }
        .section-header td {
            background-color: #eaecf4;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #858796;
            border-top: 1px solid #e3e6f0;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body>
    <div class="header">
        <h1><?php echo e(config('app.name', 'Speed')); ?></h1>
        <p><?php echo $__env->yieldContent('subtitle'); ?></p>
    </div>

    <?php echo $__env->yieldContent('content'); ?>

    <div class="footer">
        <?php echo e(__('Generated on')); ?> <?php echo e(date('Y-m-d H:i')); ?> | <?php echo e(__('Page')); ?> <span class="page-number"></span>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $x = 520;
            $y = 820;
            $text = "<?php echo e(__('Page')); ?> {PAGE_NUM} <?php echo e(__('of')); ?> {PAGE_COUNT}";
            $font = null;
            $size = 10;
            $color = array(0.5, 0.5, 0.5);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/layouts/pdf.blade.php ENDPATH**/ ?>