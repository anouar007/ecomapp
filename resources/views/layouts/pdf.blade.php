<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title')</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
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
        }
        .table th {
            background-color: #f8f9fc;
            font-weight: bold;
            color: #4e73df;
            text-align: left;
        }
        .text-end {
            text-align: right;
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
    @yield('styles')
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Speed') }}</h1>
        <p>@yield('subtitle')</p>
    </div>

    @yield('content')

    <div class="footer">
        Generated on {{ date('Y-m-d H:i') }} | Page <span class="page-number"></span>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $x = 520;
            $y = 820;
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
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
