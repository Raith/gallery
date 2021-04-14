<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <script src="https://code.jquery.com/jquery-latest.min.js"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

    <title>Image gallery</title>
    <link rel="icon" type="image/png" href="/favicon.png">
</head>

<body>
    <div id="divToFocus" border></div>
    <div class="container mt-3 mb-3">
        <div class="row align-items-center text-center">
            <div class="col-md-6 col-sx-12 pt-1 pb-1">
                <h1 class="text-primary fw-bold">Image Gallery</h1>
            </div>
            <div class="col-md-4 col-sx-12 pt-1 pb-1 text-end">
                <div class="input-group">
                    <input type="file" accept="image/*" class="form-control" aria-label="Select image" id="imageSelect" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-trigger="hover" title="Maximum image size: 10MB." onmouseleave=blurOnMouseLeave(this)>
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" onmouseleave=blurOnMouseLeave(this)>Upload</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" onClick=DirectUpload() data-bs-toggle="tooltip" data-bs-placement="left" data-bs-trigger="hover" title="File is uploaded to the server with a new unique name.">Direct</a></li>
                    <li><a class="dropdown-item" onClick=imgurAPIUpload() data-bs-toggle="tooltip" data-bs-placement="left" data-bs-trigger="hover" title="File is uploaded to Imgur.com and accessed via unique link.">Imgur API</a></li>
                    </ul>
                </div>
            </div>
            <div class="d-flex flex-sm-row flex-md-column col-md-2 col-sx-12 justify-content-around">
                <div class="form-check form-switch" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-trigger="hover" title="Toggle between infinite scroll and pagination. Default: On.">
                    <input class="form-check-input" type="checkbox" id="switchInfiniteToggle" onChange=switchInfiniteScroll() checked onmouseleave=blurOnMouseLeave(this)>
                    <label class="form-check-label" for="switchInfiniteToggle" onmouseleave=blurTargetOnMouseLeave(`switchInfiniteToggle`)>Infinite</label>
                </div>
                <div class="form-check form-switch" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-trigger="hover" title="Allows switching between two layouts: (1) images stacked closer together or (2) evenly distributed image frames. Default: Stacked.">
                    <input class="form-check-input" type="checkbox" id="switchLayoutToggle" onChange=switchLayout() checked onmouseleave=blurOnMouseLeave(this)>
                    <label class="form-check-label" for="switchLayoutToggle" onmouseleave=blurTargetOnMouseLeave(`switchLayoutToggle`)>Layout</label>
                </div>
            </div>
        </div>
    </div>

    <div class="container d-flex justify-content-center mb-2 mt-2" id="navTop">
        
    </div>

    <div class="container" id="gallery">
    </div>
    
    <div class="container d-flex justify-content-center mb-2 mt-2" id="navBot">
            
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>

    <?php
        $localImgFolder = array_filter(scandir("img"), function ($item) {
            return !is_dir("img/" . $item);
        });
        $localImgFiles = json_encode(implode(";", $localImgFolder));

        $imgurFile = "upload/imgur";
        if (!file_exists($imgurFile)) {
            touch($imgurFile);
        }
        $imgurImages = json_encode(file($imgurFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    ?>
    
    <script>
        //enable all bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new window.bootstrap.Tooltip(tooltipTriggerEl)
        });

        //disable tooltips for devices with touchscreens that do not have a secondary hover-capable pointing devices
        if (window.matchMedia("(any-hover: none)").matches) {
            tooltipList.forEach(tooltip => {
                tooltip.disable();
            })
        }
    </script>

    <script>
        var localImageFiles = <?php echo $localImgFiles; ?>;
        var imgurImages = <?php echo $imgurImages; ?>;

        var gallery = document.getElementById("gallery");

        var imageArray = [];

        var currentPage = 1;
        var infiniteStartPage = currentPage;

        const imagesPerPage = 20;
        var rowsAdded = false;
        var layoutFlag = true;
        var infiniteFlag = true;

        document.body.addEventListener("load", loadPageContent());
        document.addEventListener("scroll", checkScrollPos);

        function blurOnMouseLeave(element) {
            element.blur();
        }

        function blurTargetOnMouseLeave(elementID) {
            document.getElementById(elementID).blur();
        }

        function updatePagination() {
            if(!infiniteFlag) {
                let info = currentPage + ' / ' + getMaxPages();

                document.getElementById("paginationTop").innerHTML = info;
                document.getElementById("paginationBot").innerHTML = info;
            }
        }

        function getMaxPages() {
            return (Math.ceil(imageArray.length / imagesPerPage));
        }

        function scrollPageTop() {
            document.getElementById("divToFocus").scrollIntoView();
        }

        function nextPageClick(scroll) {
            if (currentPage+1 <= Math.ceil(imageArray.length / imagesPerPage)) {
                currentPage++;
                switchLayout();
                if (scroll) 
                    scrollPageTop();
            }
        }

        function previousPageClick(scroll) {
            if (currentPage > 1) {
                currentPage--;
                switchLayout();
                if (scroll) 
                    scrollPageTop();
            }
        }

        function switchInfiniteScroll() {
            infiniteFlag = document.getElementById("switchInfiniteToggle").checked;

            if (infiniteFlag) {
                document.getElementById("navTop").innerHTML = "";
                document.getElementById("navBot").innerHTML = "";
                infiniteStartPage = currentPage;
            } else {
                document.getElementById("navTop").innerHTML = '<div class="row align-items-center text-center"><div class="col-4"><button class="btn btn-primary btn-sm" type="button" onClick=previousPageClick(false) onmouseleave=blurOnMouseLeave(this)><<</button></div><div class="col-4"><label class="text-primary fw-bold" id="paginationTop"> </label></div><div class="col-4"><button class="btn btn-primary btn-sm" type="button" onClick=nextPageClick(false) onmouseleave=blurOnMouseLeave(this)>>></button></div></div>';
                document.getElementById("navBot").innerHTML = '<div class="row align-items-center text-center"><div class="col-4"><button class="btn btn-primary btn-sm" type="button" onClick=previousPageClick(true) onmouseleave=blurOnMouseLeave(this)><<</button></div><div class="col-4"><label class="text-primary fw-bold" id="paginationBot"> </label></div><div class="col-4"><button class="btn btn-primary btn-sm" type="button" onClick=nextPageClick(true) onmouseleave=blurOnMouseLeave(this)>>></button></div></div>';
                currentPage = infiniteStartPage;
                switchLayout();
            }
        }
        
        function DirectUpload() {
            imageInputField = document.getElementById("imageSelect");
            imageFile = imageInputField.files;

            if (imageFile.length) {

                const maxImgSize = 10000;
                
                if (imageFile[0].size > (1024 * maxImgSize)) {
                    return false;
                } 

                let formData = new FormData();
                formData.append("image", imageFile[0]);

                let params = {
                    method: "POST",
                    body: formData
                }

                let url = "directImageUpload.php";
                let newImage;

                fetch(url, params)
                .then(result => {
                    if (!result.ok) throw Error(result.statusText);

                    return result;
                })
                .then(response => response.json())
                .then(data => {
                    let separator = ""
                    if (localImageFiles !== "") {
                        separator = ";";
                    }
                    localImageFiles = localImageFiles.concat(separator,data);
                    switchLayout();
                })
                .catch(error => console.error("Error: ", error));
            }

            imageInputField.value = "";
        }

        function imgurAPIUpload() {
            imageInputField = document.getElementById("imageSelect");
            imageFile = imageInputField.files;

            if (imageFile.length) {

                const maxImgSize = 10000;
                
                if (imageFile[0].size > (1024 * maxImgSize)) {
                    return false;
                }

                const imgurUploadEndpoint = "https://api.imgur.com/3/image";
                const clientID = "e667ddbf327b742"; //does not expire
                const accessToken = "19b1bffa9c0e75f150c5adf65aea0900824558d5"; //will expire on about 2021/05/10, token refresh required afterwards

                let formData = new FormData();
                formData.append("image", imageFile[0]);
                formData.append("album", "hOV1dlg"); //cannot be used with anonymous upload

                let params = {
                    method: "POST",
                    headers: {
                        //"Authorization": "ClientID " + clientID //anonymous upload
                        "Authorization": "Bearer " + accessToken //authorized upload
                    },
                    body: formData
                }

                fetch(imgurUploadEndpoint, params)
                .then(result => {
                    if (!result.ok) throw Error(result.statusText);

                    return result;
                })
                .then(response => response.json())
                .then(data => {
                    let separator = "";
                    if (localImageFiles !== "") {
                        separator = ";";
                    }
                    localImageFiles = localImageFiles.concat(separator,data.data.link);
                    switchLayout();

                    let imgData = {
                        link: data.data.link,
                        deletehash: data.data.deletehash
                    }

                    let params = {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json;charset=utf-8"
                        },
                        body: JSON.stringify(imgData)
                    }
 
                    url = "saveImageData.php";
 
                    fetch(url, params)
                    .then(result => {
                        if (!result.ok) throw Error(result.statusText);

                        return result;
                    })
                    .catch(error => console.error("Error: ", error));
                })
                .catch(error => console.error("Error: ", error));
            }

            imageInputField.value = "";
        }

        function prepareImages() {
            if (localImageFiles != "") {
                imageArray = localImageFiles.split(";");
            }

            imgurImages.forEach(img => {
                if (img != "{}" && img != "null" && img)
                    imageArray.push(JSON.parse(img).link);
            })

            imageArray.sort(function(a,b) {

                let _a = a.split("imgur.com/").pop();
                let _b = b.split("imgur.com/").pop();
            
                if (_a.toLowerCase() < _b.toLowerCase()) return -1;
                if (_a.toLowerCase() > _b.toLowerCase()) return 1;
                return 0;
            });
        }

        function switchLayout() {
            gallery.innerHTML = "";
            rowsAdded = false;
  
            loadPageContent();
        }

        function loadPageContent() {
            layoutFlag = document.getElementById("switchLayoutToggle").checked;
            
            prepareImages();

            updatePagination();
            
            if (infiniteFlag) {
                currentPage = infiniteStartPage;
            }

            let next = (currentPage-1)*imagesPerPage;
            if (next < 0)
                next = 0;

            if (layoutFlag)
                addImagesV1(next);
            else
                addImagesV2(next);
        }

        //stacking images one behind another in each column
        function addImagesV1(nextImageID) {
            if (!rowsAdded) {
                rowsAdded = true;

                gallery.innerHTML += '<div class="row" id="row"></div>';           

                let row = document.getElementById("row");

                row.innerHTML += '<div class="col-6" id="leftColumn" </div>';
                row.innerHTML += '<div class="col-6" id="rightColumn" </div>';
            }

            let left = document.getElementById("leftColumn");
            let right = document.getElementById("rightColumn");
            
            let imageIndexes;

            if (nextImageID+imagesPerPage > imageArray.length) {
                imageIndexes = [...Array(imageArray.length).keys()].slice(nextImageID);
            } else {
                imageIndexes = [...Array(nextImageID+imagesPerPage).keys()].slice(nextImageID);
            }

            let columnSwitch = 0;

            imageIndexes.forEach( index => {
                let img = imageArray[index];
                let imgPath;
                let caption = imageArray[index].split("imgur.com/").pop();

                if (img.includes("https://i.imgur.com/")) {
                    imgPath = img;    
                } else {
                    imgPath = "img/"+img;
                }

                let insert = '<figure class="figure w-100"><img class="figure-img img-fluid rounded w-100" src="'+imgPath+'"><figcaption class="figure-caption text-center">File: '+caption+'</figcaption></figure>';

                if (columnSwitch % 2 == 0) {
                    left.innerHTML += insert;
                } else {
                    right.innerHTML += insert;
                }

                columnSwitch++;
            });
        }

        //separate implementation for even image alignment in both columns
        function addImagesV2(nextImageID) {
            let columnSwitch = 0;

            let insert = '';

            for (let index in imageArray) {
                if (index >= nextImageID) {
                    let img = imageArray[index];
                    let imgPath;
                    let caption = imageArray[index].split("imgur.com/").pop();

                    if (img.includes("https://i.imgur.com/")) {
                        imgPath = img;    
                    } else {
                        imgPath = "img/"+img;
                    }

                    if (columnSwitch % 2 == 0) {
                        insert += '<div class="row"> <div class="d-flex align-items-center col-6"> <figure class="figure w-100"><img class="figure-img img-fluid rounded w-100" src="'+imgPath+'"><figcaption class="figure-caption text-center">File: '+caption+'</figcaption></figure> </div>';
                    } else {
                        insert += '<div class="d-flex align-items-center col-6"> <figure class="figure w-100"><img class="figure-img img-fluid rounded w-100" src="'+imgPath+'"><figcaption class="figure-caption text-center">File: '+caption+'</figcaption></figure> </div> </div>';
                    }

                    columnSwitch++;

                    if (nextImageID+columnSwitch > (currentPage*imagesPerPage)-1) {
                        break;
                    }
                }
            }

            gallery.innerHTML += insert;
        }

        function checkScrollPos() {
            if (!infiniteFlag) {
                return;
            }

            const weirdOffset = 16;

            let currentPos = document.documentElement.scrollTop / (document.body.clientHeight-window.innerHeight+weirdOffset);

            if (currentPos > 0.9) {
                if (currentPage < getMaxPages()) {

                    let nextImageID = currentPage*imagesPerPage;
                    
                    if (nextImageID >= imageArray.length) {
                        return;
                    }

                    currentPage++;

                    if (layoutFlag)
                        addImagesV1(nextImageID);
                    else
                        addImagesV2(nextImageID);
                }
            }
        }
    </script>
</body>
</html>
