<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragmen Dinamis MVC</title>
    <link rel="stylesheet" href="style.css">
    <?php if ($is_mobile_device): ?>
        <script>
            // Deklarasikan variabel global yang bisa diakses oleh script.js
            window.appConfig = {
                scrollSpeedMs: <?php echo $scroll_speed_ms; ?>,
                minTimePerFragmentMs: <?php echo $min_time_per_fragment_ms; ?>
            };
        </script>
        <script src="script.js"></script>
    <?php endif; ?>
</head>
<body>

    <?php if ($is_mobile_device): ?>
        <div id="transparent-control"></div>
    <?php endif; ?>

    <?php
    if (!empty($posts)):
        $fragment_colors = ['#FF5733', '#33FF57', '#3357FF', '#FFB733', '#33FFB7', '#B733FF'];
        foreach ($posts as $index => $post):
            $color_index = $index % count($fragment_colors);
            $background_color = $fragment_colors[$color_index];
            $id_name = "fragment" . ($index + 1);
    ?>
            <section id="<?php echo $id_name; ?>" class="fragment" style="background-color: <?php echo $background_color; ?>;">
                <div class="content">
                    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                </div>
            </section>
    <?php
        endforeach;
    else:
    ?>
        <section id="fragment1" class="fragment" style="background-color: #555;">
            <div class="content">
                <h1>Tidak Ada Postingan</h1>
                <p>Silakan tambahkan data ke tabel 'posts' di database Anda.</p>
            </div>
        </section>
    <?php
    endif;
    ?>

</body>
</html>