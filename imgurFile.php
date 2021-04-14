<!doctype html>
<html lang="en">
<head>
    <title>Imgur File</title>
    <link rel="icon" type="image/png" href="/favicon.png">
</head>

<body>
    <?php
        $imgurFile = "upload/imgur";
        if (!file_exists($imgurFile)) {
            touch($imgurFile);
        }
        $imgurImages = json_encode(file($imgurFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    ?>

    <h1>List of images saved to Imgur.com</h1>

    <script>
        var imgurImages = <?php echo $imgurImages; ?>;
        var imageArray = [];

        imgurImages.forEach(img => {
            if (img != "{}" && img != "null" && img) {
                let link = JSON.parse(img).link;
                let deletehash = JSON.parse(img).deletehash;
                document.body.innerHTML += "<a href ='"+link+"'>"+link+"</a> (deletehash: "+ JSON.parse(img).deletehash+")</br>";
            }
        });

    </script> 
</body>
</html>
