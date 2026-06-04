<!DOCTYPE html>
<html>

<head>
    <style>
        .modal-header {
            border-bottom: 2px solid #007bff; /* Menambahkan garis bawah pada header */
        }

        .modal-body {
            font-family: 'Arial', sans-serif; /* Mengubah font */
        }

        .table th, .table td {
            text-align: center; /* Menyelaraskan teks di tengah */
        }
    </style>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo e($setting->nama_perusahaan); ?> | <?php echo $__env->yieldContent('title'); ?></title>
    <script>
        window.baseUrl = <?php echo json_encode(url('/'), 15, 512) ?>;
    </script>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="icon" href="<?php echo e(url('$setting->path_logo')); ?>" type="image/x-icon">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet"
        href="<?php echo e(asset('AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css')); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="<?php echo e(asset('AdminLTE-2/bower_components/font-awesome/css/font-awesome.min.css')); ?>">
    <!-- Ionicons -->
    <link rel="stylesheet"
        href="<?php echo e(asset('AdminLTE-2/bower_components/Ionicons/css/ionicons.min.css')); ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo e(asset('AdminLTE-2/dist/css/AdminLTE.min.css')); ?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo e(asset('AdminLTE-2/dist/css/skins/_all-skins.min.css')); ?>">
    <!-- Morris chart -->
    <link rel="stylesheet"
        href="<?php echo e(asset('AdminLTE-2/bower_components/morris.js/morris.css')); ?>">
    <!-- jvectormap -->
    <link rel="stylesheet"
        href="<?php echo e(asset('AdminLTE-2/bower_components/jvectormap/jquery-jvectormap.css')); ?>">
    <!-- Date Picker -->
    <link rel="stylesheet"
        href="<?php echo e(asset('AdminLTE-2/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')); ?>">
    <!-- Daterange picker -->
    <link rel="stylesheet"
        href="<?php echo e(asset('AdminLTE-2/bower_components/bootstrap-daterangepicker/daterangepicker.css')); ?>">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet"
        href="<?php echo e(asset('AdminLTE-2/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')); ?>">
    <!-- DataTables -->
    <link rel="stylesheet"
        href="<?php echo e(asset('AdminLTE-2/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')); ?>">
    <!-- Di head -->
<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<!-- Tambahkan di <head> -->
<link href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- CSS Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- jQuery harus dimuat duluan -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Kemudian Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <?php echo $__env->yieldPushContent('css'); ?>
</head>

<body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">

        <?php if ($__env->exists('header')) echo $__env->make('header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!-- Left side column. contains the logo and sidebar -->
            <?php if ($__env->exists('sidebar')) echo $__env->make('sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <!-- Content Wrapper. Contains page content -->
                <div class="content-wrapper">
                    <!-- Content Header (Page header) -->
                    <section class="content-header">
                        <h1>
                            <?php echo $__env->yieldContent('title'); ?>
                        </h1>
                        <ol class="breadcrumb">
                            <?php $__env->startSection('breadcrumb'); ?>
                            <li><a href="<?php echo e(url('/')); ?>"><i class="fa fa-dashboard"></i> Home</a>
                            </li>
                            <?php echo $__env->yieldSection(); ?>
                        </ol>
                    </section>

                    <!-- Main content -->
                    <section class="content">
                        <?php echo $__env->yieldContent('content'); ?>

                    </section>
                    <!-- /.content -->
                </div>
                <!-- /.content-wrapper -->
                <?php if ($__env->exists('footer')) echo $__env->make('footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
    <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <!-- <script src="<?php echo e(asset('AdminLTE-2/bower_components/jquery/dist/jquery.min.js')); ?>"> -->
    </script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo e(asset('AdminLTE-2/bower_components/jquery-ui/jquery-ui.min.js')); ?>">
    </script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.7 -->
    <script
        src="<?php echo e(asset('AdminLTE-2/bower_components/bootstrap/dist/js/bootstrap.min.js')); ?>">
    </script>
    <!-- Morris.js charts -->
    <script src="<?php echo e(asset('AdminLTE-2/bower_components/raphael/raphael.min.js')); ?>"></script>
    <script src="<?php echo e(asset('AdminLTE-2/bower_components/morris.js/morris.min.js')); ?>"></script>
    <!-- Sparkline -->
    <script
        src="<?php echo e(asset('AdminLTE-2/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')); ?>">
    </script>
    <!-- jvectormap -->
    <script src="<?php echo e(asset('AdminLTE-2/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')); ?>">
    </script>
    <script
        src="<?php echo e(asset('AdminLTE-2/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')); ?>">
    </script>
    <!-- jQuery Knob Chart -->
    <script
        src="<?php echo e(asset('AdminLTE-2/bower_components/jquery-knob/dist/jquery.knob.min.js')); ?>">
    </script>
    <!-- daterangepicker -->
    <script src="<?php echo e(asset('AdminLTE-2/bower_components/moment/min/moment.min.js')); ?>">
    </script>
    <script
        src="<?php echo e(asset('AdminLTE-2/bower_components/bootstrap-daterangepicker/daterangepicker.js')); ?>">
    </script>
    <!-- datepicker -->
    <script
        src="<?php echo e(asset('AdminLTE-2/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')); ?>">
    </script>
    <!-- Bootstrap WYSIHTML5 -->
    <script
        src="<?php echo e(asset('AdminLTE-2/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')); ?>">
    </script>
    <!-- Slimscroll -->
    <script
        src="<?php echo e(asset('AdminLTE-2/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')); ?>">
    </script>
    <!-- FastClick -->
    <script src="<?php echo e(asset('AdminLTE-2/bower_components/fastclick/lib/fastclick.js')); ?>">
    </script>
    <!-- ChartJS -->
    <script src="<?php echo e(asset('AdminLTE-2/bower_components/Chart.js/Chart.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- DataTables -->
    <script
        src="<?php echo e(asset('AdminLTE-2/bower_components/datatables.net/js/jquery.dataTables.min.js')); ?>">
    </script>
    <script
        src="<?php echo e(asset('AdminLTE-2/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')); ?>">
    </script>
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('AdminLTE-2/dist/js/adminlte.min.js')); ?>"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?php echo e(asset('AdminLTE-2/dist/js/pages/dashboard.js')); ?>"></script>
    
    <!--validator-->
    <script src="<?php echo e(asset('js/validator.min.js')); ?>"></script>

    <script>
        function preview(selector, temporaryFile, width = 200)  {
            $(selector).empty();
            $(selector).append(`<img src="${window.URL.createObjectURL(temporaryFile)}" width="${width}">`);
        }
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\app.blade.php ENDPATH**/ ?>